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
        'reserved_amoung'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
