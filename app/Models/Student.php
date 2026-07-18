<?php

namespace App\Models;

use App\Enums\StudentStatus;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'roll',
        'registration_no',
        'department',
        'academic_session',
        'phone',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StudentStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seatAllocations(): HasMany
    {
        return $this->hasMany(SeatAllocation::class);
    }

    public function currentAllocation(): HasOne
    {
        return $this->hasOne(SeatAllocation::class)
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function seatApplications(): HasMany
    {
        return $this->hasMany(SeatApplication::class);
    }

    public function roomChangeRequests(): HasMany
    {
        return $this->hasMany(RoomChangeRequest::class);
    }

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    public function diningAttendances(): HasMany
    {
        return $this->hasMany(DiningAttendance::class);
    }

    public function currentSeat(): ?Seat
    {
        return $this->currentAllocation?->seat;
    }

    public function currentRoom(): ?Room
    {
        return $this->currentAllocation?->seat?->room;
    }
}
