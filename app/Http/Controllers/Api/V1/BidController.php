<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBidRequest;
use App\Http\Resources\BidResource;
use App\Models\Bid;
use App\Models\Tender;
use Illuminate\Http\Request;

class BidController extends Controller
{
    public function store(StoreBidRequest $request)
    {
        $tender = Tender::find($request->tender_id);

        if ($tender->status !== 'open') {
            return response()->json([
                'success'   => false,
                'message'   => 'Gagal submit. Lelang ini sudah ditutup.'
            ], 403);
        }

        $existingBid = Bid::where('tender_id', $tender->id)
                        ->where('vendor_id', $request->vendor_id)
                        ->exists();

        if ($existingBid) {
            return response()->json([
                'success'   => false,
                'message'   => 'Vendor Anda sudah pernah mengajukan penawaran pada lelang ini.'
            ], 422);
        }

        $documentPath = null;
        if ($request->hasFile('bid_document')) {
            $file = $request->file('bid_document');
            $documentPath = $file->store('bids', 'public');
        }

        $bid = Bid::create([
            'tender_id'         => $tender->id,
            'vendor_id'         => $request->vendor_id,
            'offered_price'     => $request->offered_price,
            'bid_document_path' => $documentPath,
            'is_winner'         => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Penawaran dan dokumen berhasil disubmit!',
            'data'    => new BidResource($bid->load(['tender', 'vendor']))
        ], 201);
    }
}
