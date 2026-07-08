<?php

namespace Tests\Feature\Student;

use App\Enums\AllocationStatus;
use App\Enums\SeatApplicationStatus;
use App\Enums\SeatStatus;
use App\Models\Room;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\SeatApplication;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeatApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_seat_application(): void
    {
        $response = $this->post(route('student.applications.store'), [
            'reason' => 'Need a seat',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('seat_applications', 0);
    }

    public function test_student_can_submit_seat_application(): void
    {
        $student = Student::factory()->create();
        $student->user->update(['is_first_login' => false]);
        $room = Room::factory()->create(['room_no' => '101', 'floor' => 2]);

        $response = $this->actingAs($student->user)->post(route('student.applications.store'), [
            'preferred_floor' => 2,
            'preferred_room_id' => $room->id,
            'reason' => 'Need a seat near campus',
        ]);

        $response->assertRedirect(route('student.applications.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('seat_applications', [
            'student_id' => $student->id,
            'preferred_floor' => 2,
            'preferred_room_id' => $room->id,
            'reason' => 'Need a seat near campus',
            'status' => SeatApplicationStatus::Pending->value,
        ]);
    }

    public function test_student_cannot_submit_when_already_allocated(): void
    {
        $student = Student::factory()->create();
        $student->user->update(['is_first_login' => false]);
        $room = Room::factory()->create();
        $seat = Seat::factory()->for($room)->create(['status' => SeatStatus::Active]);

        SeatAllocation::factory()->create([
            'student_id' => $student->id,
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($student->user)->post(route('student.applications.store'), [
            'reason' => 'Need a seat',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('seat_applications', 0);
    }

    public function test_student_cannot_submit_second_pending_application(): void
    {
        $student = Student::factory()->create();
        $student->user->update(['is_first_login' => false]);

        SeatApplication::factory()->create([
            'student_id' => $student->id,
            'status' => SeatApplicationStatus::Pending,
        ]);

        $response = $this->actingAs($student->user)->post(route('student.applications.store'), [
            'reason' => 'Another request',
        ]);

        $response->assertRedirect(route('student.applications.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('seat_applications', 1);
    }

    public function test_student_can_view_own_applications(): void
    {
        $student = Student::factory()->create();
        $student->user->update(['is_first_login' => false]);

        SeatApplication::factory()->create([
            'student_id' => $student->id,
            'reason' => 'Need a quiet room',
        ]);

        $response = $this->actingAs($student->user)->get(route('student.applications.index'));

        $response->assertOk();
        $response->assertSee('My Applications');
        $response->assertSee('Need a quiet room');
    }
}
