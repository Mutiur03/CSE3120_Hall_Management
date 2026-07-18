<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DiningExport;
use App\Exports\RoomOccupancyExport;
use App\Http\Controllers\Controller;
use App\Models\DiningAttendance;
use App\Models\Room;
use App\Models\RoomChangeRequest;
use App\Models\Seat;
use App\Models\SeatApplication;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $totalRooms = Room::count();
        $totalSeats = Seat::count();
        $occupiedSeats = Seat::occupied()->count();
        $availableSeats = Seat::available()->count();
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
            $query->where('academic_session', $request->session);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->with('user')->latest()->get();
        $departments = Student::select('department')->distinct()->pluck('department');
        $sessions = Student::select('academic_session')->distinct()->pluck('academic_session');

        return view('admin.reports.student', compact('students', 'departments', 'sessions'));
    }

    public function roomOccupancyReport(Request $request)
    {
        $rooms = Room::withCount(['seats as total_seats', 'seats as occupied_seats' => function ($query) {
            $query->whereHas('currentAllocation');
        }])->get()->map(function ($room) {
            $room->available_seats = $room->total_seats - $room->occupied_seats;
            $room->occupancy_percentage = $room->total_seats > 0 ? round(($room->occupied_seats / $room->total_seats) * 100, 2) : 0;

            return $room;
        });

        if ($request->filled('export')) {
            if ($request->export === 'pdf') {
                $pdf = Pdf::loadView('admin.reports.exports.room-pdf', compact('rooms'));

                return $pdf->download('room-occupancy-report-'.now()->format('Y-m-d').'.pdf');
            }

            if ($request->export === 'excel') {
                return Excel::download(new RoomOccupancyExport, 'room-occupancy-report-'.now()->format('Y-m-d').'.xlsx');
            }
        }

        return view('admin.reports.room-occupancy', compact('rooms'));
    }

    public function diningReport(Request $request)
    {
        $month = $request->filled('month') ? $request->month : now()->format('Y-m');
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);
        $daysInMonth = Carbon::create($year, $monthNum)->daysInMonth;

        $dailyStats = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $monthNum, $day)->toDateString();
            $dailyStats[$day] = [
                'date' => $date,
                'breakfast' => DiningAttendance::where('date', $date)->where('meal_type', 'breakfast')->where('present', true)->count(),
                'lunch' => DiningAttendance::where('date', $date)->where('meal_type', 'lunch')->where('present', true)->count(),
                'dinner' => DiningAttendance::where('date', $date)->where('meal_type', 'dinner')->where('present', true)->count(),
            ];
        }

        $totalBreakfast = array_sum(array_column($dailyStats, 'breakfast'));
        $totalLunch = array_sum(array_column($dailyStats, 'lunch'));
        $totalDinner = array_sum(array_column($dailyStats, 'dinner'));

        if ($request->filled('export')) {
            if ($request->export === 'pdf') {
                $pdf = Pdf::loadView('admin.reports.exports.dining-pdf', compact('dailyStats', 'month', 'daysInMonth', 'totalBreakfast', 'totalLunch', 'totalDinner'));

                return $pdf->download('dining-report-'.$month.'.pdf');
            }

            if ($request->export === 'excel') {
                return Excel::download(new DiningExport($month), 'dining-report-'.$month.'.xlsx');
            }
        }

        return view('admin.reports.dining', compact('month', 'dailyStats', 'daysInMonth', 'totalBreakfast', 'totalLunch', 'totalDinner'));
    }

    public function dashboardOverview()
    {
        $data = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'total_rooms' => Room::count(),
            'total_seats' => Seat::count(),
            'occupied_seats' => Seat::occupied()->count(),
            'available_seats' => Seat::available()->count(),
            'pending_applications' => SeatApplication::where('status', 'pending')->count(),
            'pending_room_changes' => RoomChangeRequest::where('status', 'pending')->count(),
            'department_distribution' => Student::selectRaw('department, count(*) as count')->groupBy('department')->get(),
            'building_occupancy' => Room::selectRaw('floor')->groupBy('floor')->orderBy('floor')->get()->map(function ($item) {
                $item->building = 'Floor '.$item->floor;
                $item->total = Seat::whereHas('room', fn ($q) => $q->where('floor', $item->floor))->count();
                $item->occupied = Seat::whereHas('room', fn ($q) => $q->where('floor', $item->floor))->occupied()->count();

                return $item;
            }),
        ];

        return view('admin.reports.overview', compact('data'));
    }
}
