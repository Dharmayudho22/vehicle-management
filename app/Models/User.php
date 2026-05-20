<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'approved_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isApprover(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'approved_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'requester_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }

    public function pendingApprovals()
    {
        return $this->approvals()->where('status', 'pending');
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }
    
}
