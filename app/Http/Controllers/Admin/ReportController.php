<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Room;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\SeatApplication;
use App\Models\RoomChangeRequest;
use App\Models\Meal;
use App\Models\DiningAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use App\Exports\RoomOccupancyExport;
use App\Exports\DiningExport;

class ReportController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $totalRooms = Room::count();
        $totalSeats = Seat::count();
        $occupiedSeats = Seat::where('status', 'occupied')->count();
        $availableSeats = Seat::where('status', 'available')->count();
        $pendingApplications = SeatApplication::where('status', 'pending')->count();
        $pendingRoomChanges = RoomChangeRequest::where('status', 'pending')->count();

        return view('admin.reports.index', compact(
            'totalStudents', 'activeStudents', 'totalRooms', 'totalSeats',
            'occupiedSeats', 'availableSeats', 'pendingApplications', 'pendingRoomChanges'
        ));
    }

    public function studentReport(Request $request)
    {
        $query = Student::query();

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->latest()->get();
        $departments = Student::select('department')->distinct()->pluck('department');
        $sessions = Student::select('session')->distinct()->pluck('session');

        return view('admin.reports.student', compact('students', 'departments', 'sessions'));
    }

    
}
