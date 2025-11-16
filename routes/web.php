<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;


// API Routes using web middleware (since api routes aren't working)
Route::prefix('api')->group(function () {
    // Dashboard
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('/attendance/today', [DashboardController::class, 'todayAttendance']);
    
    // Students
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/{student}', [StudentController::class, 'show']);
    Route::put('/students/{student}', [StudentController::class, 'update']);
    Route::delete('/students/{student}', [StudentController::class, 'destroy']);
    
    // Attendance
    Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore']);
    Route::get('/attendance/monthly-report', [AttendanceController::class, 'monthlyReport']);
});

// Vue.js SPA routes - MUST COME AFTER API ROUTES
Route::get('/', [DashboardController::class, 'index']);
Route::get('/students', [DashboardController::class, 'index']);
Route::get('/attendance', [DashboardController::class, 'index']);
Route::get('/{any}', [DashboardController::class, 'index'])->where('any', '.*');