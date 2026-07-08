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
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $inactiveStudents = Student::where('status', 'inactive')->count();
        $totalRooms = Room::count();
        $totalSeats = Seat::count();
        $occupiedSeats = Seat::where('status', 'occupied')->count();
        $availableSeats = Seat::where('status', 'available')->count();
        $pendingApplications = SeatApplication::where('status', 'pending')->count();
        $pendingRoomChanges = RoomChangeRequest::where('status', 'pending')->count();

        $occupancyPercentage = $totalSeats > 0 ? round(($occupiedSeats / $totalSeats) * 100, 2) : 0;

        // Monthly allocation data for chart
        $months = [];
        $allocationData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');
            $allocationData[] = SeatAllocation::whereYear('allocation_date', $month->year)
                ->whereMonth('allocation_date', $month->month)
                ->count();
        }

        // Department distribution
        $departmentData = Student::selectRaw('department, count(*) as count')
            ->groupBy('department')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        $departments = $departmentData->pluck('department');
        $deptCounts = $departmentData->pluck('count');

        // Recent activities
        $recentAllocations = SeatAllocation::with(['student', 'room'])
            ->latest()
            ->limit(10)
            ->get();

        $recentApplications = SeatApplication::with('student')
            ->latest()
            ->limit(5)
            ->get();

        // Room occupancy by building
        $buildingStats = Room::selectRaw('building, count(*) as total_rooms, sum(capacity) as total_capacity')
            ->groupBy('building')
            ->get()
            ->map(function ($item) {
                $occupied = Seat::whereHas('room', function ($q) use ($item) {
                    $q->where('building', $item->building);
                })->where('status', 'occupied')->count();
                $item->occupied = $occupied;
                $item->occupancy_rate = $item->total_capacity > 0 ? round(($occupied / $item->total_capacity) * 100, 2) : 0;
                return $item;
            });

        // Today's meal counts
        $todayMeals = Meal::where('date', today())->where('meal_active', true);
        $breakfastCount = (clone $todayMeals)->where('breakfast', true)->count();
        $lunchCount = (clone $todayMeals)->where('lunch', true)->count();
        $dinnerCount = (clone $todayMeals)->where('dinner', true)->count();

        return view('admin.dashboard.index', compact(
            'totalStudents', 'activeStudents', 'inactiveStudents',
            'totalRooms', 'totalSeats', 'occupiedSeats', 'availableSeats',
            'pendingApplications', 'pendingRoomChanges', 'occupancyPercentage',
            'months', 'allocationData', 'departments', 'deptCounts',
            'recentAllocations', 'recentApplications', 'buildingStats',
            'breakfastCount', 'lunchCount', 'dinnerCount'
        ));
    }
}
