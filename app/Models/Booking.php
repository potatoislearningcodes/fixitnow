<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'technician_id',
        'service_type',
        'description',
        'address',
        'scheduled_at',
        'status', // pending | accepted | in_progress | completed | cancelled
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
