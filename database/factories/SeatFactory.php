<?php

namespace Database\Factories;

use App\Enums\SeatStatus;
use App\Models\Room;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Seat>
 */
class SeatFactory extends Factory
{
    protected $model = Seat::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'seat_no' => 'S'.fake()->unique()->numerify('##'),
            'status' => SeatStatus::Active,
        ];
    }
}
