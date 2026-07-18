<?php

use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiningController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoomChangeController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\SeatController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Student\PasswordResetController;
use App\Http\Controllers\Student\ProfileController;
use Illuminate\Support\Facades\Route;

// Welcome Route
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Admin Auth Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Auth
    Route::get('/change-password', [AuthController::class, 'changePasswordForm'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Students
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');

    // Rooms
    Route::resource('rooms', RoomController::class);

    // Seats
    Route::get('/seats/available', [SeatController::class, 'available'])->name('seats.available');
    Route::get('/seats/occupied', [SeatController::class, 'occupied'])->name('seats.occupied');
    Route::get('/seats/statistics', [SeatController::class, 'statistics'])->name('seats.statistics');
    Route::get('/seats/allocate', [SeatController::class, 'allocateForm'])->name('seats.allocate-form');
    Route::post('/seats/allocate', [SeatController::class, 'allocate'])->name('seats.allocate');
    Route::get('/seats/{seat}/vacate', [SeatController::class, 'vacateForm'])->name('seats.vacate-form');
    Route::post('/seats/{seat}/vacate', [SeatController::class, 'vacate'])->name('seats.vacate');
    Route::get('/seats/{seat}/transfer', [SeatController::class, 'transferForm'])->name('seats.transfer-form');
    Route::post('/seats/{seat}/transfer', [SeatController::class, 'transfer'])->name('seats.transfer');
    Route::get('/seats', [SeatController::class, 'index'])->name('seats.index');

    // Applications
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');

    // Room Change Requests
    Route::get('/room-changes', [RoomChangeController::class, 'index'])->name('room-changes.index');
    Route::get('/room-changes/{roomChange}', [RoomChangeController::class, 'show'])->name('room-changes.show');
    Route::post('/room-changes/{roomChange}/approve', [RoomChangeController::class, 'approve'])->name('room-changes.approve');
    Route::post('/room-changes/{roomChange}/reject', [RoomChangeController::class, 'reject'])->name('room-changes.reject');

    // Dining
    Route::get('/dining', [DiningController::class, 'index'])->name('dining.index');
    Route::get('/dining/attendance', [DiningController::class, 'attendance'])->name('dining.attendance');
    Route::post('/dining/attendance', [DiningController::class, 'storeAttendance'])->name('dining.attendance.store');
    Route::get('/dining/daily-count', [DiningController::class, 'dailyCount'])->name('dining.daily-count');
    Route::get('/dining/monthly-report', [DiningController::class, 'monthlyReport'])->name('dining.monthly-report');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/students', [ReportController::class, 'studentReport'])->name('reports.students');
    Route::get('/reports/room-occupancy', [ReportController::class, 'roomOccupancyReport'])->name('reports.room-occupancy');
    Route::get('/reports/dining', [ReportController::class, 'diningReport'])->name('reports.dining');
    Route::get('/reports/overview', [ReportController::class, 'dashboardOverview'])->name('reports.overview');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
});

/*
|--------------------------------------------------------------------------
| Student Auth Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->prefix('student')->name('student.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Student\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Student\AuthController::class, 'login']);

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Student Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {

    // Auth (accessible during first-login before password change)
    Route::get('/change-password', [App\Http\Controllers\Student\AuthController::class, 'changePasswordForm'])->name('change-password');
    Route::post('/change-password', [App\Http\Controllers\Student\AuthController::class, 'changePassword']);
    Route::post('/logout', [App\Http\Controllers\Student\AuthController::class, 'logout'])->name('logout');

    // Everything else requires the default password to have been changed
    Route::middleware('student.password-changed')->group(function () {

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Seat
        Route::get('/seat', [App\Http\Controllers\Student\SeatController::class, 'show'])->name('seat');

        // Applications
        Route::get('/applications', [App\Http\Controllers\Student\ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/create', [App\Http\Controllers\Student\ApplicationController::class, 'create'])->name('applications.create');
        Route::post('/applications', [App\Http\Controllers\Student\ApplicationController::class, 'store'])->name('applications.store');

        // Room Changes
        Route::get('/room-changes', [App\Http\Controllers\Student\RoomChangeController::class, 'index'])->name('room-changes.index');
        Route::get('/room-changes/create', [App\Http\Controllers\Student\RoomChangeController::class, 'create'])->name('room-changes.create');
        Route::post('/room-changes', [App\Http\Controllers\Student\RoomChangeController::class, 'store'])->name('room-changes.store');
        Route::get('/room-changes/{roomChange}', [App\Http\Controllers\Student\RoomChangeController::class, 'show'])->name('room-changes.show');

        // Dining
        Route::get('/dining/status', [App\Http\Controllers\Student\DiningController::class, 'status'])->name('dining.status');
        Route::post('/dining/toggle', [App\Http\Controllers\Student\DiningController::class, 'toggleMeal'])->name('dining.toggle');
        Route::post('/dining/preference', [App\Http\Controllers\Student\DiningController::class, 'updateMealPreference'])->name('dining.preference');
    });
});
