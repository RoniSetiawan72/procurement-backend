<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
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
            'department_id'     => $this->department_id,

            'department_name'   => $this->department?->name,
            'department_code'   => $this->department?->code,

            'fiscal_year'       => $this->fiscal_year,
            'total_amount'      => $this->total_amount,
            'used_amount'       => $this->used_amount,
            'reserved_amount'   => $this->reserced_amount,
            'created_at'        => $this->created_at?->format('d-m-Y H:i'),
            'updated_at'        => $this->updated_at?->format('d-m-Y H:i'),

        ];
    }
}
