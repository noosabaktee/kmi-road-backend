<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\trDutyTrip;
use App\Models\dtLocationTracking;

class LiveTrackingController extends Controller
{
    /**
     * Live Tracking Command Center View (Interactive Leaflet Map).
     */
    public function index(Request $request)
    {
        $selectedTripId = $request->query('trip_id');

        $activeTrips = trDutyTrip::with(['vehicle', 'driver', 'passengers', 'latestLocation'])
            ->whereIn('txtTripStatus', ['IN_PROGRESS', 'REFUELING', 'ARRIVED'])
            ->orderBy('dtmDepartureTime', 'desc')
            ->get();

        return view('admin.tracking.index', compact('activeTrips', 'selectedTripId'));
    }

    /**
     * Real-time GPS Telemetry Stream endpoint.
     */
    public function telemetryData(Request $request)
    {
        $activeTrips = trDutyTrip::with(['vehicle', 'driver', 'passengers', 'latestLocation'])
            ->whereIn('txtTripStatus', ['IN_PROGRESS', 'REFUELING', 'ARRIVED', 'SCHEDULED'])
            ->get()
            ->map(function ($trip) {
                $latest = $trip->latestLocation;
                
                // Fetch recent breadcrumbs (last 30 points) for route polyline
                $trail = dtLocationTracking::where('intDutyTrip_ID', $trip->intDutyTrip_ID)
                    ->orderBy('dtmTracked', 'asc')
                    ->take(50)
                    ->get()
                    ->map(fn($p) => [
                        'lat' => $p->floatLatitude,
                        'lng' => $p->floatLongitude,
                        'speed' => $p->floatSpeed,
                        'time' => $p->dtmTracked ? $p->dtmTracked->format('H:i:s') : null,
                    ]);

                return [
                    'trip_id' => $trip->intDutyTrip_ID,
                    'trip_code' => $trip->txtTripCode,
                    'status' => $trip->txtTripStatus,
                    'destination' => $trip->txtDestination,
                    'purpose' => $trip->txtPurpose,
                    'vehicle' => $trip->vehicle ? [
                        'name' => $trip->vehicle->txtVehicleName,
                        'plate' => $trip->vehicle->txtPlateNumber,
                        'model' => $trip->vehicle->txtBrandModel,
                    ] : null,
                    'driver' => $trip->driver ? [
                        'name' => $trip->driver->txtDriverName,
                        'phone' => $trip->driver->txtPhoneNumber,
                    ] : null,
                    'passenger_count' => $trip->passengers->count(),
                    'passengers' => $trip->passengers->map(fn($p) => [
                        'name' => $p->txtEmployeeName,
                        'dept' => $p->txtDepartment,
                    ]),
                    'current_location' => $latest ? [
                        'lat' => $latest->floatLatitude,
                        'lng' => $latest->floatLongitude,
                        'speed' => round($latest->floatSpeed, 1),
                        'heading' => round($latest->floatHeading, 1),
                        'updated_at' => $latest->dtmTracked ? $latest->dtmTracked->diffForHumans() : null,
                        'time' => $latest->dtmTracked ? $latest->dtmTracked->format('H:i:s') : null,
                    ] : null,
                    'trail' => $trail,
                ];
            });

        return response()->json([
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'active_count' => $activeTrips->count(),
            'trips' => $activeTrips,
        ]);
    }
}
