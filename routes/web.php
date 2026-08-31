<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\BookingController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LiveTrackingController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes - KMI Road
|--------------------------------------------------------------------------
*/

// Public Employee Portal (NO LOGIN REQUIRED)
Route::get('/', [BookingController::class, 'index'])->name('employee.form');
Route::get('/form-dinas', [BookingController::class, 'index'])->name('employee.form.alias');
Route::post('/form-dinas', [BookingController::class, 'store'])->name('employee.store');
Route::get('/form-dinas/sukses/{id}', [BookingController::class, 'success'])->name('employee.success');
Route::get('/cek-status', [BookingController::class, 'checkStatus'])->name('employee.status');

// Admin HC Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Routes
    Route::middleware('auth:web')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard.root');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Live Tracking Fleet Map
        Route::get('/live-tracking', [LiveTrackingController::class, 'index'])->name('admin.tracking');
        Route::get('/live-tracking-data', [LiveTrackingController::class, 'telemetryData'])->name('admin.tracking.data');

        // Trip Dispatcher & Management
        Route::get('/trips', [TripController::class, 'index'])->name('admin.trips.index');
        Route::get('/trips/create', [TripController::class, 'create'])->name('admin.trips.create');
        Route::post('/trips', [TripController::class, 'store'])->name('admin.trips.store');
        Route::get('/trips/{id}', [TripController::class, 'show'])->name('admin.trips.show');
        Route::post('/trips/assign', [TripController::class, 'assign'])->name('admin.trips.assign');
        Route::post('/trips/{id}/status', [TripController::class, 'updateStatus'])->name('admin.trips.status');

        // Vehicle Master & Settings
        Route::get('/vehicles', [VehicleController::class, 'index'])->name('admin.vehicles.index');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('admin.vehicles.store');
        Route::put('/vehicles/{id}', [VehicleController::class, 'update'])->name('admin.vehicles.update');
        Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy'])->name('admin.vehicles.destroy');

        // Driver Master & Accounts
        Route::get('/drivers', [DriverController::class, 'index'])->name('admin.drivers.index');
        Route::post('/drivers', [DriverController::class, 'store'])->name('admin.drivers.store');
        Route::put('/drivers/{id}', [DriverController::class, 'update'])->name('admin.drivers.update');
        Route::delete('/drivers/{id}', [DriverController::class, 'destroy'])->name('admin.drivers.destroy');

        // Reports & Export
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    });
});
