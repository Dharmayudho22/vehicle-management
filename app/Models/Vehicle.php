<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'plate_number', 'brand', 'model', 'type', 'ownership', 'status',
        'fuel_consumption', 'last_service', 'next_service'
    ];

    protected $casts = [
        'last_service' => 'date',
        'next_service' => 'date',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }
    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }
    public function isAvilable()
    {
        return $this->status === 'available';
    }
    public function getOwnershipLabelAttribute()
    {
        return match($this->ownership) {
            'owned' => 'Milik Perusahaan',
            'leased' => 'Leasing',
            'rental' => 'Sewa',
            default => $this->ownership,
        };
    }
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'available'   => 'Tersedia',
            'in_use'      => 'Digunakan',
            'maintenance' => 'Perawatan',
            default       => $this->status,
        };
    }
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
    
}
