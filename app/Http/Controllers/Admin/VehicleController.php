<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\mVehicle;

class VehicleController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $vehicles = mVehicle::orderBy('txtVehicleName')->get();
        foreach ($vehicles as $v) {
            $v->remaining_seats = $v->getRemainingSeatsAttribute($today);
        }
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'txtVehicleName' => 'required|string|max:100',
            'txtPlateNumber' => 'required|string|max:20|unique:mVehicle,txtPlateNumber',
            'txtBrandModel' => 'required|string|max:100',
            'txtVehicleType' => 'required|string|max:50',
            'intMaxSeat' => 'required|integer|min:1|max:60',
            'intCurrentOdometer' => 'required|integer|min:0',
            'txtFuelType' => 'required|string|max:50',
        ]);

        mVehicle::create([
            'txtVehicleName' => $validated['txtVehicleName'],
            'txtPlateNumber' => strtoupper($validated['txtPlateNumber']),
            'txtBrandModel' => $validated['txtBrandModel'],
            'txtVehicleType' => $validated['txtVehicleType'],
            'intMaxSeat' => $validated['intMaxSeat'],
            'intCurrentOdometer' => $validated['intCurrentOdometer'],
            'txtFuelType' => $validated['txtFuelType'],
            'txtStatus' => 'AVAILABLE',
            'txtInsertedBy' => 'ADMIN_HC',
            'dtmInserted' => now(),
            'bitActive' => 1,
        ]);

        return redirect()->route('admin.vehicles.index')->with('success', "Kendaraan {$validated['txtVehicleName']} ({$validated['txtPlateNumber']}) berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $vehicle = mVehicle::findOrFail($id);

        $validated = $request->validate([
            'txtVehicleName' => 'required|string|max:100',
            'txtPlateNumber' => "required|string|max:20|unique:mVehicle,txtPlateNumber,{$id},intVehicle_ID",
            'txtBrandModel' => 'required|string|max:100',
            'txtVehicleType' => 'required|string|max:50',
            'intMaxSeat' => 'required|integer|min:1|max:60',
            'intCurrentOdometer' => 'required|integer|min:0',
            'txtFuelType' => 'required|string|max:50',
            'txtStatus' => 'required|in:AVAILABLE,IN_USE,MAINTENANCE',
            'bitActive' => 'required|in:0,1',
        ]);

        $vehicle->update([
            'txtVehicleName' => $validated['txtVehicleName'],
            'txtPlateNumber' => strtoupper($validated['txtPlateNumber']),
            'txtBrandModel' => $validated['txtBrandModel'],
            'txtVehicleType' => $validated['txtVehicleType'],
            'intMaxSeat' => $validated['intMaxSeat'],
            'intCurrentOdometer' => $validated['intCurrentOdometer'],
            'txtFuelType' => $validated['txtFuelType'],
            'txtStatus' => $validated['txtStatus'],
            'bitActive' => (int)$validated['bitActive'],
            'txtUpdatedBy' => 'ADMIN_HC',
            'dtmUpdated' => now(),
        ]);

        return redirect()->route('admin.vehicles.index')->with('success', "Data kendaraan {$vehicle->txtVehicleName} berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $vehicle = mVehicle::findOrFail($id);
        $vehicle->update([
            'bitActive' => 0,
            'txtUpdatedBy' => 'ADMIN_HC',
            'dtmUpdated' => now(),
        ]);
        return redirect()->route('admin.vehicles.index')->with('success', "Kendaraan {$vehicle->txtVehicleName} dinonaktifkan.");
    }
}
