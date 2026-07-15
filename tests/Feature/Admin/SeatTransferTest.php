<?php

namespace Tests\Feature\Admin;

use App\Enums\AllocationStatus;
use App\Enums\SeatStatus;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeatTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_transfer_form(): void
    {
        $seat = Seat::factory()->create();

        $response = $this->get(route('admin.seats.transfer-form', $seat));

        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_view_transfer_form(): void
    {
        $student = Student::factory()->create();
        $seat = Seat::factory()->create();

        $response = $this->actingAs($student->user)->get(route('admin.seats.transfer-form', $seat));

        $response->assertForbidden();
    }

    public function test_admin_sees_transfer_form_for_occupied_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.seats.transfer-form', $seat));

        $response->assertOk();
    }

    public function test_transfer_form_redirects_when_seat_not_occupied(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.seats.transfer-form', $seat));

        $response->assertRedirect(route('admin.seats.index'));
        $response->assertSessionHas('error');
    }

    public function test_guest_cannot_transfer_seat(): void
    {
        $seat = Seat::factory()->create();
        $targetSeat = Seat::factory()->create();
        $allocation = SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->post(route('admin.seats.transfer', $seat), [
            'target_seat_id' => $targetSeat->id,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertSame(AllocationStatus::Active, $allocation->fresh()->status);
        $this->assertDatabaseCount('seat_allocations', 1);
    }

    public function test_student_cannot_transfer_seat(): void
    {
        $student = Student::factory()->create();
        $seat = Seat::factory()->create();
        $targetSeat = Seat::factory()->create();
        $allocation = SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($student->user)->post(route('admin.seats.transfer', $seat), [
            'target_seat_id' => $targetSeat->id,
        ]);

        $response->assertForbidden();
        $this->assertSame(AllocationStatus::Active, $allocation->fresh()->status);
        $this->assertDatabaseCount('seat_allocations', 1);
    }

    public function test_admin_can_transfer_occupied_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        $targetSeat = Seat::factory()->create();
        $allocation = SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
            'vacated_at' => null,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.seats.transfer', $seat), [
            'target_seat_id' => $targetSeat->id,
        ]);

        $response->assertRedirect(route('admin.seats.occupied'));
        $response->assertSessionHas('success');

        $allocation->refresh();
        $this->assertSame(AllocationStatus::Vacated, $allocation->status);
        $this->assertNotNull($allocation->vacated_at);
        $this->assertTrue($seat->fresh()->isAvailable());

        $this->assertDatabaseHas('seat_allocations', [
            'seat_id' => $targetSeat->id,
            'student_id' => $allocation->student_id,
            'status' => AllocationStatus::Active->value,
            'vacated_at' => null,
        ]);
        $this->assertTrue($targetSeat->fresh()->isOccupied());
    }

    public function test_cannot_transfer_unoccupied_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        $targetSeat = Seat::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.seats.transfer', $seat), [
            'target_seat_id' => $targetSeat->id,
        ]);

        $response->assertSessionHasErrors('seat');
        $this->assertDatabaseCount('seat_allocations', 0);
    }

    public function test_target_seat_id_is_required(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.seats.transfer', $seat), []);

        $response->assertSessionHasErrors('target_seat_id');
    }

    public function test_cannot_transfer_to_same_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.seats.transfer', $seat), [
            'target_seat_id' => $seat->id,
        ]);

        $response->assertSessionHasErrors('target_seat_id');
    }

    public function test_cannot_transfer_to_occupied_target_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        $targetSeat = Seat::factory()->create();
        $allocation = SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);
        SeatAllocation::factory()->create([
            'seat_id' => $targetSeat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.seats.transfer', $seat), [
            'target_seat_id' => $targetSeat->id,
        ]);

        $response->assertSessionHasErrors('seat_id');
        $this->assertSame(AllocationStatus::Active, $allocation->fresh()->status);
    }

    public function test_cannot_transfer_to_inactive_target_seat(): void
    {
        $admin = User::factory()->admin()->create();
        $seat = Seat::factory()->create();
        $targetSeat = Seat::factory()->create(['status' => SeatStatus::Inactive]);
        $allocation = SeatAllocation::factory()->create([
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.seats.transfer', $seat), [
            'target_seat_id' => $targetSeat->id,
        ]);

        $response->assertSessionHasErrors('seat_id');
        $this->assertSame(AllocationStatus::Active, $allocation->fresh()->status);
    }
}
