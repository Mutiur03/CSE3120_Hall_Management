<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'student_id',
        'name',
        'department',
        'session',
        'batch',
        'gender',
        'blood_group',
        'phone',
        'email',
        'address',
        'guardian_name',
        'guardian_phone',
        'photo',
        'status',
        'password',
        'password_changed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'password_changed' => 'boolean',
            'status' => 'string',
        ];
    }

    public function getAuthIdentifierName()
    {
        return 'student_id';
    }

    public function seatAllocations()
    {
        return $this->hasMany(SeatAllocation::class);
    }

    public function currentAllocation()
    {
        return $this->hasOne(SeatAllocation::class)->where('status', 'active')->latest();
    }

    public function seatApplications()
    {
        return $this->hasMany(SeatApplication::class);
    }

    public function roomChangeRequests()
    {
        return $this->hasMany(RoomChangeRequest::class);
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function diningAttendances()
    {
        return $this->hasMany(DiningAttendance::class);
    }

    public function currentSeat()
    {
        $allocation = $this->currentAllocation;
        return $allocation ? $allocation->seat : null;
    }

    public function currentRoom()
    {
        $allocation = $this->currentAllocation;
        return $allocation ? $allocation->room : null;
    }
}
