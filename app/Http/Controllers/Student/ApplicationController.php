<?php

namespace App\Http\Controllers\Student;

use App\Enums\RoomStatus;
use App\Enums\SeatApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreSeatApplicationRequest;
use App\Models\Room;
use App\Models\SeatApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(): View
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        $applications = $student->seatApplications()
            ->with('preferredRoom')
            ->latest()
            ->paginate(10);

        return view('student.applications.index', compact('applications', 'student'));
    }

    public function create(): View|RedirectResponse
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        if ($student->currentAllocation) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'You already have an allocated seat.');
        }

        if ($student->seatApplications()->where('status', SeatApplicationStatus::Pending)->exists()) {
            return redirect()
                ->route('student.applications.index')
                ->with('error', 'You already have a pending application.');
        }

        $floors = Room::query()->select('floor')->distinct()->orderBy('floor')->pluck('floor');
        $rooms = Room::query()
            ->where('status', RoomStatus::Active)
            ->orderBy('floor')
            ->orderBy('room_no')
            ->get();

        return view('student.applications.create', compact('floors', 'rooms'));
    }

    public function store(StoreSeatApplicationRequest $request): RedirectResponse
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        if ($student->currentAllocation) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'You already have an allocated seat.');
        }

        if ($student->seatApplications()->where('status', SeatApplicationStatus::Pending)->exists()) {
            return redirect()
                ->route('student.applications.index')
                ->with('error', 'You already have a pending application.');
        }

        SeatApplication::create([
            ...$request->validated(),
            'student_id' => $student->id,
            'status' => SeatApplicationStatus::Pending,
        ]);

        return redirect()
            ->route('student.applications.index')
            ->with('success', 'Seat application submitted successfully.');
    }
}
lass AuthController extends Controller
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

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
