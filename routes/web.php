<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware('student.auth')->prefix('student')->name('student.')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Student\AuthController::class, 'logout'])->name('logout');
    
    Route::get('/room-changes', [App\Http\Controllers\Student\RoomChangeController::class, 'index'])->name('room-changes.index');
    Route::get('/room-changes/create', [App\Http\Controllers\Student\RoomChangeController::class, 'create'])->name('room-changes.create');
    Route::post('/room-changes', [App\Http\Controllers\Student\RoomChangeController::class, 'store'])->name('room-changes.store');

});
