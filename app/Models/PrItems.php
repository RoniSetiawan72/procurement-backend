<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrItems extends Model
{
    protected $fillable = [
        'purchase_requisition_id',
        'item_name',
        'specs',
        'quantity',
        'uom',
        'estimated_unit_price'
    ];

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }
}
