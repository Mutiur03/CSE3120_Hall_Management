<?php

namespace App\Http\Controllers\Student;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\StudentResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showForgotForm(): View
    {
        return view('student.auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()
            ->where('email', $request->input('email'))
            ->where('role', UserRole::Student)
            ->first();

        if ($user) {
            Password::sendResetLink(
                ['email' => $user->email],
                function (User $user, string $token): void {
                    $user->notify(new StudentResetPassword($token));
                }
            );
        }

        return back()->with('status', 'If a matching student account exists, a reset link has been sent to the email address.');
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('student.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = User::query()
            ->where('email', $request->input('email'))
            ->first();

        if (! $user || $user->role !== UserRole::Student) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid or expired reset link.']);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('student.login')
                ->with('status', 'Your password has been reset. You can now log in.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid or expired reset link.']);
    }
}
