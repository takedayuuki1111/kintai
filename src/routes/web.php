<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminStaffController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [AttendanceController::class, 'startWork'])->name('attendance.start');
    Route::post('/attendance/end', [AttendanceController::class, 'endWork'])->name('attendance.end');
    Route::post('/attendance/rest/start', [AttendanceController::class, 'startRest'])->name('attendance.rest.start');
    Route::post('/attendance/rest/end', [AttendanceController::class, 'endRest'])->name('attendance.rest.end');
    Route::get('/attendance/list', [AttendanceController::class, 'monthlyList'])->name('attendance.list');
    Route::post('/attendance/correction', [StampCorrectionRequestController::class, 'store'])->name('correction.store'); 
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])->name('correction.list');

        Route::get('/attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');


Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store'])->name('login.store');

    Route::middleware(['auth:admin'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.list');
        Route::put('/attendance/{attendance}', [AdminAttendanceController::class, 'update'])->name('attendance.update');
        
        Route::get('/staff', [AdminStaffController::class, 'index'])->name('staff.list');
        Route::get('/staff/{id}/attendance', [AdminStaffController::class, 'show'])->name('staff.attendance');
        Route::get('/staff/{id}/attendance/export', [AdminStaffController::class, 'exportCsv'])->name('staff.attendance.export');

        Route::get('/stamp_correction_requests', [StampCorrectionRequestController::class, 'adminIndex'])->name('correction.list');
        Route::get('/stamp_correction_requests/{attendance_correct_request_id}/approve', [StampCorrectionRequestController::class, 'approveShow'])->name('correction.approve.show');
        Route::post('/stamp_correction_requests/{attendance_correct_request_id}/approve', [StampCorrectionRequestController::class, 'approve'])->name('correction.approve');

        Route::get('/attendance/{attendance}', [AdminAttendanceController::class, 'show'])->name('attendance.show');
    });
});