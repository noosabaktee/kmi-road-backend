<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\mVehicle;
use App\Models\mDriver;
use App\Models\trDutyTrip;
use App\Models\trDutyTrip_Details;
use App\Models\dtLocationTracking;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Key Fleet Metrics
        $totalVehicles = mVehicle::where('bitActive', 1)->count();
        $totalDrivers = mDriver::where('bitActive', 1)->count();
        $activeTripsCount = trDutyTrip::where('txtTripStatus', 'IN_PROGRESS')->count();
        $scheduledTripsCount = trDutyTrip::where('txtTripStatus', 'SCHEDULED')->count();
        $pendingSubmissionsCount = trDutyTrip_Details::where('txtBookingStatus', 'PENDING')->count();

        $passengersTodayCount = trDutyTrip_Details::where('dtmTripDate', $today)
            ->whereNotIn('txtBookingStatus', ['CANCELLED'])
            ->count();

        // Active Trips with current coordinates & drivers
        $activeTrips = trDutyTrip::with(['vehicle', 'driver', 'passengers', 'latestLocation'])
            ->whereIn('txtTripStatus', ['IN_PROGRESS', 'REFUELING', 'ARRIVED'])
            ->orderBy('dtmDepartureTime', 'desc')
            ->get();

        // Upcoming trips
        $upcomingTrips = trDutyTrip::with(['vehicle', 'driver', 'passengers'])
            ->where('txtTripStatus', 'SCHEDULED')
            ->orderBy('dtmTripDate', 'asc')
            ->limit(5)
            ->get();

        // Pending Employee Bookings waiting for dispatch
        $pendingBookings = trDutyTrip_Details::with('requestedVehicle')
            ->where('txtBookingStatus', 'PENDING')
            ->orderBy('dtmInserted', 'desc')
            ->limit(6)
            ->get();

        // Vehicle statuses
        $vehicles = mVehicle::where('bitActive', 1)->orderBy('txtVehicleName')->get();
        foreach ($vehicles as $v) {
            $v->remaining_seats = $v->getRemainingSeatsAttribute($today);
        }

        return view('admin.dashboard', compact(
            'totalVehicles',
            'totalDrivers',
            'activeTripsCount',
            'scheduledTripsCount',
            'pendingSubmissionsCount',
            'passengersTodayCount',
            'activeTrips',
            'upcomingTrips',
            'pendingBookings',
            'vehicles'
        ));
    }
}
