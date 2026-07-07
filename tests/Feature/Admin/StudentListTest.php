<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentListTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_students_list(): void
    {
        $response = $this->get(route('admin.students.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_students_list(): void
    {
        $studentUser = User::factory()->create(['role' => UserRole::Student]);

        $response = $this->actingAs($studentUser)->get(route('admin.students.index'));

        $response->assertForbidden();
    }

    public function test_admin_sees_all_students(): void
    {
        $admin = User::factory()->admin()->create();
        $a = Student::factory()->create(['roll' => '2024001']);
        $b = Student::factory()->create(['roll' => '2024002']);

        $response = $this->actingAs($admin)->get(route('admin.students.index'));

        $response->assertOk();
        $response->assertSee($a->user->name);
        $response->assertSee($b->user->name);
    }

    public function test_filter_narrows_the_list(): void
    {
        $admin = User::factory()->admin()->create();
        $match = Student::factory()->create(['roll' => '2024999', 'department' => 'CSE']);
        Student::factory()->create(['roll' => '2024888', 'department' => 'EEE']);

        $response = $this->actingAs($admin)->get(route('admin.students.index', ['search' => '2024999']));

        $response->assertOk();
        $response->assertSee($match->user->name);
        $response->assertDontSee('2024888');
    }

    public function test_row_renders_student_info_popup(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create([
            'roll' => '2024777',
            'registration_no' => 'REG-7777',
            'department' => 'CSE',
            'academic_session' => '2024-2025',
            'phone' => '01711111111',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.students.index'));

        $response->assertOk();
        // Popup content is rendered server-side (hidden until clicked).
        $response->assertSee("student-modal-{$student->id}");
        $response->assertSee('REG-7777');
        $response->assertSee('01711111111');
        $response->assertSee($student->user->email);
    }
}
