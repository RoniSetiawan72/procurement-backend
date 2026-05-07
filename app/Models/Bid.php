<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = [
        'tender_id',
        'vendor_id',
        'offered_price',
        'bid_document_path',
        'is_winner'
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function vendor()
    {
        return $thos->belongsTo(Vendor::class);
    }
}
