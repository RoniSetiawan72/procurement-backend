<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'po_number'                 => $this->po_number,
            'pr_number'                 => $this->purchaseRequisition?->pr_number,
            'vendor_id'                 => $this->vendor_id,
            'vendor_name'               => $this->vendor?->name,
            'expected_delivery_date'    => $this->expected_delivery_date?->format('Y-m-d'),
            'notes'                     => $this->notes,
            'actual_total_cost'         => $this->actual_total_cost,
            'status'                    => $this->status,
            'items'                     => PoItemResource::collection($this->whenLoaded('items')),
            'created_at'                => $this->created_at?->format('d-m-Y H:i'),
            'updated_at'                => $this->updated_at?->format('d-m-Y H:i'),
        ];
    }
}
