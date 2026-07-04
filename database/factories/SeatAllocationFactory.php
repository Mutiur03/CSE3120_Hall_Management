<?php

namespace Database\Factories;

use App\Enums\AllocationStatus;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeatAllocation>
 */
class SeatAllocationFactory extends Factory
{
    protected $model = SeatAllocation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'seat_id' => Seat::factory(),
            'allocated_by' => User::factory()->admin(),
            'allocated_at' => now()->toDateString(),
            'vacated_at' => null,
            'status' => AllocationStatus::Active,
        ];
    }
}
