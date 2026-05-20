<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_code', 'requester_id', 'vehicle_id', 'driver_id',
        'start_datetime', 'end_datetime', 'destination', 'purpose',
        'passenger_count', 'status',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class)->orderBy('level');
    }

    public function approvalLevel1()
    {
        return $this->hasOne(Approval::class)->where('level', 1);
    }

    public function approvalLevel2()
    {
        return $this->hasOne(Approval::class)->where('level', 2);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $last = static::whereDate('created_at', today())->count() + 1;
        return 'BK-' . $date . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            'pending' => '<span class="badge-yellow">Menunggu Persetujuan</span>',
            'approved' => '<span class="badge-green">Disetujui</span>',
            'rejected' => '<span class="badge-red">Ditolak</span>',
            'completed' => '<span class="badge-gray">Selesai</span>',
            default => $this->status,
        };
    }

    public function getDurationHours(): float
    {
        return $this->start_datetime->diffInHours($this->end_datetime);
    }

    public function isEditable(): bool
    {
        return $this->status === 'pending';
    }

    public function isFullyApproved(): bool
    {
        return $this->approvals->count() > 0
            && $this->approvals->every(fn($a) => $a->status === 'approved');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}
