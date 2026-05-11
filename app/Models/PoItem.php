<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'item_name',
        'quantity',
        'uom',
        'actual_unit_price',
    ];

    protected $casts = [
        'actual_unit_price' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
