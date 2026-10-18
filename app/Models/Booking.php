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
        'building_type',   // residential | institutional_business | hospitality
        'service_type',
        'service_group',   // repair | maintenance
        'description',
        'rooms',
        'address',
        'lat',
        'lng',
        'scheduled_at',
        'status', // pending | accepted | in_progress | completed | cancelled
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'lat' => 'float',
            'lng' => 'float',
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

    public function hasCoordinates(): bool
    {
        return $this->lat !== null && $this->lng !== null;
    }

    /**
     * Display label for the status, matching the simpler terminology from
     * the system guide (Pending / On-going / Repaired) without changing
     * the underlying database values the rest of the app relies on.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'in_progress' => 'On-going',
            'completed' => 'Repaired',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function buildingTypeLabel(): string
    {
        return match ($this->building_type) {
            'residential' => 'Residential',
            'institutional_business' => 'Institutional / Business',
            'hospitality' => 'Hospitality',
            default => '—',
        };
    }
}
