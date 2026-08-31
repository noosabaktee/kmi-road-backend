<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DriverAuthController;
use App\Http\Controllers\Api\DriverTripController;
use App\Http\Controllers\Api\DriverTrackingController;
use App\Http\Controllers\Api\DriverDocumentationController;
use App\Http\Controllers\Employee\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes - KMI Road
|--------------------------------------------------------------------------
*/

// Public dynamic vehicle availability check for Employee Form
Route::get('/check-vehicles', [BookingController::class, 'checkVehicles']);

// Driver Mobile Authentication
Route::prefix('driver')->group(function () {
    Route::post('/login', [DriverAuthController::class, 'login']);

    // Protected Driver Endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [DriverAuthController::class, 'profile']);
        Route::post('/status', [DriverAuthController::class, 'updateStatus']);
        Route::post('/logout', [DriverAuthController::class, 'logout']);

        // Trip Management
        Route::get('/trips', [DriverTripController::class, 'index']);
        Route::get('/trips/{id}', [DriverTripController::class, 'show']);
        Route::post('/trips/{id}/start', [DriverTripController::class, 'start']);
        Route::post('/trips/{id}/arrived', [DriverTripController::class, 'arrived']);
        Route::post('/trips/{id}/complete', [DriverTripController::class, 'complete']);

        // GPS Telemetry Live Tracking (periodically called by mobile GPS service)
        Route::post('/location-update', [DriverTrackingController::class, 'updateLocation']);

        // Photo Documentation Checkpoint Upload (Pre-trip check, Fuel Refill with cost/liters, Arrived, Selesai)
        Route::post('/trips/{id}/documentation', [DriverDocumentationController::class, 'store']);
    });
});
