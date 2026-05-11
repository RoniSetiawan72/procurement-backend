<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'item_id'               => $this->item_id,
            'item_name'             => $this->item_name,
            'specs'                 => $this->specs,
            'quantity'              => $this->quantity,
            'uom'                   => $this->uom,
            'estimated_unit_price'  => $this->estimated_unit_price,
            'subtotal'              => $this->quantity * $this->estimated_unit_price,
        ];
    }
}
