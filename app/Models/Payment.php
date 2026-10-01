<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'gateway_reference',
        'status', // pending | paid | failed
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
