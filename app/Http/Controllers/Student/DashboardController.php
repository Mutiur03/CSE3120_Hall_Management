<?php

namespace App\Http\Controllers\Student;

use App\Enums\RoomChangeRequestStatus;
use App\Enums\SeatApplicationStatus;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        $student->load(['user', 'currentAllocation.seat.room', 'seatApplications', 'roomChangeRequests']);

        $currentSeat = $student->currentSeat();
        $currentRoom = $student->currentRoom();

        $pendingApplication = $student->seatApplications()
            ->where('status', SeatApplicationStatus::Pending)
            ->latest()
            ->first();

        $pendingRoomChange = $student->roomChangeRequests()
            ->where('status', RoomChangeRequestStatus::Pending)
            ->latest()
            ->first();

        $todayMeal = null;

        return view('student.dashboard', compact(
            'student', 'currentSeat', 'currentRoom',
            'pendingApplication', 'pendingRoomChange', 'todayMeal'
        ));
    }
}
