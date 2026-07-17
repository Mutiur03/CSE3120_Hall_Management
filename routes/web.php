<?php

use Illuminate\Support\Facades\Route;


// Welcome Route
Route::get('/', function () {
    return redirect()->route('login');
});

// Admin Auth Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
});

// Admin Auth Routes (Authenticated)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Auth
    Route::get('/change-password', [App\Http\Controllers\Admin\AuthController::class, 'changePasswordForm'])->name('change-password');
    Route::post('/change-password', [App\Http\Controllers\Admin\AuthController::class, 'changePassword']);
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    // Students
    Route::resource('students', App\Http\Controllers\Admin\StudentController::class);
    Route::post('/students/{student}/toggle-status', [App\Http\Controllers\Admin\StudentController::class, 'toggleStatus'])->name('students.toggle-status');

    // Rooms
    Route::resource('rooms', App\Http\Controllers\Admin\RoomController::class);

    // Seats
    Route::get('/seats/available', [App\Http\Controllers\Admin\SeatController::class, 'available'])->name('seats.available');
    Route::get('/seats/occupied', [App\Http\Controllers\Admin\SeatController::class, 'occupied'])->name('seats.occupied');
    Route::get('/seats/statistics', [App\Http\Controllers\Admin\SeatController::class, 'statistics'])->name('seats.statistics');
    Route::get('/seats/allocate', [App\Http\Controllers\Admin\SeatController::class, 'allocateForm'])->name('seats.allocate-form');
    Route::post('/seats/allocate', [App\Http\Controllers\Admin\SeatController::class, 'allocate'])->name('seats.allocate');
    Route::get('/seats/{seat}/vacate', [App\Http\Controllers\Admin\SeatController::class, 'vacateForm'])->name('seats.vacate-form');
    Route::post('/seats/{seat}/vacate', [App\Http\Controllers\Admin\SeatController::class, 'vacate'])->name('seats.vacate');
    Route::get('/seats/{seat}/transfer', [App\Http\Controllers\Admin\SeatController::class, 'transferForm'])->name('seats.transfer-form');
    Route::post('/seats/{seat}/transfer', [App\Http\Controllers\Admin\SeatController::class, 'transfer'])->name('seats.transfer');
    Route::get('/seats', [App\Http\Controllers\Admin\SeatController::class, 'index'])->name('seats.index');

    // Applications
    Route::get('/applications', [App\Http\Controllers\Admin\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [App\Http\Controllers\Admin\ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/approve', [App\Http\Controllers\Admin\ApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [App\Http\Controllers\Admin\ApplicationController::class, 'reject'])->name('applications.reject');

    // Room Change Requests
    Route::get('/room-changes', [App\Http\Controllers\Admin\RoomChangeController::class, 'index'])->name('room-changes.index');
    Route::get('/room-changes/{roomChange}', [App\Http\Controllers\Admin\RoomChangeController::class, 'show'])->name('room-changes.show');
    Route::post('/room-changes/{roomChange}/approve', [App\Http\Controllers\Admin\RoomChangeController::class, 'approve'])->name('room-changes.approve');
    Route::post('/room-changes/{roomChange}/reject', [App\Http\Controllers\Admin\RoomChangeController::class, 'reject'])->name('room-changes.reject');

    // Dining
    Route::get('/dining', [App\Http\Controllers\Admin\DiningController::class, 'index'])->name('dining.index');
    Route::get('/dining/attendance', [App\Http\Controllers\Admin\DiningController::class, 'attendance'])->name('dining.attendance');
    Route::post('/dining/attendance', [App\Http\Controllers\Admin\DiningController::class, 'storeAttendance'])->name('dining.attendance.store');
    Route::get('/dining/daily-count', [App\Http\Controllers\Admin\DiningController::class, 'dailyCount'])->name('dining.daily-count');
    Route::get('/dining/monthly-report', [App\Http\Controllers\Admin\DiningController::class, 'monthlyReport'])->name('dining.monthly-report');

    // Reports
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/students', [App\Http\Controllers\Admin\ReportController::class, 'studentReport'])->name('reports.students');
    Route::get('/reports/room-occupancy', [App\Http\Controllers\Admin\ReportController::class, 'roomOccupancyReport'])->name('reports.room-occupancy');
    Route::get('/reports/dining', [App\Http\Controllers\Admin\ReportController::class, 'diningReport'])->name('reports.dining');
    Route::get('/reports/overview', [App\Http\Controllers\Admin\ReportController::class, 'dashboardOverview'])->name('reports.overview');

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/clear-cache', [App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('settings.clear-cache');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/

// Student Auth Routes (Guest)
Route::middleware('guest:student')->group(function () {
    Route::get('/student/login', [App\Http\Controllers\Student\AuthController::class, 'showLoginForm'])->name('student.login');
    Route::post('/student/login', [App\Http\Controllers\Student\AuthController::class, 'login']);
});

// Student Auth Routes (Authenticated)
Route::middleware('student.auth')->prefix('student')->name('student.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');

    // Auth
    Route::get('/change-password', [App\Http\Controllers\Student\AuthController::class, 'changePasswordForm'])->name('change-password');
    Route::post('/change-password', [App\Http\Controllers\Student\AuthController::class, 'changePassword']);
    Route::post('/logout', [App\Http\Controllers\Student\AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile');

    // Applications
    Route::get('/applications', [App\Http\Controllers\Student\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/create', [App\Http\Controllers\Student\ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications', [App\Http\Controllers\Student\ApplicationController::class, 'store'])->name('applications.store');

    // Room Changes
    Route::get('/room-changes', [App\Http\Controllers\Student\RoomChangeController::class, 'index'])->name('room-changes.index');
    Route::get('/room-changes/create', [App\Http\Controllers\Student\RoomChangeController::class, 'create'])->name('room-changes.create');
    Route::post('/room-changes', [App\Http\Controllers\Student\RoomChangeController::class, 'store'])->name('room-changes.store');

    // Dining
    Route::get('/dining/status', [App\Http\Controllers\Student\DiningController::class, 'status'])->name('dining.status');
    Route::post('/dining/toggle', [App\Http\Controllers\Student\DiningController::class, 'toggleMeal'])->name('dining.toggle');
    Route::post('/dining/preference', [App\Http\Controllers\Student\DiningController::class, 'updateMealPreference'])->name('dining.preference');
});
