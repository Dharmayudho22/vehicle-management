<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceLog extends Model
{
    protected $table = 'services_logs';

    protected $fillable = [
        'vehicle_id', 'odometer',
        'service_date', 'cost', 'notes',
    ];

    protected $casts = [
        'service_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
