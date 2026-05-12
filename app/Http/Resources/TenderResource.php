<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'tender_number'     => $this->tender_number,
            'pr_number'         => $this->purchaseRequisition?->pr_number,
            'creator'           => $this->creator?->name,
            'title'             => $this->title,
            'description'       => $this->description,
            'start_date'        => $this->start_date?->format('Y-m-d H:i'),
            'end_date'          => $this->end_date?->format('Y-m-d H:i'),
            'status'            => $this->status,
            'created_at'        => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
