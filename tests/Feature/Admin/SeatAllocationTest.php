<?php

namespace Tests\Feature\Admin;

use App\Enums\AllocationStatus;
use App\Enums\SeatStatus;
use App\Enums\StudentStatus;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeatAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_allocate_form(): void
    {
        $response = $this->get(route('admin.seats.allocate-form'));

        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_view_allocate_form(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student->user)->get(route('admin.seats.allocate-form'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_allocate_form(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.seats.allocate-form'));

        $response->assertOk();
    }

    public function test_guest_cannot_allocate_seat(): void
    {
        $seat = Seat::factory()->create();
        $student = Student::factory()->create();

        $response = $this->post(route('admin.seats.allocate'), [
            'seat_id' => $seat->id,
            'student_id' => $student->id,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('seat_allocations', 0);
    }

    public function test_student_cannot_allocate_seat(): void
    {
        $seat = Seat::factory()->create();
        $student = Student::factory()->create();

        $response = $this->actingAs($student->user)->post(route('admin.seats.allocate'), [
            'seat_id' => $seat->id,
            'student_id' => $student->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('seat_allocations', 0);
    }

    public function test_admin_can_allocate_available_seat_to_active_student(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        $student = Student::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.seats.allocate'), [
            'seat_id' => $seat->id,
            'student_id' => $student->id,
        ]);

        $response->assertRedirect(route('admin.seats.occupied'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('seat_allocations', [
            'seat_id' => $seat->id,
            'student_id' => $student->id,
            'allocated_by' => $admin->id,
            'status' => AllocationStatus::Active->value,
            'vacated_at' => null,
        ]);
    }

    public function test_allocation_requires_seat_and_student(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.seats.allocate'), []);

        $response->assertSessionHasErrors(['seat_id', 'student_id']);
        $this->assertDatabaseCount('seat_allocations', 0);
    }

    public function test_cannot_allocate_to_inactive_student(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        $student = Student::factory()->create(['status' => StudentStatus::Inactive]);

        $response = $this->actingAs($admin)->post(route('admin.seats.allocate'), [
            'seat_id' => $seat->id,
            'student_id' => $student->id,
        ]);

        $response->assertSessionHasErrors('student_id');
        $this->assertDatabaseCount('seat_allocations', 0);
    }

    public function test_cannot_allocate_inactive_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create(['status' => SeatStatus::Inactive]);
        $student = Student::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.seats.allocate'), [
            'seat_id' => $seat->id,
            'student_id' => $student->id,
        ]);

        $response->assertSessionHasErrors('seat_id');
        $this->assertDatabaseCount('seat_allocations', 0);
    }

    public function test_cannot_allocate_second_seat_to_already_allocated_student(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();
        SeatAllocation::factory()->create([
            'student_id' => $student->id,
            'status' => AllocationStatus::Active,
        ]);
        $newSeat = Seat::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.seats.allocate'), [
            'seat_id' => $newSeat->id,
            'student_id' => $student->id,
        ]);

        $response->assertSessionHasErrors('student_id');
        $this->assertSame(1, SeatAllocation::where('student_id', $student->id)->count());
        $this->assertDatabaseMissing('seat_allocations', [
            'seat_id' => $newSeat->id,
        ]);
    }

    public function test_cannot_allocate_already_occupied_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);
        $student = Student::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.seats.allocate'), [
            'seat_id' => $seat->id,
            'student_id' => $student->id,
        ]);

        $response->assertSessionHasErrors('seat_id');
        $this->assertSame(1, SeatAllocation::where('seat_id', $seat->id)->count());
        $this->assertDatabaseMissing('seat_allocations', [
            'student_id' => $student->id,
        ]);
    }
}
