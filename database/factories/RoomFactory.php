<?php

namespace Database\Factories;

use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_no' => fake()->unique()->numerify('###'),
            'floor' => fake()->numberBetween(1, 5),
            'capacity' => fake()->numberBetween(2, 4),
            'status' => RoomStatus::Active,
        ];
    }
}
