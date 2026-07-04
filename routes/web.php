<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/change-password', [AdminAuthController::class, 'changePasswordForm'])->name('change-password');
    Route::post('/change-password', [AdminAuthController::class, 'changePassword']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
});

Route::middleware('guest')->prefix('student')->name('student.')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [StudentAuthController::class, 'login']);

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
});
Route::middleware('student.auth')->prefix('student')->name('student.')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Student\AuthController::class, 'logout'])->name('logout');
    
    Route::get('/room-changes', [App\Http\Controllers\Student\RoomChangeController::class, 'index'])->name('room-changes.index');
    Route::get('/room-changes/create', [App\Http\Controllers\Student\RoomChangeController::class, 'create'])->name('room-changes.create');
    Route::post('/room-changes', [App\Http\Controllers\Student\RoomChangeController::class, 'store'])->name('room-changes.store');

});
