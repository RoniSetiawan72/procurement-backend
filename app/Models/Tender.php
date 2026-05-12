<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    protected $fillable = [
        'tender_number',
        'purchase_requisition_id',
        'user_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date'    => 'datetime',
        'end_date'      => 'datetime'
    ];

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    public function workFlowLogs()
    {
        return $this->morphMany(WorkFlowLog::class, 'auditable');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
