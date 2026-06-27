<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminHolidayController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\WebController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebController::class, 'home'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::get('/attendance/qr', [QRCodeController::class, 'show'])->name('attendance.qr');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');

    Route::middleware([RoleMiddleware::class.':admin'])->group(function () {
        Route::resource('employees', EmployeeController::class)->except(['show']);
        Route::resource('positions', PositionController::class)->except(['show']);
        Route::resource('shifts', ShiftController::class)->except(['show']);
        Route::resource('attendances', AttendanceController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::get('/admin/holidays', [AdminHolidayController::class, 'index'])->name('admin.holidays.index');
        Route::post('/admin/holidays', [AdminHolidayController::class, 'store'])->name('admin.holidays.store');
        Route::post('/admin/holidays/import', [AdminHolidayController::class, 'import'])->name('admin.holidays.import');
        Route::delete('/admin/holidays/{holiday}', [AdminHolidayController::class, 'destroy'])->name('admin.holidays.destroy');
        Route::resource('leave-requests', LeaveRequestController::class)->only(['edit', 'update', 'destroy']);
        Route::get('/reports/attendance/pdf', [AttendanceController::class, 'exportPdf'])->name('reports.attendance.pdf');
        Route::get('/reports/attendance/excel', [AttendanceController::class, 'exportExcel'])->name('reports.attendance.excel');
        Route::get('/reports/attendance', [AttendanceController::class, 'report'])->name('reports.attendance');
    });
});

// Public scan endpoint (used by QR code scanners)
Route::get('/attendance/scan/{token}', [QRCodeController::class, 'scan'])->name('attendance.scan');
Route::get('/s/{token}', [QRCodeController::class, 'scan'])->name('attendance.scan.short');

Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');

require __DIR__.'/auth.php';
