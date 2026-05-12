<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePoRequest;
use App\Http\Requests\UpdatePoRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Mail\PurchaseOrderMail;
use App\Models\Budget;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index()
    {

    }

    public function store(StorePoRequest $request)
    {
        $pr = PurchaseRequisition::find($request->purchase_requisition_id);

        if ($pr->status !== 'approved') {
            return response()->json([
                'success'   => false,
                'message'   => 'Hanya Purchase Requisition berstatus Approved yang dapat menjadi PO.'
            ], 403);
        }

        $existingPo = PurchaseOrder::where('purchase_requisition_id', $pr->id)->exists();
        if ($existingPo) {
            return response()->json([
                'success'   => false,
                'message'   => 'Purchase Requisition ini sudah memiliki dokumen PO yang aktif.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $po = PurchaseOrder::create([
                'po_number'               => 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'purchase_requisition_id' => $pr->id,
                'vendor_id'               => $request->vendor_id,
                'user_id'                 => $request->user()->id,
                'expected_delivery_date'  => $request->expected_delivery_date,
                'notes'                   => $request->notes,
                'status'                  => 'pending',
                'actual_total_cost'       => 0,
            ]);

            $actualTotalCost = 0;
            foreach ($request->items as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['actual_unit_price'];
                $actualTotalCost += $subtotal;

                $po->items()->create($itemData);
            }

            $po->update(['actual_total_cost' => $actualTotalCost]);

            $budget = Budget::where('department_id', $pr->department_id)
                            ->where('fiscal_year', date('Y'))
                            ->first();

            $estimatedCost = $pr->estimated_total_cost;
            $costDiff = $actualTotalCost - $estimatedCost;

            if ($costDiff > 0) {
                $availBudget = $budget->total_amount - ($budget->used_amount + $budget->reserved_amount);

                if ($availBudget < $costDiff) {
                    throw new \Exception("Anggaran tidak mencukupi untuk menutupi selisih harga Vendor. Kekurangan dana: Rp " . number_format($costDiff - $availBudget, 0, ',', '.'));
                }

                $budget->increment('used_amount', $costDiff);
            } elseif ($costDiff < 0) {
                $savings = abs($costDiff);
                $budget->decrement('used_amount', $savings);
            }

            DB::commit();

            return (new PurchaseOrderResource($po->load(['items', 'vendor', 'purchaseRequisition'])))
                ->additional([
                    'success' => true,
                    'message' => 'Purchase Order berhasil dibuat dan anggaran telah disesuaikan.'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdatePoRequest $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya PO berstatus Pending yang dapat diubah.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $oldActualCost = $purchaseOrder->actual_total_cost;

            $purchaseOrder->update([
                'vendor_id'              => $request->vendor_id,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes'                  => $request->notes,
            ]);

            $purchaseOrder->items()->delete();

            $newActualCost = 0;
            foreach ($request->items as $itemData) {
                $subTotal = $itemData['quantity'] * $itemData['actual_unit_price'];
                $newActualCost += $subTotal;

                $purchaseOrder->items()->create($itemData);
            }

            $purchaseOrder->update(['actual_total_cost' => $newActualCost]);
            $costDiff = $newActualCost - $oldActualCost;

            if ($costDiff != 0) {
                $pr = $purchaseOrder->purchaseRequisition;
                $budget = Budget::where('department_id', $pr->department_id)
                                ->where('fiscal_year', date('Y'))
                                ->first();

                if ($costDiff > 0) {
                    $availBudget = $budget->total_amount - ($budget->used_amount + $budget->reserved_amount);
                    if ($availBudget < $costDiff) {
                        throw new \Exception("Anggaran tidak cukup untuk menutupi kenaikan harga. Kekurangan: Rp " . number_format($$costDiff - $availBudget, 0, ',', '.'));
                    }

                    $budget->increment('used_amount', $costDiff);
                } else {
                    $savings = abs($costDiff);
                    $budget->decrement('used_amount', $savings);
                }
            }

            DB::commit();

            return (new PurchaseOrderResource($purchaseOrder->load(['items', 'vendor', 'purchaseRequisition'])))
                ->additional([
                    'success' => true,
                    'message' => 'Purchase Order berhasil diperbarui dan anggaran telah disesuaikan ulang.'
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function generatePdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['vendor', 'items', 'purchaseRequisition', 'creator']);
        $pdf = Pdf::loadView('pdf.purchase_order', ['po' => $purchaseOrder]);
        $pdf->setPaper('a4', 'potrait');
        $fileName = 'Surat_PO_' . $purchaseOrder->po_number . '.pdf';

        return $pdf->download($fileName);
    }

    public function markAsSent(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya PO berstatus Pending yang dapat ditandai sebagai Sent.'
            ], 403);
        }

        $purchaseOrder->update(['status' => 'sent']);

        Mail::to($purchaseOrder->vendor->email)->send(new PurchaseOrderMail($purchaseOrder));

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order berhasil ditandai sebagai Sent dan Email PDF telah dikirim ke Vendor.',
            'data' => new PurchaseOrderResource($purchaseOrder->load(['items', 'vendor', 'purchaseRequisition']))
        ]);
    }
}
