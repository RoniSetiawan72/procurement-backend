<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkFlowLog extends Model
{
    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'from_status',
        'to_status',
        'user_id',
        'notes'
    ];

    public function auditable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
