<?php

namespace App\Models;

use App\Enums\RoomChangeRequestStatus;
use Database\Factories\RoomChangeRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomChangeRequest extends Model
{
    /** @use HasFactory<RoomChangeRequestFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'current_seat_id',
        'requested_room_id',
        'reason',
        'status',
        'admin_comment',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => RoomChangeRequestStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function currentSeat(): BelongsTo
    {
        return $this->belongsTo(Seat::class, 'current_seat_id');
    }

    public function requestedRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'requested_room_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === RoomChangeRequestStatus::Pending;
    }
}
