<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'item_code'         => $this->item_code,
            'name'              => $this->name,
            'category'          => $this->category,
            'uom'               => $this->uom,
            'estimated_price'   => $this->estimated_price,
            'created_at'        => $this->created_at?->format('d-m-Y H:i'),
            'updated_at'        => $this->updated_at?->format('d-m-Y H:i'),
        ];
    }
}
