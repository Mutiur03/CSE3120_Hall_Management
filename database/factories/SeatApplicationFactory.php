<?php

namespace Database\Factories;

use App\Enums\SeatApplicationStatus;
use App\Models\SeatApplication;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeatApplication>
 */
class SeatApplicationFactory extends Factory
{
    protected $model = SeatApplication::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'preferred_floor' => fake()->optional()->numberBetween(1, 5),
            'preferred_room_id' => null,
            'reason' => fake()->optional()->sentence(),
            'status' => SeatApplicationStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SeatApplicationStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SeatApplicationStatus::Rejected,
            'admin_comment' => fake()->sentence(),
            'reviewed_at' => now(),
        ]);
    }
}
