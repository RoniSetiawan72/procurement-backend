<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseRequisitionResource extends JsonResource
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
            'pr_number'                 => $this->pr_number,
            'title'                     => $this->title,
            'description'               => $this->description,
            'status'                    => $this->status,
            'estimated_total_cost'      => $this->estimated_total_cost,
            'department'                => $this->department?->name,
            'requester'                 => $this->requester?->name,
            'items'                     => PrItemResource::collection($this->whenLoaded('items')),
            'created_at'                => $this->created_at?->format('d-m-Y H:i'),
            'updated_at'                => $this->updated_at?->format('d-m-Y H:i'),
            'approved_at'               => $this->approved_at?->format('d-m-Y H:i'),
        ];
    }
}
