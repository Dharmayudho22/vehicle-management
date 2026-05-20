<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'booking_id', 'approver_id', 'level',
        'status', 'notes', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
