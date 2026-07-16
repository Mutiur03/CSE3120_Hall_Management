<?php

namespace Database\Factories;

use App\Enums\RoomChangeRequestStatus;
use App\Models\Room;
use App\Models\RoomChangeRequest;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomChangeRequest>
 */
class RoomChangeRequestFactory extends Factory
{
    protected $model = RoomChangeRequest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'current_seat_id' => null,
            'requested_room_id' => Room::factory(),
            'reason' => fake()->optional()->sentence(),
            'status' => RoomChangeRequestStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RoomChangeRequestStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RoomChangeRequestStatus::Rejected,
            'admin_comment' => fake()->sentence(),
            'reviewed_at' => now(),
        ]);
    }
}
