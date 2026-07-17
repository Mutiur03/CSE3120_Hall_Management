<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'seat_number',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function allocations()
    {
        return $this->hasMany(SeatAllocation::class);
    }

    public function currentAllocation()
    {
        return $this->hasOne(SeatAllocation::class)->where('status', 'active')->latest();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }
}
