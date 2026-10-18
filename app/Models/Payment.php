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
        'payment_method',    // cash | gcash | bank_transfer
        'reference_number',  // proof of payment, e.g. GCash ref or bank slip no.
        'status',            // pending | paid | failed
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function methodLabel(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'gcash' => 'GCash',
            'bank_transfer' => 'Online Banking',
            default => '—',
        };
    }
}
