<?php

namespace Tests\Feature\Admin;

use App\Enums\AllocationStatus;
use App\Enums\UserRole;
use App\Models\Room;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_room_details(): void
    {
        $room = Room::factory()->create();

        $response = $this->get(route('admin.rooms.show', $room));

        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_view_room_details(): void
    {
        $studentUser = User::factory()->create(['role' => UserRole::Student]);
        $room = Room::factory()->create();

        $response = $this->actingAs($studentUser)->get(route('admin.rooms.show', $room));

        $response->assertForbidden();
    }

    public function test_admin_can_view_room_details(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::factory()->create([
            'room_no' => '201',
            'floor' => 2,
            'capacity' => 2,
        ]);

        Seat::factory()->create(['room_id' => $room->id, 'seat_no' => 'S01']);
        Seat::factory()->create(['room_id' => $room->id, 'seat_no' => 'S02']);

        $response = $this->actingAs($admin)->get(route('admin.rooms.show', $room));

        $response->assertOk();
        $response->assertSee('Room 201');
        $response->assertSee('S01');
        $response->assertSee('S02');
        $response->assertSee('Vacant');
    }

    public function test_room_details_show_occupied_student_name(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::factory()->create(['capacity' => 1]);
        $seat = Seat::factory()->create(['room_id' => $room->id, 'seat_no' => 'S01']);
        $student = Student::factory()->create();

        SeatAllocation::factory()->create([
            'student_id' => $student->id,
            'seat_id' => $seat->id,
            'allocated_by' => $admin->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.rooms.show', $room));

        $response->assertOk();
        $response->assertSee('Occupied');
        $response->assertSee($student->user->name);
    }

    public function test_admin_can_view_rooms_index(): void
    {
        $admin = User::factory()->admin()->create();
        Room::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.rooms.index'));

        $response->assertOk();
        $response->assertSee('View Details');
    }
}
