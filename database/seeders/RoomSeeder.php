<?php

namespace Database\Seeders;

use App\Enums\AllocationStatus;
use App\Enums\RoomStatus;
use App\Enums\SeatStatus;
use App\Models\Room;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@hall.edu')->first();

        if (! $admin) {
            return;
        }

        $room = Room::query()->updateOrCreate(
            ['room_no' => '101'],
            [
                'floor' => 1,
                'capacity' => 3,
                'status' => RoomStatus::Active,
            ]
        );

        foreach (['S01', 'S02', 'S03'] as $seatNo) {
            Seat::query()->updateOrCreate(
                ['room_id' => $room->id, 'seat_no' => $seatNo],
                ['status' => SeatStatus::Active]
            );
        }

        $student = Student::query()->first();

        if ($student) {
            $seat = Seat::query()->where('room_id', $room->id)->where('seat_no', 'S01')->first();

            if ($seat && ! SeatAllocation::query()->where('seat_id', $seat->id)->where('status', AllocationStatus::Active)->exists()) {
                SeatAllocation::query()->create([
                    'student_id' => $student->id,
                    'seat_id' => $seat->id,
                    'allocated_by' => $admin->id,
                    'allocated_at' => now()->toDateString(),
                    'status' => AllocationStatus::Active,
                ]);
            }
        }
    }
}
