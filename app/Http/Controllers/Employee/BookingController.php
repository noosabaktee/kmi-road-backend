<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\mVehicle;
use App\Models\mDepartment;
use App\Models\trDutyTrip;
use App\Models\trDutyTrip_Details;

class BookingController extends Controller
{
    /**
     * Display the employee travel request form.
     */
    public function index()
    {
        $departments = mDepartment::where('bitActive', 1)->orderBy('txtDepartmentName')->get();
        $vehicles = mVehicle::where('bitActive', 1)->orderBy('txtVehicleName')->get();

        // Calculate current remaining seats for today
        $today = now()->toDateString();
        foreach ($vehicles as $v) {
            $v->remaining_seats = $v->getRemainingSeatsAttribute($today);
        }

        return view('employee.form', compact('departments', 'vehicles'));
    }

    /**
     * API to check available vehicles and dynamic seat availability for a specific date.
     */
    public function checkVehicles(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $vehicles = mVehicle::where('bitActive', 1)->get()->map(function ($vehicle) use ($date) {
            $remaining = $vehicle->getRemainingSeatsAttribute($date);
            return [
                'id' => $vehicle->intVehicle_ID,
                'name' => $vehicle->txtVehicleName,
                'plate' => $vehicle->txtPlateNumber,
                'brand_model' => $vehicle->txtBrandModel,
                'type' => $vehicle->txtVehicleType,
                'fuel' => $vehicle->txtFuelType,
                'max_seat' => $vehicle->intMaxSeat,
                'remaining_seats' => $remaining,
                'is_full' => $remaining <= 0,
                'status' => $vehicle->txtStatus,
            ];
        });

        return response()->json([
            'date' => $date,
            'vehicles' => $vehicles,
        ]);
    }

    /**
     * Submit employee booking form.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'txtEmployeeName' => 'required|string|max:100',
            'txtDepartment' => 'required|string|max:100',
            'txtPhoneNumber' => 'required|string|max:50',
            'dtmTripDate' => 'required|date|after_or_equal:today',
            'intRequestedVehicle_ID' => 'required|exists:mVehicle,intVehicle_ID',
            'txtDestination' => 'required|string|max:255',
            'txtPurpose' => 'required|string',
            'txtNotes' => 'nullable|string',
        ]);

        $vehicle = mVehicle::findOrFail($validated['intRequestedVehicle_ID']);
        $remaining = $vehicle->getRemainingSeatsAttribute($validated['dtmTripDate']);

        if ($remaining <= 0) {
            return back()->withInput()->withErrors([
                'intRequestedVehicle_ID' => "Maaf, kapasitas mobil {$vehicle->txtVehicleName} ({$vehicle->txtPlateNumber}) pada tanggal " . date('d M Y', strtotime($validated['dtmTripDate'])) . " sudah penuh ({$vehicle->intMaxSeat}/{$vehicle->intMaxSeat} Kursi). Silakan pilih kendaraan lain.",
            ]);
        }

        $booking = trDutyTrip_Details::create([
            'txtEmployeeName' => $validated['txtEmployeeName'],
            'txtDepartment' => $validated['txtDepartment'],
            'txtPhoneNumber' => $validated['txtPhoneNumber'],
            'dtmTripDate' => $validated['dtmTripDate'],
            'intRequestedVehicle_ID' => $vehicle->intVehicle_ID,
            'txtDestination' => $validated['txtDestination'],
            'txtPurpose' => $validated['txtPurpose'],
            'txtNotes' => $validated['txtNotes'] ?? null,
            'txtBookingStatus' => 'PENDING',
            'txtInsertedBy' => 'EMPLOYEE_' . strtoupper(substr(str_replace(' ', '', $validated['txtEmployeeName']), 0, 10)),
            'dtmInserted' => now(),
        ]);

        return redirect()->route('employee.success', ['id' => $booking->intDutyTrip_Detail_ID]);
    }

    /**
     * Show booking success page with tracking ticket.
     */
    public function success($id)
    {
        $booking = trDutyTrip_Details::with(['requestedVehicle', 'trip.driver', 'trip.vehicle'])->findOrFail($id);
        return view('employee.success', compact('booking'));
    }

    /**
     * Check status page for employees.
     */
    public function checkStatus(Request $request)
    {
        $search = $request->query('q');
        $results = null;

        if ($search) {
            $results = trDutyTrip_Details::with(['requestedVehicle', 'trip.driver', 'trip.vehicle'])
                ->where('txtEmployeeName', 'ilike', "%{$search}%")
                ->orWhere('txtPhoneNumber', 'like', "%{$search}%")
                ->orWhere('intDutyTrip_Detail_ID', is_numeric($search) ? (int)$search : 0)
                ->orderBy('dtmInserted', 'desc')
                ->get();
        }

        return view('employee.status', compact('search', 'results'));
    }
}
