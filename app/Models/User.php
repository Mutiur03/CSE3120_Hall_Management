<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_first_login',
        'is_active',
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
            'role' => UserRole::class,
            'is_first_login' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::Student;
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
