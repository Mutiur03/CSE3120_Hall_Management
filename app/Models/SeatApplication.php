<?php

namespace App\Models;

use App\Enums\SeatApplicationStatus;
use Database\Factories\SeatApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatApplication extends Model
{
    /** @use HasFactory<SeatApplicationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'preferred_floor',
        'preferred_room_id',
        'reason',
        'document_path',
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
            'preferred_floor' => 'integer',
            'status' => SeatApplicationStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function preferredRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'preferred_room_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === SeatApplicationStatus::Pending;
    }
}
