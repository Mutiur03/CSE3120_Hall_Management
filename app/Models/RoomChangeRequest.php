<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RoomChangeRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'current_room_id',
        'requested_room_id',
        'reason',
        'status',
        'admin_remarks',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'approved_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function currentRoom()
    {
        return $this->belongsTo(Room::class, 'current_room_id');
    }

    public function requestedRoom()
    {
        return $this->belongsTo(Room::class, 'requested_room_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
