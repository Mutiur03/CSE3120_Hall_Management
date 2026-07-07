<?php

namespace Tests\Feature\Admin;

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
}
