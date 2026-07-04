<?php

namespace App\Models;

use App\Enums\SeatStatus;
use Database\Factories\SeatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Seat extends Model
{
    /** @use HasFactory<SeatFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'room_id',
        'seat_no',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SeatStatus::class,
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SeatAllocation::class);
    }

    public function currentAllocation(): HasOne
    {
        return $this->hasOne(SeatAllocation::class)
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function isOccupied(): bool
    {
        return $this->currentAllocation()->exists();
    }

    public function isAvailable(): bool
    {
        return $this->status === SeatStatus::Active && ! $this->isOccupied();
    }
}
