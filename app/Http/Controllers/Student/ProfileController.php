<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $student = auth('student')->user();
        $student->load(['currentAllocation.seat.room', 'seatApplications', 'roomChangeRequests']);

        return view('student.profile.show', compact('student'));
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $student = auth('student')->user();

        if (! Hash::check($validated['current_password'], $student->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $student->update([
            'password' => Hash::make($validated['new_password']),
            'password_changed' => true,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Password changed successfully.');
    }
}
