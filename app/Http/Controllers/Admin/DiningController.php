<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\DiningAttendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiningController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : today();

        $meals = Meal::with('student.user')
            ->where('date', $date)
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $breakfastCount = Meal::where('date', $date)->where('breakfast', true)->where('meal_active', true)->count();
        $lunchCount = Meal::where('date', $date)->where('lunch', true)->where('meal_active', true)->count();
        $dinnerCount = Meal::where('date', $date)->where('dinner', true)->where('meal_active', true)->count();

        $attendance = DiningAttendance::where('date', $date)
            ->selectRaw('meal_type, count(*) as count')
            ->where('present', true)
            ->groupBy('meal_type')
            ->pluck('count', 'meal_type');

        return view('admin.dining.index', compact(
            'meals', 'date', 'breakfastCount', 'lunchCount', 'dinnerCount', 'attendance'
        ));
    }

    public function attendance(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : today();
        $mealType = $request->filled('meal_type') ? $request->meal_type : 'lunch';

        $attendances = DiningAttendance::with('student.user')
            ->where('date', $date)
            ->where('meal_type', $mealType)
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $presentCount = DiningAttendance::where('date', $date)
            ->where('meal_type', $mealType)
            ->where('present', true)
            ->count();

        $absentCount = DiningAttendance::where('date', $date)
            ->where('meal_type', $mealType)
            ->where('present', false)
            ->count();

        return view('admin.dining.attendance', compact(
            'attendances', 'date', 'mealType', 'presentCount', 'absentCount'
        ));
    }

    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'present' => 'required|boolean',
            'time' => 'nullable',
        ]);

        DiningAttendance::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'date' => $validated['date'],
                'meal_type' => $validated['meal_type'],
            ],
            [
                'present' => $validated['present'],
                'time' => $validated['time'] ?? now()->format('H:i'),
            ]
        );

        return redirect()->back()->with('success', 'Attendance recorded.');
    }

    public function dailyCount(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : today();

        $breakfastCount = Meal::where('date', $date)->where('breakfast', true)->where('meal_active', true)->count();
        $lunchCount = Meal::where('date', $date)->where('lunch', true)->where('meal_active', true)->count();
        $dinnerCount = Meal::where('date', $date)->where('dinner', true)->where('meal_active', true)->count();

        $breakfastPresent = DiningAttendance::where('date', $date)->where('meal_type', 'breakfast')->where('present', true)->count();
        $lunchPresent = DiningAttendance::where('date', $date)->where('meal_type', 'lunch')->where('present', true)->count();
        $dinnerPresent = DiningAttendance::where('date', $date)->where('meal_type', 'dinner')->where('present', true)->count();

        return view('admin.dining.daily-count', compact(
            'date', 'breakfastCount', 'lunchCount', 'dinnerCount',
            'breakfastPresent', 'lunchPresent', 'dinnerPresent'
        ));
    }

    public function monthlyReport(Request $request)
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

        return view('admin.dining.monthly-report', compact(
            'month', 'dailyStats', 'daysInMonth', 'totalBreakfast', 'totalLunch', 'totalDinner'
        ));
    }
}
