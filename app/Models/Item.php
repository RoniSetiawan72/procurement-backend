<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'uom',
        'estimated_price'
    ];

    protected $casts = ['estimated_price' => 'decimal:2'];

    public function prItems()
    {
        return $this->hasMany(PrItems::class, 'item_id');
    }
}
