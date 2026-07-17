<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'generated_by');
    }

    public function approvedApplications()
    {
        return $this->hasMany(SeatApplication::class, 'approved_by');
    }

    public function approvedRoomChanges()
    {
        return $this->hasMany(RoomChangeRequest::class, 'approved_by');
    }
}
