<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'item_id',
        'reference_type',
        'reference_id',
        'type',
        'quantity',
        'remarks',
        'user_id'
    ];
}
