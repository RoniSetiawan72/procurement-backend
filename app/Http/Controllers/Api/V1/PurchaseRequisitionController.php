<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrRequest;
use App\Http\Requests\UpdatePrRequest;
use App\Http\Resources\PurchaseRequisitionResource;
use App\Models\Budget;
use App\Models\PrItems;
use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseRequisitionController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequisition::with(['department', 'requester']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'ilike', '%' . $searchTerm . '%')
                ->orWhere('title', 'ilike', '%' . $searchTerm . '%');
            });
        }

        $query->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 10);
        $prs = $query->paginate($perPage);

        return PurchaseRequisitionResource::collection($prs)->additional([
            'success' => true,
            'message' => 'Daftar Purchase Requisition berhasil diambil.'
        ]);
    }

    public function store(StorePrRequest $request)
    {
        $user = $request->user();

        try {
            DB::beginTransaction();

            $pr = PurchaseRequisition::create([
                'pr_number'         => 'PR-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'department_id'     => $user->department_id,
                'user_id'           => $user->id,
                'title'             => $request->title,
                'description'       => $request->description,
                'status'            => 'draft',
                'estimated_total_cost'  => 0
            ]);

            $totalCost = 0;

            foreach ($request->items as $itemData) {
                $subTotal = $itemData['quantity'] * $itemData['estimated_unit_price'];
                $totalCost += $subTotal;

                PrItems::create([
                    'purchase_requisition_id'   => $pr->id,
                    'item_id'                   => $itemData['item_id'] ?? null,
                    'item_name'                 => $itemData['item_name'],
                    'specs'                     => $itemData['specs'] ?? null,
                    'quantity'                  => $itemData['quantity'],
                    'uom'                       => $itemData['uom'],
                    'estimated_unit_price'      => $itemData['estimated_unit_price']
                ]);

            }
            $pr->update(['estimated_total_cost' => $totalCost]);

            DB::commit();

            return (new PurchaseRequisitionResource($pr->load(['items', 'department', 'requester'])))
            ->additional([
                'success' => true,
                'message' => 'Purchase Requisition berhasil dibuat.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success'   => false,
                'message'   => 'Terjadi kesalahan saat menyimpan PR.',
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdatePrRequest $request, PurchaseRequisition $purchaseRequisition)
    {
        if ($purchaseRequisition->status !== 'draft') {
            return response()->json([
                'success'   => false,
                'message'   => 'Hanya PR berstatus Draft yang dapat diubah.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $purchaseRequisition->update([
                'title'         => $request->title,
                'description'   => $request->description,
            ]);

            $purchaseRequisition->items()->delete();

            $totalCost = 0;
            foreach ($request->items as $itemData) {
                $totalCost += ($itemData['quantity'] * $itemData['estimated_unit_price']);
                $purchaseRequisition->items()->create($itemData);
            }

            $purchaseRequisition->update(['estimated_total_cost' => $totalCost]);

            DB::commit();

            return (new PurchaseRequisitionResource($purchaseRequisition->load(['items', 'department', 'requester'])))
            ->additional([
                'success' => true,
                'message' => 'Purchase Requisition berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function submit(PurchaseRequisition $purchaseRequisition)
    {
        if ($purchaseRequisition->status !== 'draft') {
            return response()->json([
                'success'   => false,
                'message'   => 'Gagal! Hanya PR berstatus Draft yang bisa di-submit.'
            ], 403);
        }

        $currentYear = date('Y');
        $budget = Budget::where('department_id', $purchaseRequisition->department_id)
                        -> where('fiscal_year', $currentYear)
                        ->first();

        if (!$budget) {
            return response()->json([
                'success'   => false,
                'message'   => "Anggaran departemen untuk tahun {$currentYear} belum diatur oleh Administrator."
            ], 404);
        }

        $availableBudget = $budget->total_amount - ($budget->used_amount + $budget->reserved_amount);

        if ($availableBudget < $purchaseRequisition->estimated_total_cost) {
            return response()->json([
                'success' => false,
                'message' => "Anggaran tidak mencukupi! Sisa anggaran: Rp " . number_format($availableBudget, 0, ',', '.') .
                            " | Total PR: Rp " . number_format($purchaseRequisition->estimated_total_cost, 0, ',', '.')
            ], 422);
        }

        try {
            DB::beginTransaction();
            $purchaseRequisition->update(['status' => 'submitted']);

            $budget->increment('reserved_amount', $purchaseRequisition->estimated_total_cost);

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'PR berhasil di-submit ke Manager. Dana telah berhasil  diblokir sementara.',
                'data'      => new PurchaseRequisitionResource($purchaseRequisition->load(['items', 'department', 'requester']))
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success'   => true,
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function approve(PurchaseRequisition $purchaseRequisition)
    {
        if ($purchaseRequisition->status !== 'submitted') {
            return response()->json([
                'success'   => false,
                'message'   => 'Hanya PR berstatus Submitted yang bisa di Approve.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $purchaseRequisition->update([
                'status'    => 'approved',
                'approved_by'   => auth()->id(),
                'approved_at'   => now()
            ]);

            $currentYear = date('Y');
            $budget = Budget::where('department_id', $purchaseRequisition->department_id)
                            ->where('fiscal_year', $currentYear)
                            ->first();

            $budget->decrement('reserved_amount', $purchaseRequisition->estimated_total_cost);
            $budget->increment('used_amount', $purchaseRequisition->estimated_total_cost);

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'PR berhasil disetujui. Dana resmi terpakai (Used).',
                'data'      => new PurchaseRequisitionResource($purchaseRequisition->load(['department', 'requester']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function reject(PurchaseRequisition $purchaseRequisition)
    {
        if ($purchaseRequisition->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya PR berstatus Submitted yang dapat di-Reject.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $purchaseRequisition->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $currentYear = date('Y');
            $budget = Budget::where('department_id', $purchaseRequisition->department_id)
                            ->where('fiscal_year', $currentYear)
                            ->first();

            $budget->decrement('reserved_amount', $purchaseRequisition->estimated_total_cost);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PR ditolak. Dana yang di-booking telah dikembalikan ke sisa anggaran.',
                'data' => new PurchaseRequisitionResource($purchaseRequisition->load(['department', 'requester']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
