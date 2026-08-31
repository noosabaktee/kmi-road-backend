<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\mDriver;

class DriverAuthController extends Controller
{
    /**
     * Driver Login for Flutter App.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'txtEmail' => 'required|string',
            'password' => 'required|string',
        ]);

        $driver = mDriver::where(function($q) use ($validated) {
            $q->where('txtEmail', $validated['txtEmail'])
              ->orWhere('txtPhoneNumber', $validated['txtEmail']);
        })->where('bitActive', 1)->first();

        if (!$driver || !Hash::check($validated['password'], $driver->txtPassword)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email/No. HP atau kata sandi driver tidak valid.',
            ], 401);
        }

        // Revoke previous tokens
        $driver->tokens()->delete();

        // Create new sanctum token
        $token = $driver->createToken('kmi_road_driver_app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil. Selamat bertugas!',
            'token' => $token,
            'driver' => [
                'id' => $driver->intDriver_ID,
                'name' => $driver->txtDriverName,
                'phone' => $driver->txtPhoneNumber,
                'license' => $driver->txtLicenseNumber,
                'email' => $driver->txtEmail,
                'avatar' => $driver->txtAvatar,
                'status' => $driver->txtStatus,
            ],
        ]);
    }

    /**
     * Get current driver profile.
     */
    public function profile(Request $request)
    {
        $driver = $request->user();

        return response()->json([
            'status' => 'success',
            'driver' => [
                'id' => $driver->intDriver_ID,
                'name' => $driver->txtDriverName,
                'phone' => $driver->txtPhoneNumber,
                'license' => $driver->txtLicenseNumber,
                'email' => $driver->txtEmail,
                'avatar' => $driver->txtAvatar,
                'status' => $driver->txtStatus,
                'total_trips' => $driver->trips()->count(),
                'completed_trips' => $driver->trips()->where('txtTripStatus', 'COMPLETED')->count(),
            ],
        ]);
    }

    /**
     * Update driver status (AVAILABLE / OFF / ON_DUTY).
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:AVAILABLE,ON_DUTY,OFF',
        ]);

        $driver = $request->user();
        $driver->update([
            'txtStatus' => $validated['status'],
            'txtUpdatedBy' => 'DRIVER_' . $driver->intDriver_ID,
            'dtmUpdated' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status driver berhasil diubah.',
            'driver_status' => $driver->txtStatus,
        ]);
    }

    /**
     * Driver Logout.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil.',
        ]);
    }
}
