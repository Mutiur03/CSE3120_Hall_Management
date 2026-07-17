<?php

namespace Tests\Feature\Admin;

use App\Enums\AllocationStatus;
use App\Enums\RoomChangeRequestStatus;
use App\Enums\RoomStatus;
use App\Enums\SeatStatus;
use App\Models\Room;
use App\Models\RoomChangeRequest;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomChangeApprovalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build an allocated student with a pending request toward a target room.
     *
     * @return array{admin: User, student: Student, currentSeat: Seat, targetRoom: Room, targetSeat: Seat, request: RoomChangeRequest}
     */
    private function scenario(): array
    {
        $admin = User::factory()->admin()->create();

        $student = Student::factory()->create();

        $currentRoom = Room::factory()->create(['room_no' => '101', 'floor' => 1, 'status' => RoomStatus::Active]);
        $currentSeat = Seat::factory()->for($currentRoom)->create(['status' => SeatStatus::Active]);

        SeatAllocation::factory()->create([
            'student_id' => $student->id,
            'seat_id' => $currentSeat->id,
            'status' => AllocationStatus::Active,
        ]);

        $targetRoom = Room::factory()->create(['room_no' => '202', 'floor' => 2, 'status' => RoomStatus::Active]);
        $targetSeat = Seat::factory()->for($targetRoom)->create(['status' => SeatStatus::Active]);

        $request = RoomChangeRequest::factory()->create([
            'student_id' => $student->id,
            'current_seat_id' => $currentSeat->id,
            'requested_room_id' => $targetRoom->id,
            'status' => RoomChangeRequestStatus::Pending,
        ]);

        return compact('admin', 'student', 'currentSeat', 'targetRoom', 'targetSeat', 'request');
    }

    public function test_guest_cannot_view_admin_room_changes(): void
    {
        $response = $this->get(route('admin.room-changes.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_view_admin_room_changes(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student->user)->get(route('admin.room-changes.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_list_room_changes(): void
    {
        $admin = User::factory()->admin()->create();
        $request = RoomChangeRequest::factory()->create(['reason' => 'Noisy roommate']);

        $response = $this->actingAs($admin)->get(route('admin.room-changes.index'));

        $response->assertOk();
        $response->assertSee('Room Change Requests');
        $response->assertSee('Noisy roommate');
        $response->assertSee((string) $request->id);
    }

    public function test_admin_approval_transfers_seat_atomically(): void
    {
        $s = $this->scenario();

        $response = $this->actingAs($s['admin'])->post(route('admin.room-changes.approve', $s['request']), [
            'target_seat_id' => $s['targetSeat']->id,
            'admin_comment' => 'Approved, enjoy the new room.',
        ]);

        $response->assertRedirect(route('admin.room-changes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('room_change_requests', [
            'id' => $s['request']->id,
            'status' => RoomChangeRequestStatus::Approved->value,
            'reviewed_by' => $s['admin']->id,
        ]);

        // Old allocation vacated
        $this->assertDatabaseHas('seat_allocations', [
            'student_id' => $s['student']->id,
            'seat_id' => $s['currentSeat']->id,
            'status' => AllocationStatus::Vacated->value,
        ]);

        // New active allocation on target seat
        $this->assertDatabaseHas('seat_allocations', [
            'student_id' => $s['student']->id,
            'seat_id' => $s['targetSeat']->id,
            'status' => AllocationStatus::Active->value,
        ]);

        $this->assertSame(1, SeatAllocation::query()
            ->where('student_id', $s['student']->id)
            ->where('status', AllocationStatus::Active)
            ->count());
    }

    public function test_approval_rejects_seat_not_in_requested_room(): void
    {
        $s = $this->scenario();
        $otherRoom = Room::factory()->create(['status' => RoomStatus::Active]);
        $otherSeat = Seat::factory()->for($otherRoom)->create(['status' => SeatStatus::Active]);

        $response = $this->actingAs($s['admin'])->post(route('admin.room-changes.approve', $s['request']), [
            'target_seat_id' => $otherSeat->id,
        ]);

        $response->assertSessionHasErrors('target_seat_id');

        $this->assertDatabaseHas('room_change_requests', [
            'id' => $s['request']->id,
            'status' => RoomChangeRequestStatus::Pending->value,
        ]);
        // Original allocation untouched
        $this->assertDatabaseHas('seat_allocations', [
            'student_id' => $s['student']->id,
            'seat_id' => $s['currentSeat']->id,
            'status' => AllocationStatus::Active->value,
        ]);
    }

    public function test_approval_fails_when_target_seat_occupied(): void
    {
        $s = $this->scenario();
        $occupant = Student::factory()->create();
        SeatAllocation::factory()->create([
            'student_id' => $occupant->id,
            'seat_id' => $s['targetSeat']->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($s['admin'])->post(route('admin.room-changes.approve', $s['request']), [
            'target_seat_id' => $s['targetSeat']->id,
        ]);

        $response->assertSessionHasErrors('target_seat_id');
        $this->assertDatabaseHas('room_change_requests', [
            'id' => $s['request']->id,
            'status' => RoomChangeRequestStatus::Pending->value,
        ]);
    }

    public function test_cannot_approve_non_pending_request(): void
    {
        $s = $this->scenario();
        $s['request']->update(['status' => RoomChangeRequestStatus::Rejected]);

        $response = $this->actingAs($s['admin'])->post(route('admin.room-changes.approve', $s['request']), [
            'target_seat_id' => $s['targetSeat']->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('seat_allocations', [
            'seat_id' => $s['targetSeat']->id,
            'status' => AllocationStatus::Active->value,
        ]);
    }

    public function test_admin_can_reject_request(): void
    {
        $s = $this->scenario();

        $response = $this->actingAs($s['admin'])->post(route('admin.room-changes.reject', $s['request']), [
            'admin_comment' => 'Requested room is reserved.',
        ]);

        $response->assertRedirect(route('admin.room-changes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('room_change_requests', [
            'id' => $s['request']->id,
            'status' => RoomChangeRequestStatus::Rejected->value,
            'admin_comment' => 'Requested room is reserved.',
            'reviewed_by' => $s['admin']->id,
        ]);
        // Allocation unchanged on reject
        $this->assertDatabaseHas('seat_allocations', [
            'student_id' => $s['student']->id,
            'seat_id' => $s['currentSeat']->id,
            'status' => AllocationStatus::Active->value,
        ]);
    }

    public function test_reject_requires_comment(): void
    {
        $s = $this->scenario();

        $response = $this->actingAs($s['admin'])->post(route('admin.room-changes.reject', $s['request']), []);

        $response->assertSessionHasErrors('admin_comment');
        $this->assertDatabaseHas('room_change_requests', [
            'id' => $s['request']->id,
            'status' => RoomChangeRequestStatus::Pending->value,
        ]);
    }
}
