<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\dtLocationTracking;
use App\Models\trDutyTrip;

class DriverTrackingController extends Controller
{
    /**
     * Ingest Real-time GPS Location telemetry from Driver App.
     */
    public function updateLocation(Request $request)
    {
        $driver = $request->user();

        $validated = $request->validate([
            'trip_id' => 'required|exists:trDutyTrip,intDutyTrip_ID',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $trip = trDutyTrip::where('intDriver_ID', $driver->intDriver_ID)
            ->findOrFail($validated['trip_id']);

        $tracking = dtLocationTracking::create([
            'intDutyTrip_ID' => $trip->intDutyTrip_ID,
            'intDriver_ID' => $driver->intDriver_ID,
            'floatLatitude' => $validated['latitude'],
            'floatLongitude' => $validated['longitude'],
            'floatSpeed' => $validated['speed'] ?? 0,
            'floatHeading' => $validated['heading'] ?? 0,
            'floatAccuracy' => $validated['accuracy'] ?? 0,
            'dtmTracked' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Telemetry live location tersimpan.',
            'tracking_id' => $tracking->intTracking_ID,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
