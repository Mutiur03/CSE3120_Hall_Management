<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('student.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'student_id' => 'required|string',
            'password' => 'required',
        ]);

        $student = Student::where('student_id', $credentials['student_id'])->first();

        if ($student && Hash::check($credentials['password'], $student->password)) {
            Auth::guard('student')->login($student, $request->has('remember'));
            $request->session()->regenerate();

            if (!$student->password_changed) {
                return redirect()->route('student.change-password')
                    ->with('warning', 'Please change your default password.');
            }

            return redirect()->intended(route('student.dashboard'));
        }

        return redirect()->back()
            ->withErrors(['student_id' => 'Invalid student ID or password.'])
            ->withInput($request->except('password'));
    }

    public function changePasswordForm()
    {
        return view('student.auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $student = Auth::guard('student')->user();

        if (!Hash::check($validated['current_password'], $student->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $student->update([
            'password' => Hash::make($validated['new_password']),
            'password_changed' => true,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Password changed successfully.');
    }

      public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $student = auth('student')->user();

        if (!Hash::check($validated['current_password'], $student->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $student->update([
            'password' => Hash::make($validated['new_password']),
            'password_changed' => true,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Password changed successfully.');
    }
}


