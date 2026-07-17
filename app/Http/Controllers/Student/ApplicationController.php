<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SeatApplication;
use App\Models\Room;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $student = auth('student')->user();
        $applications = $student->seatApplications()->latest()->paginate(10);
        return view('student.applications.index', compact('applications'));
    }

    public function create()
    {
        $student = auth('student')->user();

        if ($student->currentAllocation) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You already have an allocated seat.');
        }

        $pendingApplication = $student->seatApplications()->where('status', 'pending')->first();
        if ($pendingApplication) {
            return redirect()->route('student.applications.index')
                ->with('error', 'You already have a pending application.');
        }

        $buildings = Room::select('building')->distinct()->pluck('building');
        $rooms = Room::where('status', '!=', 'full')->get();

        return view('student.applications.create', compact('buildings', 'rooms'));
    }

    public function store(Request $request)
    {
        $student = auth('student')->user();

        if ($student->currentAllocation) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You already have an allocated seat.');
        }

        $validated = $request->validate([
            'preferred_building' => 'nullable|string|max:255',
            'preferred_room' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:1000',
        ]);

        $validated['student_id'] = $student->id;
        $validated['status'] = 'pending';

        SeatApplication::create($validated);

        return redirect()->route('student.applications.index')
            ->with('success', 'Seat application submitted successfully.');
    }
}
