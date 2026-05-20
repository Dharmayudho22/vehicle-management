<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
    protected $fillable = [
        'vehicle_id', 'booking_id',
        'liters', 'odometer', 'log_date', 'cost',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
