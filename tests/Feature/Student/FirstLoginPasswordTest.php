<?php

namespace Tests\Feature\Student;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FirstLoginPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_with_first_login_flag_is_redirected_to_change_password_after_login(): void
    {
        $student = Student::factory()->create([
            'roll' => '2024001',
        ]);

        $student->user->update([
            'password' => Hash::make('2024001'),
            'is_first_login' => true,
        ]);

        $response = $this->post(route('student.login'), [
            'email' => $student->user->email,
            'password' => '2024001',
        ]);

        $response->assertRedirect(route('student.change-password'));
        $response->assertSessionHas('warning');
        $this->assertAuthenticatedAs($student->user);
    }

    public function test_first_login_student_cannot_access_dashboard(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student->user)->get(route('student.dashboard'));

        $response->assertRedirect(route('student.change-password'));
    }

    public function test_first_login_student_cannot_access_profile(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student->user)->get(route('student.profile'));

        $response->assertRedirect(route('student.change-password'));
    }

    public function test_first_login_student_can_view_change_password_form(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student->user)->get(route('student.change-password'));

        $response->assertOk();
        $response->assertSee('Change Password');
        $response->assertSee('must change your default password');
    }

    public function test_first_login_student_can_change_password(): void
    {
        $student = Student::factory()->create([
            'roll' => '2024002',
        ]);

        $student->user->update([
            'password' => Hash::make('2024002'),
            'is_first_login' => true,
        ]);

        $response = $this->actingAs($student->user)->post(route('student.change-password'), [
            'current_password' => '2024002',
            'new_password' => 'new-secure-password',
            'new_password_confirmation' => 'new-secure-password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('success');

        $student->user->refresh();
        $this->assertFalse($student->user->is_first_login);
        $this->assertTrue(Hash::check('new-secure-password', $student->user->password));
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        $student = Student::factory()->create([
            'roll' => '2024003',
        ]);

        $student->user->update([
            'password' => Hash::make('2024003'),
            'is_first_login' => true,
        ]);

        $response = $this->actingAs($student->user)
            ->from(route('student.change-password'))
            ->post(route('student.change-password'), [
                'current_password' => 'wrong-password',
                'new_password' => 'new-secure-password',
                'new_password_confirmation' => 'new-secure-password',
            ]);

        $response->assertRedirect(route('student.change-password'));
        $response->assertSessionHasErrors('current_password');
        $this->assertTrue($student->user->fresh()->is_first_login);
    }

    public function test_student_without_first_login_flag_goes_to_dashboard_after_login(): void
    {
        $user = User::factory()->student()->create([
            'email' => 'student@hall.edu',
            'password' => 'password',
            'is_first_login' => false,
        ]);

        $response = $this->post(route('student.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_first_login_student_can_logout(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($student->user)->post(route('student.logout'));

        $response->assertRedirect(route('student.login'));
        $this->assertGuest();
    }
}
