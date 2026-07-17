<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SeatApplication;
use App\Models\RoomChangeRequest;
use App\Models\Meal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth('student')->user();
        $student->load(['currentAllocation.seat.room', 'seatApplications', 'roomChangeRequests', 'meals']);

        $currentSeat = $student->currentSeat();
        $currentRoom = $student->currentRoom();

        $pendingApplication = $student->seatApplications()->where('status', 'pending')->latest()->first();
        $pendingRoomChange = $student->roomChangeRequests()->where('status', 'pending')->latest()->first();

        $todayMeal = Meal::where('student_id', $student->id)->where('date', today())->first();

        return view('student.dashboard', compact(
            'student', 'currentSeat', 'currentRoom',
            'pendingApplication', 'pendingRoomChange', 'todayMeal'
        ));
    }
}
