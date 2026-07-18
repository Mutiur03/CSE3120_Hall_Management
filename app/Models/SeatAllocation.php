<?php

namespace App\Models;

use App\Enums\AllocationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'seat_id',
        'allocated_by',
        'allocated_at',
        'vacated_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'allocated_at' => 'date',
            'vacated_at' => 'date',
            'status' => AllocationStatus::class,
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function allocatedBy()
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    public function room()
    {
        return $this->hasOneThrough(Room::class, Seat::class, 'id', 'id', 'seat_id', 'room_id');
    }
}
