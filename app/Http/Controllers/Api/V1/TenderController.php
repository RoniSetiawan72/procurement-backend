<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTenderRequest;
use App\Http\Requests\UpdateTenderRequest;
use App\Http\Resources\TenderResource;
use App\Models\Bid;
use App\Models\Budget;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenderController extends Controller
{
    public function index(Request $request)
    {
        $query = Tender::with(['purchaseRequisition', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tenders = $query->latest()->paginate(10);

        return TenderResource::collection($tenders);
    }

    public function store(StoreTenderRequest $request)
    {
        $pr = PurchaseRequisition::find($request->purchase_requisition_id);

        if ($pr->status !== 'approved') {
            return response()->json([
                'success'   => false,
                'message'   => 'Hanya PR berstatus Approved yang dapat di lelang.'
            ], 403);
        }

        $existingTender = Tender::where('purchase_requisition_id', $pr->id)
                                ->whereIn('status', ['open', 'closed'])
                                ->exists();

        if ($existingTender) {
            return response()->json([
                'success'   => false,
                'message'   => 'PR ini sudah memiliki proses Tender yang sedang berjalan.'
            ], 422);
        }

        $tender = Tender::create([
            'tender_number'           => 'TND-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'purchase_requisition_id' => $pr->id,
            'user_id'                 => auth()->id(),
            'title'                   => $request->title,
            'description'             => $request->description,
            'start_date'              => $request->start_date,
            'end_date'                => $request->end_date,
            'status'                  => 'open',
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Tender berhasil dibuka.',
            'data'      => new TenderResource($tender->load(['purchaseRequisition', 'creator']))
        ], 201);
    }

    public function update(UpdateTenderRequest $request, Tender $tender)
    {
        if ($tender->status !== 'open') {
            return response()->json([
                'success'   => false,
                'message'   => 'Tender tidak dapat doibah karena statusnya sudah tidak Open.'
            ], 403);
        }

        $tender->update($request->validated());

        return response()->json([
            'success'   => true,
            'message'   => 'Tender berhasil diperbarui.',
            'data'      => new TenderResource($tender->load(['purchaseRequisition', 'creator']))
        ]);
    }

    public function selectWinner(Request $request, Tender $tender)
    {
        $request->validate([
            'bid_id'    => 'required|exists:bids,id'
        ]);

        if ($tender->status === 'completed') {
            return response()->json([
                'success'   => false,
                'message'   => 'Lelang ini sudah ditutup dan pemenang sebelumnya sudah ditetapkan.'
            ], 403);
        }

        $bid = Bid::where('id', $request->bid_id)
                ->where('tender_id', $tender->id)
                ->firstOrFail();

        try {
            DB::beginTransaction();

            $tender->update(['status' => 'completed']);
            $bid->update(['is_winner' => true]);

            $pr = $tender->purchaseRequisition;

            if (!PurchaseOrder::where('purchase_requisition_id', $pr->id)->exists()) {
                $po = PurchaseOrder::create([
                    'po_number'               => 'PO-' . date('Ymd') . '-AUTO',
                    'purchase_requisition_id' => $pr->id,
                    'vendor_id'               => $bid->vendor_id,
                    'user_id'                 => auth()->id(),
                    'expected_delivery_date'  => now()->addDays(14),
                    'notes'                   => 'PO Dihasilkan otomatis dari pemenang Lelang: ' . $tender->tender_number,
                    'status'                  => 'pending',
                    'actual_total_cost'       => $bid->offered_price,
                ]);

                foreach ($pr->items as $prItem) {
                    $po->items()->create([
                        'item_id'           => null,
                        'item_name'         => $prItem->item_name,
                        'quantity'          => $prItem->quantity,
                        'uom'               => $prItem->uom,
                        'actual_unit_price' => $prItem->estimated_unit_price,
                    ]);
                }

                $budget = Budget::where('department_id', $pr->department_id)
                                ->where('fiscal_year', date('Y'))
                                ->first();

                $estimatedCost = $pr->estimated_total_cost;
                $costDiff = $bid->offered_price - $estimatedCost;

                if ($costDiff > 0) {
                    $availBudget = $budget->total_amount - ($budget->used_amount + $budget->reserved_amount);
                    if ($availBudget < $costDiff) {
                        throw new \Exception("Anggaran tidak mencukupi untuk menetapkan pemenang ini. Dana kurang: Rp " . number_format($costDiff - $availBudget, 0, ',', '.'));
                    }
                    $budget->increment('used_amount', $costDiff);
                } elseif ($costDiff < 0) {
                    $budget->decrement('used_amount', abs($costDiff));
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Luar biasa! Pemenang lelang berhasil ditetapkan, PO otomatis dibuat, dan anggaran telah disesuaikan.',
                'data_tender' => $tender,
                'pemenang' => $bid
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
