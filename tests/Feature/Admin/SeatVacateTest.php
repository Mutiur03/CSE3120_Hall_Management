<?php

namespace Tests\Feature\Admin;

use App\Enums\AllocationStatus;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeatVacateTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_vacate_form(): void
    {
        $seat = Seat::factory()->create();

        $response = $this->get(route('admin.seats.vacate-form', $seat));

        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_view_vacate_form(): void
    {
        $student = Student::factory()->create();
        $seat = Seat::factory()->create();

        $response = $this->actingAs($student->user)->get(route('admin.seats.vacate-form', $seat));

        $response->assertForbidden();
    }

    public function test_admin_sees_vacate_form_for_occupied_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.seats.vacate-form', $seat));

        $response->assertOk();
    }

    public function test_vacate_form_redirects_when_seat_not_occupied(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.seats.vacate-form', $seat));

        $response->assertRedirect(route('admin.seats.index'));
        $response->assertSessionHas('error');
    }

    public function test_guest_cannot_vacate_seat(): void
    {
        $seat = Seat::factory()->create();
        $allocation = SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->post(route('admin.seats.vacate', $seat));

        $response->assertRedirect(route('login'));
        $this->assertSame(AllocationStatus::Active, $allocation->fresh()->status);
    }

    public function test_student_cannot_vacate_seat(): void
    {
        $student = Student::factory()->create();
        $seat = Seat::factory()->create();
        $allocation = SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($student->user)->post(route('admin.seats.vacate', $seat));

        $response->assertForbidden();
        $this->assertSame(AllocationStatus::Active, $allocation->fresh()->status);
    }

    public function test_admin_can_vacate_occupied_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        $allocation = SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
            'vacated_at' => null,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.seats.vacate', $seat));

        $response->assertRedirect(route('admin.seats.available'));
        $response->assertSessionHas('success');

        $allocation->refresh();
        $this->assertSame(AllocationStatus::Vacated, $allocation->status);
        $this->assertNotNull($allocation->vacated_at);
        $this->assertTrue($seat->fresh()->isAvailable());
    }

    public function test_cannot_vacate_unoccupied_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.seats.vacate', $seat));

        $response->assertSessionHasErrors('seat');
        $this->assertDatabaseCount('seat_allocations', 0);
    }

    public function test_vacated_seat_can_be_reallocated(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $this->actingAs($admin)->post(route('admin.seats.vacate', $seat));

        $newStudent = Student::factory()->create();
        $response = $this->actingAs($admin)->post(route('admin.seats.allocate'), [
            'seat_id' => $seat->id,
            'student_id' => $newStudent->id,
        ]);

        $response->assertRedirect(route('admin.seats.occupied'));
        $this->assertDatabaseHas('seat_allocations', [
            'seat_id' => $seat->id,
            'student_id' => $newStudent->id,
            'status' => AllocationStatus::Active->value,
        ]);
    }
}
