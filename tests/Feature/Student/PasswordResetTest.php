<?php

namespace Tests\Feature\Student;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\StudentResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_forgot_password_page(): void
    {
        $response = $this->get(route('student.password.request'));

        $response->assertOk();
        $response->assertSee('Forgot Password');
    }

    public function test_student_receives_reset_link_notification(): void
    {
        Notification::fake();

        $student = User::factory()->create([
            'email' => 'student@hall.edu',
            'role' => UserRole::Student,
        ]);

        $response = $this->post(route('student.password.email'), [
            'email' => $student->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Notification::assertSentTo($student, StudentResetPassword::class);
    }

    public function test_admin_email_does_not_receive_reset_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create([
            'email' => 'admin@hall.edu',
        ]);

        $response = $this->post(route('student.password.email'), [
            'email' => $admin->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_unknown_email_shows_same_success_message(): void
    {
        Notification::fake();

        $response = $this->post(route('student.password.email'), [
            'email' => 'missing@hall.edu',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'If a matching student account exists, a reset link has been sent to the email address.');

        Notification::assertNothingSent();
    }

    public function test_student_can_reset_password_with_valid_token(): void
    {
        $student = User::factory()->create([
            'email' => 'student@hall.edu',
            'password' => 'old-password',
            'role' => UserRole::Student,
        ]);

        $token = Password::broker('users')->createToken($student);

        $response = $this->post(route('student.password.update'), [
            'token' => $token,
            'email' => $student->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('student.login'));
        $response->assertSessionHas('status');

        $student->refresh();
        $this->assertTrue(Hash::check('new-password', $student->password));
    }

    public function test_student_can_login_after_password_reset(): void
    {
        $student = User::factory()->create([
            'email' => 'student@hall.edu',
            'password' => 'old-password',
            'role' => UserRole::Student,
        ]);

        $token = Password::broker('users')->createToken($student);

        $this->post(route('student.password.update'), [
            'token' => $token,
            'email' => $student->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response = $this->post(route('student.login'), [
            'email' => $student->email,
            'password' => 'new-password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_reset_fails_with_invalid_token(): void
    {
        $student = User::factory()->create([
            'email' => 'student@hall.edu',
            'role' => UserRole::Student,
        ]);

        $response = $this->from(route('student.password.reset', ['token' => 'invalid-token', 'email' => $student->email]))
            ->post(route('student.password.update'), [
                'token' => 'invalid-token',
                'email' => $student->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect(route('student.password.reset', ['token' => 'invalid-token', 'email' => $student->email]));
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_cannot_reset_password_through_student_form(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@hall.edu',
            'password' => 'old-password',
        ]);

        $token = Password::broker('users')->createToken($admin);

        $response = $this->from(route('student.password.reset', ['token' => $token, 'email' => $admin->email]))
            ->post(route('student.password.update'), [
                'token' => $token,
                'email' => $admin->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect(route('student.password.reset', ['token' => $token, 'email' => $admin->email]));
        $response->assertSessionHasErrors('email');

        $admin->refresh();
        $this->assertTrue(Hash::check('old-password', $admin->password));
    }

    public function test_student_login_page_shows_forgot_password_link(): void
    {
        $response = $this->get(route('student.login'));

        $response->assertOk();
        $response->assertSee('Forgot password?');
    }
}
