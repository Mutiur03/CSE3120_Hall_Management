<?php

namespace Tests\Feature\Student;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_update_own_phone(): void
    {
        $student = Student::factory()->create([
            'phone' => '01700000000',
        ]);
        $student->user->update(['is_first_login' => false]);

        $response = $this->actingAs($student->user)->put(route('student.profile.update'), [
            'phone' => '01799999999',
        ]);

        $response->assertRedirect(route('student.profile'));
        $response->assertSessionHas('success');
        $this->assertSame('01799999999', $student->fresh()->phone);
    }

    public function test_student_cannot_update_phone_without_authentication(): void
    {
        $response = $this->put(route('student.profile.update'), [
            'phone' => '01799999999',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_admin_cannot_update_student_contact_through_student_route(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $response = $this->actingAs($admin)->put(route('student.profile.update'), [
            'phone' => '01788888888',
        ]);

        $response->assertForbidden();
        $this->assertNotSame('01788888888', $student->fresh()->phone);
    }

    public function test_first_login_student_cannot_update_contact_before_password_change(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student->user)->put(route('student.profile.update'), [
            'phone' => '01777777777',
        ]);

        $response->assertRedirect(route('student.change-password'));
    }
}
