<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SeatAllocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'seat_id',
        'room_id',
        'allocation_date',
        'vacate_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'allocation_date' => 'date',
            'vacate_date' => 'date',
            'status' => 'string',
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

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
