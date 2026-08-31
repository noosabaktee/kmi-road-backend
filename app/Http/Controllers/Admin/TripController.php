<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\trDutyTrip;
use App\Models\trDutyTrip_Details;
use App\Models\trDutyTrip_Documentations;
use App\Models\mVehicle;
use App\Models\mDriver;
use App\Models\mDepartment;
use App\Models\logTripStatus;
use App\Models\dtLocationTracking;

class TripController extends Controller
{
    /**
     * Display listing of all trips and pending requests.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $date = $request->query('date');
        $search = $request->query('search');

        $query = trDutyTrip::with(['vehicle', 'driver', 'passengers', 'latestLocation'])
            ->orderBy('dtmTripDate', 'desc')
            ->orderBy('intDutyTrip_ID', 'desc');

        if ($status) {
            $query->where('txtTripStatus', $status);
        }

        if ($date) {
            $query->where('dtmTripDate', $date);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('txtTripCode', 'ilike', "%{$search}%")
                  ->orWhere('txtDestination', 'ilike', "%{$search}%")
                  ->orWhere('txtPurpose', 'ilike', "%{$search}%")
                  ->orWhereHas('passengers', function($qp) use ($search) {
                      $qp->where('txtEmployeeName', 'ilike', "%{$search}%");
                  });
            });
        }

        $trips = $query->paginate(15);

        // Pending unassigned employee submissions
        $pendingBookings = trDutyTrip_Details::with('requestedVehicle')
            ->where('txtBookingStatus', 'PENDING')
            ->whereNull('intDutyTrip_ID')
            ->orderBy('dtmTripDate', 'asc')
            ->get();

        $drivers = mDriver::where('bitActive', 1)->orderBy('txtDriverName')->get();
        $vehicles = mVehicle::where('bitActive', 1)->orderBy('txtVehicleName')->get();

        return view('admin.trips.index', compact('trips', 'pendingBookings', 'drivers', 'vehicles', 'status', 'date', 'search'));
    }

    /**
     * Show form to manually create a new direct trip schedule by Admin.
     */
    public function create()
    {
        $drivers = mDriver::where('bitActive', 1)->orderBy('txtDriverName')->get();
        $vehicles = mVehicle::where('bitActive', 1)->orderBy('txtVehicleName')->get();
        $departments = mDepartment::where('bitActive', 1)->orderBy('txtDepartmentName')->get();

        return view('admin.trips.create', compact('drivers', 'vehicles', 'departments'));
    }

    /**
     * Store new manual trip schedule created directly by Admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'intVehicle_ID' => 'required|exists:mVehicle,intVehicle_ID',
            'intDriver_ID' => 'required|exists:mDriver,intDriver_ID',
            'dtmTripDate' => 'required|date',
            'dtmDepartureTime' => 'nullable|date',
            'txtDestination' => 'required|string|max:255',
            'txtPurpose' => 'required|string',
            'txtNotes' => 'nullable|string',
            'passengers' => 'required|array|min:1',
            'passengers.*.name' => 'required|string|max:100',
            'passengers.*.dept' => 'required|string|max:100',
            'passengers.*.phone' => 'nullable|string|max:50',
            'passengers.*.nik' => 'nullable|string|max:50',
        ]);

        $vehicle = mVehicle::findOrFail($validated['intVehicle_ID']);
        $passengerCount = count($validated['passengers']);

        if ($passengerCount > $vehicle->intMaxSeat) {
            return back()->withInput()->withErrors([
                'passengers' => "Jumlah penumpang ({$passengerCount} orang) melebihi kapasitas maksimal mobil {$vehicle->txtVehicleName} ({$vehicle->intMaxSeat} Kursi).",
            ]);
        }

        DB::beginTransaction();
        try {
            $tripCode = 'TRIP-' . date('Ymd', strtotime($validated['dtmTripDate'])) . '-' . strtoupper(substr(uniqid(), -4));

            $trip = trDutyTrip::create([
                'txtTripCode' => $tripCode,
                'intVehicle_ID' => $validated['intVehicle_ID'],
                'intDriver_ID' => $validated['intDriver_ID'],
                'dtmTripDate' => $validated['dtmTripDate'],
                'dtmDepartureTime' => $validated['dtmDepartureTime'] ?? null,
                'txtDestination' => $validated['txtDestination'],
                'txtPurpose' => $validated['txtPurpose'],
                'txtTripStatus' => 'SCHEDULED',
                'intStartOdometer' => $vehicle->intCurrentOdometer,
                'txtNotes' => $validated['txtNotes'] ?? null,
                'txtInsertedBy' => 'ADMIN_HC',
                'dtmInserted' => now(),
            ]);

            foreach ($validated['passengers'] as $p) {
                trDutyTrip_Details::create([
                    'intDutyTrip_ID' => $trip->intDutyTrip_ID,
                    'txtEmployeeName' => $p['name'],
                    'txtEmployeeNIK' => $p['nik'] ?? null,
                    'txtDepartment' => $p['dept'],
                    'txtPhoneNumber' => $p['phone'] ?? null,
                    'dtmTripDate' => $validated['dtmTripDate'],
                    'intRequestedVehicle_ID' => $vehicle->intVehicle_ID,
                    'txtDestination' => $validated['txtDestination'],
                    'txtPurpose' => $validated['txtPurpose'],
                    'txtBookingStatus' => 'ASSIGNED',
                    'txtInsertedBy' => 'ADMIN_HC',
                    'dtmInserted' => now(),
                ]);
            }

            // Log status
            logTripStatus::create([
                'intDutyTrip_ID' => $trip->intDutyTrip_ID,
                'txtPreviousStatus' => null,
                'txtNewStatus' => 'SCHEDULED',
                'txtActionNotes' => 'Jadwal dinas langsung dibuat oleh Admin HC.',
                'txtInsertedBy' => 'ADMIN_HC',
                'dtmInserted' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.trips.show', $trip->intDutyTrip_ID)->with('success', "Jadwal dinas {$tripCode} berhasil dibuat dan ditugaskan!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal membuat jadwal dinas: ' . $e->getMessage()]);
        }
    }

    /**
     * Show comprehensive trip details with GPS route, photo checkpoint timeline, fuel logs, and passengers.
     */
    public function show($id)
    {
        $trip = trDutyTrip::with([
            'vehicle',
            'driver',
            'passengers',
            'documentations',
            'statusLogs',
            'latestLocation'
        ])->findOrFail($id);

        // Fetch GPS trail coordinates
        $gpsTrail = dtLocationTracking::where('intDutyTrip_ID', $trip->intDutyTrip_ID)
            ->orderBy('dtmTracked', 'asc')
            ->get();

        return view('admin.trips.show', compact('trip', 'gpsTrail'));
    }

