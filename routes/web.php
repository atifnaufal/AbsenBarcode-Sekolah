<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\FirebaseSessionController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'))->name('home');

// Railway healthcheck (see railway.toml -> healthcheckPath = /up)
Route::get('/up', fn () => response()->json(['status' => 'ok']))->name('health');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::post('/firebase/session', [FirebaseSessionController::class, 'store'])->name('firebase.session');

    // Registration Routes
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin_sekolah'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitor', [MonitorController::class, 'index'])->name('monitor');
    Route::get('/monitor/refresh', [MonitorController::class, 'refresh'])->name('monitor.refresh');
    Route::get('/monitor/recent', [MonitorController::class, 'recentScans'])->name('monitor.recent');
    Route::prefix('admin')->name('admin.')->group(function(){
        Route::get('/{role}/users', [UserController::class,'index'])->whereIn('role',['siswa','guru'])->name('users.index');
        Route::post('/{role}/users/toggle-registration', [UserController::class,'toggleRegistration'])->name('users.toggle-registration');
        Route::get('/{role}/users/create', [UserController::class,'create'])->name('users.create');
        Route::post('/{role}/users', [UserController::class,'store'])->name('users.store');
        Route::get('/{role}/users/{user}/edit', [UserController::class,'edit'])->name('users.edit');
        Route::put('/{role}/users/{user}', [UserController::class,'update'])->name('users.update');
        Route::delete('/{role}/users/{user}', [UserController::class,'destroy'])->name('users.destroy');
        Route::get('/laporan', [ReportController::class,'index'])->name('reports.index');
        Route::get('/laporan/export', [ReportController::class,'export'])->name('reports.export');
        Route::get('/lokasi', [LocationController::class,'edit'])->name('location.edit');
        Route::put('/lokasi', [LocationController::class,'update'])->name('location.update');

        // NEW: Attendance CRUD for Admin
        Route::put('/attendances/{attendance}', [\App\Http\Controllers\Admin\AttendanceController::class, 'update'])->name('attendances.update');
        Route::delete('/attendances/{attendance}', [\App\Http\Controllers\Admin\AttendanceController::class, 'destroy'])->name('attendances.destroy');

        // NEW: Schedule Management
        Route::get('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'store'])->name('schedules.store');
        Route::put('/schedules/{schedule}', [\App\Http\Controllers\Admin\ScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [\App\Http\Controllers\Admin\ScheduleController::class, 'destroy'])->name('schedules.destroy');
    });
});

Route::middleware(['auth', 'role:guru,siswa'])->group(function () {
    Route::get('/dashboard-siswa', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/scan', [StudentDashboardController::class, 'scan'])->name('attendance.scan');
    Route::post('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan.store');
    Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('student.profile');
    Route::put('/profile', [StudentDashboardController::class, 'updateProfile'])->name('student.profile.update');
});
