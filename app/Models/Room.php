<?php

namespace App\Models;

use App\Enums\RoomStatus;
use App\Enums\SeatStatus;
use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'room_no',
        'floor',
        'capacity',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'floor' => 'integer',
            'capacity' => 'integer',
            'status' => RoomStatus::class,
        ];
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    public function seatAllocations(): HasManyThrough
    {
        return $this->hasManyThrough(SeatAllocation::class, Seat::class);
    }

    public function activeAllocations(): HasManyThrough
    {
        return $this->seatAllocations()->where('seat_allocations.status', 'active');
    }

    public function occupiedSeatsCount(): int
    {
        return $this->seats()
            ->whereHas('currentAllocation')
            ->count();
    }

    public function availableSeatsCount(): int
    {
        return $this->seats()
            ->where('status', SeatStatus::Active)
            ->whereDoesntHave('currentAllocation')
            ->count();
    }

    public function occupancyPercentage(): float
    {
        if ($this->capacity === 0) {
            return 0;
        }

        return round(($this->occupiedSeatsCount() / $this->capacity) * 100, 2);
    }
}
