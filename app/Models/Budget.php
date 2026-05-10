<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Budget extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'department_id',
        'fiscal_year',
        'total_amount',
        'used_amount',
        'reserved_amount'
    ];

    protected $casts = [
        'total_amount'      => 'decimal:2',
        'reserved_amount'   => 'decimal:2',
        'used_amount'       => 'decimal:2'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getAvailableAmountAttribute()
    {
        return $this->total_amount - ($this->reserved_amount + $this->used_amount);
    }
}
