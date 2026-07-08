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
