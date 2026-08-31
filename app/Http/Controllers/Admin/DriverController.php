<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\mDriver;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = mDriver::withCount(['trips as total_trips', 'trips as active_trips' => function($q) {
            $q->where('txtTripStatus', 'IN_PROGRESS');
        }])->orderBy('txtDriverName')->get();

        return view('admin.drivers.index', compact('drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'txtDriverName' => 'required|string|max:100',
            'txtPhoneNumber' => 'required|string|max:50',
            'txtLicenseNumber' => 'required|string|max:50',
            'txtEmail' => 'required|email|max:150|unique:mDriver,txtEmail',
            'password' => 'required|string|min:6',
        ]);

        mDriver::create([
            'txtDriverName' => $validated['txtDriverName'],
            'txtPhoneNumber' => $validated['txtPhoneNumber'],
            'txtLicenseNumber' => $validated['txtLicenseNumber'],
            'txtEmail' => $validated['txtEmail'],
            'txtPassword' => Hash::make($validated['password']),
            'txtStatus' => 'AVAILABLE',
            'txtInsertedBy' => 'ADMIN_HC',
            'dtmInserted' => now(),
            'bitActive' => 1,
        ]);

        return redirect()->route('admin.drivers.index')->with('success', "Driver {$validated['txtDriverName']} berhasil didaftarkan untuk aplikasi mobile!");
    }

    public function update(Request $request, $id)
    {
        $driver = mDriver::findOrFail($id);

        $validated = $request->validate([
            'txtDriverName' => 'required|string|max:100',
            'txtPhoneNumber' => 'required|string|max:50',
            'txtLicenseNumber' => 'required|string|max:50',
            'txtEmail' => "required|email|max:150|unique:mDriver,txtEmail,{$id},intDriver_ID",
            'password' => 'nullable|string|min:6',
            'txtStatus' => 'required|in:AVAILABLE,ON_DUTY,OFF',
            'bitActive' => 'required|in:0,1',
        ]);

        $updateData = [
            'txtDriverName' => $validated['txtDriverName'],
            'txtPhoneNumber' => $validated['txtPhoneNumber'],
            'txtLicenseNumber' => $validated['txtLicenseNumber'],
            'txtEmail' => $validated['txtEmail'],
            'txtStatus' => $validated['txtStatus'],
            'bitActive' => (int)$validated['bitActive'],
            'txtUpdatedBy' => 'ADMIN_HC',
            'dtmUpdated' => now(),
        ];

        if (!empty($validated['password'])) {
            $updateData['txtPassword'] = Hash::make($validated['password']);
        }

        $driver->update($updateData);

        return redirect()->route('admin.drivers.index')->with('success', "Data driver {$driver->txtDriverName} berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $driver = mDriver::findOrFail($id);
        $driver->update([
            'bitActive' => 0,
            'txtUpdatedBy' => 'ADMIN_HC',
            'dtmUpdated' => now(),
        ]);
        return redirect()->route('admin.drivers.index')->with('success', "Akun driver {$driver->txtDriverName} dinonaktifkan.");
    }
}
