<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BidResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'tender_title'  => $this->tender?->title,
            'vendor_name'   => $this->vendor?->name,
            'offered_price' => $this->offered_price,
            'is_winner'     => $this->is_winner,
            'document_url'  => $this->bid_document_path ? url(Storage::url($this->bid_document_path)) : null,
            'submitted_at'  => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
