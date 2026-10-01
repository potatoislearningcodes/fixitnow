<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialty',
        'bio',
        'document_path',
        'verification_status', // pending | approved | rejected
        'admin_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isApproved(): bool
    {
        return $this->verification_status === 'approved';
    }

    /** Only technicians the admin has approved should be bookable by customers. */
    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'approved');
    }
}
