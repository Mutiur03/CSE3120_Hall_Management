<?php

namespace Tests\Feature\Admin;

use App\Enums\SeatApplicationStatus;
use App\Models\SeatApplication;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeatApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_admin_applications(): void
    {
        $response = $this->get(route('admin.applications.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_view_admin_applications(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student->user)->get(route('admin.applications.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_list_applications(): void
    {
        $admin = User::factory()->admin()->create();
        $application = SeatApplication::factory()->create(['reason' => 'Urgent request']);

        $response = $this->actingAs($admin)->get(route('admin.applications.index'));

        $response->assertOk();
        $response->assertSee('Seat Applications');
        $response->assertSee('Urgent request');
        $response->assertSee((string) $application->id);
    }

    public function test_admin_can_approve_pending_application(): void
    {
        $admin = User::factory()->admin()->create();
        $application = SeatApplication::factory()->create([
            'status' => SeatApplicationStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.applications.approve', $application));

        $response->assertRedirect(route('admin.applications.index'));
        $response->assertSessionHas('success');

        $application->refresh();
        $this->assertSame(SeatApplicationStatus::Approved, $application->status);
        $this->assertSame($admin->id, $application->reviewed_by);
        $this->assertNotNull($application->reviewed_at);
    }

    public function test_admin_cannot_approve_non_pending_application(): void
    {
        $admin = User::factory()->admin()->create();
        $application = SeatApplication::factory()->approved()->create([
            'reviewed_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.applications.approve', $application));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(SeatApplicationStatus::Approved, $application->fresh()->status);
    }

    public function test_student_cannot_approve_application(): void
    {
        $student = Student::factory()->create();
        $application = SeatApplication::factory()->create();

        $response = $this->actingAs($student->user)->post(route('admin.applications.approve', $application));

        $response->assertForbidden();
        $this->assertSame(SeatApplicationStatus::Pending, $application->fresh()->status);
    }

    public function test_admin_can_reject_pending_application(): void
    {
        $admin = User::factory()->admin()->create();
        $application = SeatApplication::factory()->create([
            'status' => SeatApplicationStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.applications.reject', $application), [
            'admin_comment' => 'No seats available right now',
        ]);

        $response->assertRedirect(route('admin.applications.index'));
        $response->assertSessionHas('success');

        $application->refresh();
        $this->assertSame(SeatApplicationStatus::Rejected, $application->status);
        $this->assertSame('No seats available right now', $application->admin_comment);
        $this->assertSame($admin->id, $application->reviewed_by);
        $this->assertNotNull($application->reviewed_at);
    }

    public function test_admin_cannot_reject_without_comment(): void
    {
        $admin = User::factory()->admin()->create();
        $application = SeatApplication::factory()->create([
            'status' => SeatApplicationStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.applications.reject', $application), []);

        $response->assertSessionHasErrors('admin_comment');
        $this->assertSame(SeatApplicationStatus::Pending, $application->fresh()->status);
    }

    public function test_student_cannot_reject_application(): void
    {
        $student = Student::factory()->create();
        $application = SeatApplication::factory()->create();

        $response = $this->actingAs($student->user)->post(route('admin.applications.reject', $application), [
            'admin_comment' => 'Not allowed',
        ]);

        $response->assertForbidden();
        $this->assertSame(SeatApplicationStatus::Pending, $application->fresh()->status);
    }
}
