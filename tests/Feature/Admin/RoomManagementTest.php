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

class RoomManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_room(): void
    {
        $response = $this->post(route('admin.rooms.store'), [
            'room_no' => '101',
            'floor' => 1,
            'capacity' => 2,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('rooms', 0);
    }

    public function test_student_cannot_create_room(): void
    {
        $student = User::factory()->create(['role' => UserRole::Student]);

        $response = $this->actingAs($student)->post(route('admin.rooms.store'), [
            'room_no' => '101',
            'floor' => 1,
            'capacity' => 2,
            'status' => 'active',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('rooms', 0);
    }

    public function test_admin_can_create_room_and_seats_are_generated(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'room_no' => '101',
            'floor' => 1,
            'capacity' => 3,
            'status' => 'active',
        ]);

        $room = Room::firstWhere('room_no', '101');

        $this->assertNotNull($room);
        $response->assertRedirect(route('admin.rooms.show', $room));
        $this->assertSame(3, $room->seats()->count());
    }

    public function test_create_room_validates_input(): void
    {
        $admin = User::factory()->admin()->create();
        Room::factory()->create(['room_no' => '101']);

        $response = $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'room_no' => '101',
            'floor' => 1,
            'capacity' => 0,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors(['room_no', 'capacity']);
        $this->assertDatabaseCount('rooms', 1);
    }

    public function test_admin_can_update_room_and_seats_grow(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::factory()->create(['room_no' => '101', 'capacity' => 2]);
        Seat::factory()->count(2)->sequence(
            ['seat_no' => '101-1'],
            ['seat_no' => '101-2'],
        )->create(['room_id' => $room->id]);

        $response = $this->actingAs($admin)->put(route('admin.rooms.update', $room), [
            'room_no' => '101',
            'floor' => 1,
            'capacity' => 4,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.rooms.show', $room));
        $this->assertSame(4, $room->fresh()->seats()->count());
    }

    public function test_capacity_cannot_drop_below_occupied_seats(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::factory()->create(['capacity' => 2]);
        $seat = Seat::factory()->create(['room_id' => $room->id, 'seat_no' => 'S1']);
        Seat::factory()->create(['room_id' => $room->id, 'seat_no' => 'S2']);
        $student = Student::factory()->create();
        SeatAllocation::factory()->create([
            'student_id' => $student->id,
            'seat_id' => $seat->id,
            'allocated_by' => $admin->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.rooms.update', $room), [
            'room_no' => $room->room_no,
            'floor' => $room->floor,
            'capacity' => 0,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('capacity');
        $this->assertSame(2, $room->fresh()->seats()->count());
    }

    public function test_admin_can_delete_empty_room(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::factory()->create();
        Seat::factory()->create(['room_id' => $room->id, 'seat_no' => 'S1']);

        $response = $this->actingAs($admin)->delete(route('admin.rooms.destroy', $room));

        $response->assertRedirect(route('admin.rooms.index'));
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
        $this->assertDatabaseMissing('seats', ['room_id' => $room->id]);
    }

    public function test_cannot_delete_room_with_occupied_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::factory()->create();
        $seat = Seat::factory()->create(['room_id' => $room->id, 'seat_no' => 'S1']);
        $student = Student::factory()->create();
        SeatAllocation::factory()->create([
            'student_id' => $student->id,
            'seat_id' => $seat->id,
            'allocated_by' => $admin->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.rooms.destroy', $room));

        $response->assertSessionHasErrors('room');
        $this->assertDatabaseHas('rooms', ['id' => $room->id]);
    }
}
