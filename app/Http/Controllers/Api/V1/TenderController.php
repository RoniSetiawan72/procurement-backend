<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTenderRequest;
use App\Http\Requests\UpdateTenderRequest;
use App\Http\Resources\TenderResource;
use App\Models\PurchaseRequisition;
use App\Models\Tender;
use Illuminate\Http\Request;
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
}
