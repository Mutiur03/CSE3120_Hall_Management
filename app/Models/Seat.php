<?php

namespace App\Models;

use App\Enums\SeatStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'seat_no',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => SeatStatus::class,
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

    public function scopeAvailable($query)
    {
        return $query->where('status', SeatStatus::Active)
            ->whereDoesntHave('currentAllocation');
    }

    public function scopeOccupied($query)
    {
        return $query->whereHas('currentAllocation');
    }

    public function isActive(): bool
    {
        return $this->status === SeatStatus::Active;
    }

    public function isAvailable(): bool
    {
        return $this->status === SeatStatus::Active
            && ! $this->currentAllocation()->exists();
    }

    public function isOccupied(): bool
    {
        return $this->currentAllocation()->exists();
    }
}
