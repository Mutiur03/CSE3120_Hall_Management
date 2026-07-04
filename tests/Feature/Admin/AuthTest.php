<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_admin_login_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Admin Login');
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@hall.edu',
            'password' => 'admin123',
        ]);

        $response = $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@hall.edu',
            'password' => 'admin123',
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'admin@hall.edu',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_student_cannot_login_through_admin_form(): void
    {
        User::factory()->create([
            'email' => 'student@hall.edu',
            'password' => 'password',
            'role' => UserRole::Student,
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'student@hall.edu',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->create(['role' => UserRole::Student]);

        $response = $this->actingAs($student)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_can_change_password(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => 'old-password',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.change-password'), [
            'current_password' => 'old-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertTrue(password_verify('new-password', $admin->password));
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => 'old-password',
        ]);

        $response = $this->actingAs($admin)->from(route('admin.change-password'))->post(route('admin.change-password'), [
            'current_password' => 'wrong-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('admin.change-password'));
        $response->assertSessionHasErrors('current_password');
    }
}
