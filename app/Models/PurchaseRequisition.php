<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    protected $fillable = [
        'pr_number',
        'department_id',
        'user_id',
        'title',
        'description',
        'estimated_total_cost',
        'status',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'approved_at'   => 'datetime',
        'estimated_total_cost'  => 'decimal:2'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(PrItems::class);
    }

    public function tender()
    {
        return $this->hasOne(Tender::class);
    }

    public function workFlowLogs()
    {
        return $this->morphMany(WorkFlowLog::class, 'auditable');
    }
}
