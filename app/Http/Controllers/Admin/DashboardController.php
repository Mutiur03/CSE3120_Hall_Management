<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoomChangeRequestStatus;
use App\Enums\SeatApplicationStatus;
use App\Enums\StudentStatus;
use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Room;
use App\Models\RoomChangeRequest;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\SeatApplication;
use App\Models\Student;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', StudentStatus::Active)->count();
        $inactiveStudents = Student::where('status', StudentStatus::Inactive)->count();
        $totalRooms = Room::count();
        $totalSeats = Seat::count();
        $occupiedSeats = Seat::occupied()->count();
        $availableSeats = Seat::available()->count();
        $pendingApplications = SeatApplication::where('status', SeatApplicationStatus::Pending)->count();
        $pendingRoomChanges = RoomChangeRequest::where('status', RoomChangeRequestStatus::Pending)->count();

        $occupancyPercentage = $totalSeats > 0 ? round(($occupiedSeats / $totalSeats) * 100, 2) : 0;

        // Monthly allocation data for chart
        $months = [];
        $allocationData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');
            $allocationData[] = SeatAllocation::whereYear('allocated_at', $month->year)
                ->whereMonth('allocated_at', $month->month)
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
        $recentAllocations = SeatAllocation::with(['student.user', 'seat.room'])
            ->latest()
            ->limit(10)
            ->get();

        $recentApplications = SeatApplication::with('student.user')
            ->latest()
            ->limit(5)
            ->get();

        // Occupancy by floor (rooms have floors, not buildings)
        $buildingStats = Room::selectRaw('floor, count(*) as total_rooms, sum(capacity) as total_capacity')
            ->groupBy('floor')
            ->orderBy('floor')
            ->get()
            ->map(function ($item) {
                $occupied = Seat::query()
                    ->whereHas('room', fn ($q) => $q->where('floor', $item->floor))
                    ->occupied()
                    ->count();
                $available = Seat::query()
                    ->whereHas('room', fn ($q) => $q->where('floor', $item->floor))
                    ->available()
                    ->count();

                $item->building = 'Floor '.$item->floor;
                $item->occupied = $occupied;
                $item->available = $available;
                $item->occupancy_rate = $item->total_capacity > 0
                    ? round(($occupied / $item->total_capacity) * 100, 2)
                    : 0;

                return $item;
            });

        // Today's meal count: students with meals active for today.
        $todayMeals = Meal::query()->whereDate('date', today())->where('meal_active', true);
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
