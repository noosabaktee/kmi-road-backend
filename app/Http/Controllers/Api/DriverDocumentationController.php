<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\trDutyTrip_Documentations;
use App\Models\trDutyTrip;
use App\Models\logTripStatus;

class DriverDocumentationController extends Controller
{
    /**
     * Upload photo documentation checkpoint (Pre-trip, Fuel, Arrived, Selesai).
     */
    public function store(Request $request, $id)
    {
        $driver = $request->user();
        $trip = trDutyTrip::where('intDriver_ID', $driver->intDriver_ID)->findOrFail($id);

        $validated = $request->validate([
            'category' => 'required|in:SEBELUM_BERANGKAT,ISI_BBM,SAMPAI_TUJUAN,SELESAI',
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'odometer' => 'nullable|integer',
            'fuel_liters' => 'nullable|numeric|min:0',
            'fuel_cost' => 'nullable|numeric|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Upload photo file
        $photoPath = $request->file('photo')->store('documentations/' . date('Ym'), 'public');

        $doc = trDutyTrip_Documentations::create([
            'intDutyTrip_ID' => $trip->intDutyTrip_ID,
            'intDriver_ID' => $driver->intDriver_ID,
            'txtCategory' => $validated['category'],
            'txtPhotoPath' => $photoPath,
            'intOdometer' => $validated['odometer'] ?? null,
            'floatFuelLiters' => $validated['fuel_liters'] ?? null,
            'floatFuelCost' => $validated['fuel_cost'] ?? null,
            'floatLatitude' => $validated['latitude'] ?? null,
            'floatLongitude' => $validated['longitude'] ?? null,
            'txtLocationName' => $validated['location_name'] ?? null,
            'txtNotes' => $validated['notes'] ?? null,
            'txtInsertedBy' => 'DRIVER_' . $driver->intDriver_ID,
            'dtmInserted' => now(),
        ]);

        // Update trip fuel aggregates and odometer if applicable
        if (!empty($validated['fuel_cost']) || !empty($validated['fuel_liters'])) {
            $trip->increment('floatTotalFuelCost', (float)($validated['fuel_cost'] ?? 0));
            $trip->increment('floatTotalFuelLiters', (float)($validated['fuel_liters'] ?? 0));
        }

        if (!empty($validated['odometer'])) {
            if ($validated['category'] === 'SEBELUM_BERANGKAT' && empty($trip->intStartOdometer)) {
                $trip->update(['intStartOdometer' => $validated['odometer']]);
            } elseif ($validated['category'] === 'SELESAI') {
                $trip->update(['intEndOdometer' => $validated['odometer']]);
            }
        }

        // Status log
        $categoryLabel = match($validated['category']) {
            'SEBELUM_BERANGKAT' => 'Pengecekan Sebelum Berangkat',
            'ISI_BBM' => 'Pengisian BBM (' . ($validated['fuel_liters'] ?? '0') . ' L / Rp ' . number_format($validated['fuel_cost'] ?? 0, 0, ',', '.') . ')',
            'SAMPAI_TUJUAN' => 'Tiba di Lokasi Tujuan',
            'SELESAI' => 'Dokumentasi Selesai Perjalanan',
            default => $validated['category'],
        };

        logTripStatus::create([
            'intDutyTrip_ID' => $trip->intDutyTrip_ID,
            'txtPreviousStatus' => $trip->txtTripStatus,
            'txtNewStatus' => $trip->txtTripStatus,
            'txtActionNotes' => "Driver mengunggah bukti foto: {$categoryLabel}. " . ($validated['notes'] ?? ''),
            'txtInsertedBy' => 'DRIVER_' . $driver->intDriver_ID,
            'dtmInserted' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Bukti foto {$categoryLabel} berhasil diunggah!",
            'documentation' => [
                'id' => $doc->intDocumentation_ID,
                'category' => $doc->txtCategory,
                'photo_url' => asset('storage/' . $doc->txtPhotoPath),
                'odometer' => $doc->intOdometer,
                'fuel_liters' => $doc->floatFuelLiters,
                'fuel_cost' => $doc->floatFuelCost,
                'location_name' => $doc->txtLocationName,
                'notes' => $doc->txtNotes,
                'created_at' => $doc->dtmInserted->format('d M Y, H:i'),
            ],
        ]);
    }
}
