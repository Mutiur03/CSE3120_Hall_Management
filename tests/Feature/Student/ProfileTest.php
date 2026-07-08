<?php

namespace Tests\Feature\Student;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_student_profile(): void
    {
        $response = $this->get(route('student.profile'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_cannot_view_student_profile(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('student.profile'));

        $response->assertForbidden();
    }

    public function test_student_can_view_own_profile(): void
    {
        $student = Student::factory()->create([
            'roll' => '2024001',
            'registration_no' => 'REG-1001',
            'department' => 'CSE',
            'academic_session' => '2024-2025',
            'phone' => '01700000000',
        ]);
        $student->user->update(['is_first_login' => false]);

        $response = $this->actingAs($student->user)->get(route('student.profile'));

        $response->assertOk();
        $response->assertSee('My Profile');
        $response->assertSee($student->user->name);
        $response->assertSee('2024001');
        $response->assertSee('REG-1001');
        $response->assertSee('CSE');
        $response->assertSee('2024-2025');
        $response->assertSee('01700000000');
        $response->assertSee($student->user->email);
    }

    public function test_student_without_profile_record_gets_not_found(): void
    {
        $studentUser = User::factory()->create([
            'role' => UserRole::Student,
        ]);

        $response = $this->actingAs($studentUser)->get(route('student.profile'));

        $response->assertNotFound();
    }
}
