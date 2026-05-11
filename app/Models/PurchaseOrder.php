<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'purchase_requisition_id',
        'vendor_id',
        'user_id',
        'expected_delivery_date',
        'notes',
        'actual_total_cost',
        'status',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'actual_total_cost' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PoItem::class, 'purchase_order_id');
    }

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function workflowLogs()
    {
        return $this->morphMany(WorkFlowLog::class, 'auditable');
    }
}
