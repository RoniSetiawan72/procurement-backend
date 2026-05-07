<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'tender_id',
        'vendor_id',
        'po_number',
        'total_amount',
        'pdf_path',
        'status',
        'notified_at'
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function workflowLogs()
    {
        return $this->morphMany(WorkFlowLog::class, 'auditable');
    }
}
