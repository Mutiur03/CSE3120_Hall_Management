<?php

namespace Tests\Feature\Student;

use App\Enums\AllocationStatus;
use App\Enums\RoomChangeRequestStatus;
use App\Enums\RoomStatus;
use App\Enums\SeatStatus;
use App\Models\Room;
use App\Models\RoomChangeRequest;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomChangeRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create an active-seat student and return [student, currentRoom, seat].
     *
     * @return array{0: Student, 1: Room, 2: Seat}
     */
    private function allocatedStudent(): array
    {
        $student = Student::factory()->create();
        $student->user->update(['is_first_login' => false]);

        $room = Room::factory()->create(['room_no' => '101', 'floor' => 1, 'status' => RoomStatus::Active]);
        $seat = Seat::factory()->for($room)->create(['status' => SeatStatus::Active]);

        SeatAllocation::factory()->create([
            'student_id' => $student->id,
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        return [$student, $room, $seat];
    }

    public function test_guest_cannot_submit_room_change_request(): void
    {
        $room = Room::factory()->create();

        $response = $this->post(route('student.room-changes.store'), [
            'requested_room_id' => $room->id,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('room_change_requests', 0);
    }

    public function test_allocated_student_can_submit_request(): void
    {
        [$student, , $seat] = $this->allocatedStudent();
        $target = Room::factory()->create(['room_no' => '202', 'floor' => 2, 'status' => RoomStatus::Active]);

        $response = $this->actingAs($student->user)->post(route('student.room-changes.store'), [
            'requested_room_id' => $target->id,
            'reason' => 'Prefer a quieter floor',
        ]);

        $response->assertRedirect(route('student.room-changes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('room_change_requests', [
            'student_id' => $student->id,
            'current_seat_id' => $seat->id,
            'requested_room_id' => $target->id,
            'reason' => 'Prefer a quieter floor',
            'status' => RoomChangeRequestStatus::Pending->value,
        ]);
    }

    public function test_student_without_allocation_cannot_submit(): void
    {
        $student = Student::factory()->create();
        $student->user->update(['is_first_login' => false]);
        $target = Room::factory()->create(['status' => RoomStatus::Active]);

        $response = $this->actingAs($student->user)->post(route('student.room-changes.store'), [
            'requested_room_id' => $target->id,
        ]);

        $response->assertRedirect(route('student.room-changes.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('room_change_requests', 0);
    }

    public function test_student_cannot_submit_second_pending_request(): void
    {
        [$student] = $this->allocatedStudent();
        $target = Room::factory()->create(['status' => RoomStatus::Active]);

        RoomChangeRequest::factory()->create([
            'student_id' => $student->id,
            'status' => RoomChangeRequestStatus::Pending,
        ]);

        $response = $this->actingAs($student->user)->post(route('student.room-changes.store'), [
            'requested_room_id' => $target->id,
        ]);

        $response->assertRedirect(route('student.room-changes.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('room_change_requests', 1);
    }

    public function test_student_cannot_request_current_room(): void
    {
        [$student, $currentRoom] = $this->allocatedStudent();

        $response = $this->actingAs($student->user)->post(route('student.room-changes.store'), [
            'requested_room_id' => $currentRoom->id,
        ]);

        $response->assertRedirect(route('student.room-changes.create'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('room_change_requests', 0);
    }

    public function test_requested_room_must_be_active_and_valid(): void
    {
        [$student] = $this->allocatedStudent();
        $inactive = Room::factory()->create(['status' => RoomStatus::Inactive]);

        $response = $this->actingAs($student->user)->post(route('student.room-changes.store'), [
            'requested_room_id' => $inactive->id,
        ]);

        $response->assertSessionHasErrors('requested_room_id');
        $this->assertDatabaseCount('room_change_requests', 0);
    }

    public function test_student_can_view_own_requests(): void
    {
        [$student] = $this->allocatedStudent();

        RoomChangeRequest::factory()->create([
            'student_id' => $student->id,
            'reason' => 'Need to move closer to friends',
        ]);

        $response = $this->actingAs($student->user)->get(route('student.room-changes.index'));

        $response->assertOk();
        $response->assertSee('My Room Change Requests');
        $response->assertSee('Need to move closer to friends');
    }

    public function test_student_can_view_own_request_status_detail(): void
    {
        [$student] = $this->allocatedStudent();

        $request = RoomChangeRequest::factory()->rejected()->create([
            'student_id' => $student->id,
            'reason' => 'Roommate conflict',
            'admin_comment' => 'Requested room is full.',
        ]);

        $response = $this->actingAs($student->user)->get(route('student.room-changes.show', $request));

        $response->assertOk();
        $response->assertSee('Room Change Request Status');
        $response->assertSee('Rejected');
        $response->assertSee('Roommate conflict');
        $response->assertSee('Requested room is full.');
    }

    public function test_student_cannot_view_another_students_request(): void
    {
        [$owner] = $this->allocatedStudent();
        $request = RoomChangeRequest::factory()->create(['student_id' => $owner->id]);

        $other = Student::factory()->create();
        $other->user->update(['is_first_login' => false]);

        $response = $this->actingAs($other->user)->get(route('student.room-changes.show', $request));

        $response->assertForbidden();
    }
}
