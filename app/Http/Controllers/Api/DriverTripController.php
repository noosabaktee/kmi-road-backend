<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\trDutyTrip;
use App\Models\trDutyTrip_Details;
use App\Models\logTripStatus;
use App\Models\mVehicle;

class DriverTripController extends Controller
{
    /**
     * List assigned trips for authenticated driver.
     */
    public function index(Request $request)
    {
        $driver = $request->user();

        // Active Trip (Currently IN_PROGRESS, REFUELING, or ARRIVED)
        $activeTrip = trDutyTrip::with(['vehicle', 'passengers', 'documentations'])
            ->where('intDriver_ID', $driver->intDriver_ID)
            ->whereIn('txtTripStatus', ['IN_PROGRESS', 'REFUELING', 'ARRIVED'])
            ->latest('dtmDepartureTime')
            ->first();

        // Upcoming Scheduled Trips
        $upcomingTrips = trDutyTrip::with(['vehicle', 'passengers'])
            ->where('intDriver_ID', $driver->intDriver_ID)
            ->where('txtTripStatus', 'SCHEDULED')
            ->orderBy('dtmTripDate', 'asc')
            ->get();

        // Completed History Trips
        $completedTrips = trDutyTrip::with(['vehicle', 'passengers'])
            ->where('intDriver_ID', $driver->intDriver_ID)
            ->where('txtTripStatus', 'COMPLETED')
            ->orderBy('dtmTripDate', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'active_trip' => $activeTrip ? $this->formatTrip($activeTrip) : null,
            'upcoming_trips' => $upcomingTrips->map(fn($t) => $this->formatTrip($t)),
            'completed_trips' => $completedTrips->map(fn($t) => $this->formatTrip($t)),
        ]);
    }

    /**
     * Get single trip detail for driver.
     */
    public function show(Request $request, $id)
    {
        $driver = $request->user();

        $trip = trDutyTrip::with(['vehicle', 'passengers', 'documentations'])
            ->where('intDriver_ID', $driver->intDriver_ID)
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'trip' => $this->formatTrip($trip, true),
        ]);
    }

    /**
     * Start Trip (Driver clicks "Mulai Perjalanan").
     */
    public function start(Request $request, $id)
    {
        $driver = $request->user();
        $trip = trDutyTrip::where('intDriver_ID', $driver->intDriver_ID)->findOrFail($id);

        $validated = $request->validate([
            'start_odometer' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $prevStatus = $trip->txtTripStatus;
        $trip->update([
            'txtTripStatus' => 'IN_PROGRESS',
            'dtmDepartureTime' => $trip->dtmDepartureTime ?? now(),
            'intStartOdometer' => $validated['start_odometer'] ?? ($trip->vehicle ? $trip->vehicle->intCurrentOdometer : null),
        ]);

        // Update driver status to ON_DUTY
        $driver->update(['txtStatus' => 'ON_DUTY']);

        // Update vehicle status to IN_USE
        if ($trip->vehicle) {
            $trip->vehicle->update(['txtStatus' => 'IN_USE']);
        }

        logTripStatus::create([
            'intDutyTrip_ID' => $trip->intDutyTrip_ID,
            'txtPreviousStatus' => $prevStatus,
            'txtNewStatus' => 'IN_PROGRESS',
            'txtActionNotes' => 'Driver memulai perjalanan dinas via aplikasi mobile. ' . ($validated['notes'] ?? ''),
            'txtInsertedBy' => 'DRIVER_' . $driver->intDriver_ID,
            'dtmInserted' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Perjalanan dinas telah dimulai. Live location tracking diaktifkan!',
            'trip' => $this->formatTrip($trip->fresh(['vehicle', 'passengers', 'documentations'])),
        ]);
    }

    /**
     * Mark Arrived at Destination.
     */
    public function arrived(Request $request, $id)
    {
        $driver = $request->user();
        $trip = trDutyTrip::where('intDriver_ID', $driver->intDriver_ID)->findOrFail($id);

        $prevStatus = $trip->txtTripStatus;
        $trip->update([
            'txtTripStatus' => 'ARRIVED',
        ]);

        logTripStatus::create([
            'intDutyTrip_ID' => $trip->intDutyTrip_ID,
            'txtPreviousStatus' => $prevStatus,
            'txtNewStatus' => 'ARRIVED',
            'txtActionNotes' => 'Driver mengonfirmasi telah sampai di lokasi tujuan dinas.',
            'txtInsertedBy' => 'DRIVER_' . $driver->intDriver_ID,
            'dtmInserted' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status berhasil diubah: Sampai di Lokasi Tujuan.',
            'trip' => $this->formatTrip($trip->fresh(['vehicle', 'passengers', 'documentations'])),
        ]);
    }

    /**
     * Complete Trip (Driver clicks "Selesaikan Perjalanan").
     */
    public function complete(Request $request, $id)
    {
        $driver = $request->user();
        $trip = trDutyTrip::where('intDriver_ID', $driver->intDriver_ID)->findOrFail($id);

        $validated = $request->validate([
            'end_odometer' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $prevStatus = $trip->txtTripStatus;
        $endOdo = $validated['end_odometer'] ?? ($trip->intStartOdometer ? $trip->intStartOdometer + 35 : null);

        $trip->update([
            'txtTripStatus' => 'COMPLETED',
            'dtmArrivalTime' => now(),
            'intEndOdometer' => $endOdo,
            'txtNotes' => $validated['notes'] ?? $trip->txtNotes,
        ]);

        // Update vehicle odometer & status
        if ($trip->vehicle && $endOdo) {
            $trip->vehicle->update([
                'intCurrentOdometer' => max($trip->vehicle->intCurrentOdometer, $endOdo),
                'txtStatus' => 'AVAILABLE',
            ]);
        }

        // Update driver status back to AVAILABLE
        $driver->update(['txtStatus' => 'AVAILABLE']);

        // Update passenger status
        trDutyTrip_Details::where('intDutyTrip_ID', $trip->intDutyTrip_ID)
            ->update(['txtBookingStatus' => 'COMPLETED']);

        logTripStatus::create([
            'intDutyTrip_ID' => $trip->intDutyTrip_ID,
            'txtPreviousStatus' => $prevStatus,
            'txtNewStatus' => 'COMPLETED',
            'txtActionNotes' => 'Perjalanan dinas selesai. Odometer akhir: ' . ($endOdo ?? '-') . ' KM.',
            'txtInsertedBy' => 'DRIVER_' . $driver->intDriver_ID,
            'dtmInserted' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Perjalanan dinas telah selesai. Terima kasih atas tugasnya!',
            'trip' => $this->formatTrip($trip->fresh(['vehicle', 'passengers', 'documentations'])),
        ]);
    }

    private function formatTrip($trip, $includeDocs = true)
    {
        return [
            'id' => $trip->intDutyTrip_ID,
            'trip_code' => $trip->txtTripCode,
            'status' => $trip->txtTripStatus,
            'trip_date' => $trip->dtmTripDate ? $trip->dtmTripDate->format('Y-m-d') : null,
            'trip_date_formatted' => $trip->dtmTripDate ? $trip->dtmTripDate->format('d M Y') : null,
            'departure_time' => $trip->dtmDepartureTime ? $trip->dtmDepartureTime->format('H:i') : null,
            'destination' => $trip->txtDestination,
            'purpose' => $trip->txtPurpose,
            'notes' => $trip->txtNotes,
            'start_odometer' => $trip->intStartOdometer,
            'end_odometer' => $trip->intEndOdometer,
            'total_fuel_liters' => $trip->floatTotalFuelLiters,
            'total_fuel_cost' => $trip->floatTotalFuelCost,
            'vehicle' => $trip->vehicle ? [
                'id' => $trip->vehicle->intVehicle_ID,
                'name' => $trip->vehicle->txtVehicleName,
                'plate_number' => $trip->vehicle->txtPlateNumber,
                'brand_model' => $trip->vehicle->txtBrandModel,
                'fuel_type' => $trip->vehicle->txtFuelType,
                'max_seat' => $trip->vehicle->intMaxSeat,
                'odometer' => $trip->vehicle->intCurrentOdometer,
            ] : null,
            'passengers' => $trip->passengers->map(fn($p) => [
                'id' => $p->intDutyTrip_Detail_ID,
                'name' => $p->txtEmployeeName,
                'nik' => $p->txtEmployeeNIK,
                'department' => $p->txtDepartment,
                'phone' => $p->txtPhoneNumber,
                'purpose' => $p->txtPurpose,
            ]),
            'documentations' => $includeDocs ? $trip->documentations->map(fn($d) => [
                'id' => $d->intDocumentation_ID,
                'category' => $d->txtCategory,
                'photo_url' => str_starts_with($d->txtPhotoPath, 'http') ? $d->txtPhotoPath : asset('storage/' . $d->txtPhotoPath),
                'odometer' => $d->intOdometer,
                'fuel_liters' => $d->floatFuelLiters,
                'fuel_cost' => $d->floatFuelCost,
                'location_name' => $d->txtLocationName,
                'notes' => $d->txtNotes,
                'created_at' => $d->dtmInserted ? $d->dtmInserted->format('d M Y, H:i') : null,
            ]) : [],
        ];
    }
}
