<?php

namespace Tests\Feature\Student;

use App\Enums\AllocationStatus;
use App\Models\Room;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeatAllocationViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_seat_allocation(): void
    {
        $response = $this->get(route('student.seat'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_cannot_view_student_seat_allocation(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('student.seat'));

        $response->assertForbidden();
    }

    public function test_student_sees_current_allocation(): void
    {
        $student = Student::factory()->create();
        $student->user->update(['is_first_login' => false]);
        $room = Room::factory()->create(['room_no' => '204', 'floor' => 2]);
        $seat = Seat::factory()->create(['room_id' => $room->id, 'seat_no' => 'B1']);
        SeatAllocation::factory()->create([
            'student_id' => $student->id,
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($student->user)->get(route('student.seat'));

        $response->assertOk();
        $response->assertSee('My Seat Allocation');
        $response->assertSee('204');
        $response->assertSee('B1');
        $response->assertDontSee('No seat allocated');
    }

    public function test_student_without_allocation_sees_empty_state(): void
    {
        $student = Student::factory()->create();
        $student->user->update(['is_first_login' => false]);

        $response = $this->actingAs($student->user)->get(route('student.seat'));

        $response->assertOk();
        $response->assertSee('No seat allocated');
    }

    public function test_student_only_sees_own_allocation(): void
    {
        $other = Student::factory()->create();
        $otherRoom = Room::factory()->create(['room_no' => '999']);
        $otherSeat = Seat::factory()->create(['room_id' => $otherRoom->id, 'seat_no' => 'Z9']);
        SeatAllocation::factory()->create([
            'student_id' => $other->id,
            'seat_id' => $otherSeat->id,
            'status' => AllocationStatus::Active,
        ]);

        $viewer = Student::factory()->create();
        $viewer->user->update(['is_first_login' => false]);
        $viewerRoom = Room::factory()->create(['room_no' => '101']);
        $viewerSeat = Seat::factory()->create(['room_id' => $viewerRoom->id, 'seat_no' => 'A1']);
        SeatAllocation::factory()->create([
            'student_id' => $viewer->id,
            'seat_id' => $viewerSeat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($viewer->user)->get(route('student.seat'));

        $response->assertOk();
        $response->assertSee('101');
        $response->assertSee('A1');
        $response->assertDontSee('999');
        $response->assertDontSee('Z9');
    }

    public function test_vacated_allocation_is_not_shown(): void
    {
        $student = Student::factory()->create();
        $student->user->update(['is_first_login' => false]);
        $seat = Seat::factory()->create();
        SeatAllocation::factory()->create([
            'student_id' => $student->id,
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Vacated,
            'vacated_at' => now()->toDateString(),
        ]);

        $response = $this->actingAs($student->user)->get(route('student.seat'));

        $response->assertOk();
        $response->assertSee('No seat allocated');
    }
}