    /**
     * Assign driver and vehicle to single or multiple pending employee booking requests.
     */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'booking_ids' => 'required|array|min:1',
            'booking_ids.*' => 'exists:trDutyTrip_Details,intDutyTrip_Detail_ID',
            'intVehicle_ID' => 'required|exists:mVehicle,intVehicle_ID',
            'intDriver_ID' => 'required|exists:mDriver,intDriver_ID',
            'dtmTripDate' => 'required|date',
            'dtmDepartureTime' => 'nullable|date',
            'txtNotes' => 'nullable|string',
        ]);

        $vehicle = mVehicle::findOrFail($validated['intVehicle_ID']);
        $bookings = trDutyTrip_Details::whereIn('intDutyTrip_Detail_ID', $validated['booking_ids'])->get();

        if ($bookings->count() > $vehicle->intMaxSeat) {
            return back()->withErrors([
                'error' => "Jumlah penumpang yang dipilih ({$bookings->count()} orang) melebihi kapasitas kursi ({$vehicle->intMaxSeat} Kursi).",
            ]);
        }

        DB::beginTransaction();
        try {
            $first = $bookings->first();
            $tripCode = 'TRIP-' . date('Ymd', strtotime($validated['dtmTripDate'])) . '-' . strtoupper(substr(uniqid(), -4));

            $trip = trDutyTrip::create([
                'txtTripCode' => $tripCode,
                'intVehicle_ID' => $validated['intVehicle_ID'],
                'intDriver_ID' => $validated['intDriver_ID'],
                'dtmTripDate' => $validated['dtmTripDate'],
                'dtmDepartureTime' => $validated['dtmDepartureTime'] ?? null,
                'txtDestination' => $first->txtDestination,
                'txtPurpose' => $first->txtPurpose,
                'txtTripStatus' => 'SCHEDULED',
                'intStartOdometer' => $vehicle->intCurrentOdometer,
                'txtNotes' => $validated['txtNotes'] ?? null,
                'txtInsertedBy' => 'ADMIN_HC',
                'dtmInserted' => now(),
            ]);

            foreach ($bookings as $b) {
                $b->update([
                    'intDutyTrip_ID' => $trip->intDutyTrip_ID,
                    'txtBookingStatus' => 'ASSIGNED',
                ]);
            }

            logTripStatus::create([
                'intDutyTrip_ID' => $trip->intDutyTrip_ID,
                'txtPreviousStatus' => 'PENDING',
                'txtNewStatus' => 'SCHEDULED',
                'txtActionNotes' => "Admin HC menugaskan Driver dan Kendaraan {$vehicle->txtPlateNumber} untuk " . $bookings->count() . " karyawan.",
                'txtInsertedBy' => 'ADMIN_HC',
                'dtmInserted' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.trips.show', $trip->intDutyTrip_ID)->with('success', "Penugasan dinas {$tripCode} berhasil diproses!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses penugasan: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel or Complete a trip.
     */
    public function updateStatus(Request $request, $id)
    {
        $trip = trDutyTrip::findOrFail($id);

        $validated = $request->validate([
            'txtTripStatus' => 'required|in:SCHEDULED,IN_PROGRESS,COMPLETED,CANCELLED',
            'txtActionNotes' => 'nullable|string',
        ]);

        $prev = $trip->txtTripStatus;
        $trip->update([
            'txtTripStatus' => $validated['txtTripStatus'],
            'dtmArrivalTime' => ($validated['txtTripStatus'] === 'COMPLETED') ? now() : $trip->dtmArrivalTime,
        ]);

        // Update passenger statuses
        if ($validated['txtTripStatus'] === 'COMPLETED') {
            trDutyTrip_Details::where('intDutyTrip_ID', $trip->intDutyTrip_ID)
                ->update(['txtBookingStatus' => 'COMPLETED']);
        } elseif ($validated['txtTripStatus'] === 'CANCELLED') {
            trDutyTrip_Details::where('intDutyTrip_ID', $trip->intDutyTrip_ID)
                ->update(['txtBookingStatus' => 'CANCELLED']);
        }

        logTripStatus::create([
            'intDutyTrip_ID' => $trip->intDutyTrip_ID,
            'txtPreviousStatus' => $prev,
            'txtNewStatus' => $validated['txtTripStatus'],
            'txtActionNotes' => $validated['txtActionNotes'] ?? 'Status diupdate oleh Admin HC.',
            'txtInsertedBy' => 'ADMIN_HC',
            'dtmInserted' => now(),
        ]);

        return back()->with('success', "Status perjalanan dinas diubah menjadi {$validated['txtTripStatus']}.");
    }
}
