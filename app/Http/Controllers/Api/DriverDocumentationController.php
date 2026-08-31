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
     * Upload or Update photo documentation checkpoint (Pre-trip, Fuel, Arrived, Selesai).
     */
    public function store(Request $request, $id)
    {
        $driver = $request->user();
        $trip = trDutyTrip::where('intDriver_ID', $driver->intDriver_ID)->findOrFail($id);

        $validated = $request->validate([
            'category' => 'required|in:SEBELUM_BERANGKAT,ISI_BBM,SAMPAI_TUJUAN,SELESAI',
            'documentation_id' => 'nullable|integer',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'odometer' => 'nullable|integer',
            'fuel_liters' => 'nullable|numeric|min:0',
            'fuel_cost' => 'nullable|numeric|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $category = $validated['category'];

        // Determine if updating an existing record
        $doc = null;
        if (!empty($validated['documentation_id'])) {
            $doc = trDutyTrip_Documentations::where('intDutyTrip_ID', $trip->intDutyTrip_ID)->find($validated['documentation_id']);
        } elseif (in_array($category, ['SEBELUM_BERANGKAT', 'SAMPAI_TUJUAN', 'SELESAI'])) {
            $doc = trDutyTrip_Documentations::where('intDutyTrip_ID', $trip->intDutyTrip_ID)
                ->where('txtCategory', $category)
                ->first();
        }

        if (!$doc) {
            // Must have photo for new documentation
            if (!$request->hasFile('photo')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Foto bukti dokumentasi wajib diambil/diunggah.',
                ], 422);
            }

            $photoPath = $request->file('photo')->store('documentations/' . date('Ym'), 'public');

            $doc = trDutyTrip_Documentations::create([
                'intDutyTrip_ID' => $trip->intDutyTrip_ID,
                'intDriver_ID' => $driver->intDriver_ID,
                'txtCategory' => $category,
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
        } else {
            // Update existing record
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('documentations/' . date('Ym'), 'public');
                $doc->txtPhotoPath = $photoPath;
            }

            if (array_key_exists('odometer', $validated)) $doc->intOdometer = $validated['odometer'];
            if (array_key_exists('fuel_liters', $validated)) $doc->floatFuelLiters = $validated['fuel_liters'];
            if (array_key_exists('fuel_cost', $validated)) $doc->floatFuelCost = $validated['fuel_cost'];
            if (array_key_exists('latitude', $validated)) $doc->floatLatitude = $validated['latitude'];
            if (array_key_exists('longitude', $validated)) $doc->floatLongitude = $validated['longitude'];
            if (array_key_exists('location_name', $validated)) $doc->txtLocationName = $validated['location_name'];
            if (array_key_exists('notes', $validated)) $doc->txtNotes = $validated['notes'];
            $doc->save();
        }

        // If category is SAMPAI_TUJUAN, automatically update trip status to ARRIVED
        if ($category === 'SAMPAI_TUJUAN' && $trip->txtTripStatus !== 'ARRIVED' && $trip->txtTripStatus !== 'COMPLETED') {
            $prevStatus = $trip->txtTripStatus;
            $trip->update(['txtTripStatus' => 'ARRIVED']);

            logTripStatus::create([
                'intDutyTrip_ID' => $trip->intDutyTrip_ID,
                'txtPreviousStatus' => $prevStatus,
                'txtNewStatus' => 'ARRIVED',
                'txtActionNotes' => 'Status otomatis diubah ke ARRIVED saat driver mengunggah bukti tiba di lokasi.',
                'txtInsertedBy' => 'DRIVER_' . $driver->intDriver_ID,
                'dtmInserted' => now(),
            ]);
        }

        // Recalculate trip total fuel cost & liters from all fuel documentations
        if ($category === 'ISI_BBM') {
            $totalFuelCost = trDutyTrip_Documentations::where('intDutyTrip_ID', $trip->intDutyTrip_ID)
                ->where('txtCategory', 'ISI_BBM')
                ->sum('floatFuelCost');
            $totalFuelLiters = trDutyTrip_Documentations::where('intDutyTrip_ID', $trip->intDutyTrip_ID)
                ->where('txtCategory', 'ISI_BBM')
                ->sum('floatFuelLiters');

            $trip->update([
                'floatTotalFuelCost' => $totalFuelCost,
                'floatTotalFuelLiters' => $totalFuelLiters,
            ]);
        }

        // Update trip start/end odometer if applicable
        if (!empty($validated['odometer'])) {
            if ($category === 'SEBELUM_BERANGKAT') {
                $trip->update(['intStartOdometer' => $validated['odometer']]);
            } elseif ($category === 'SELESAI') {
                $trip->update(['intEndOdometer' => $validated['odometer']]);
            }
        }

        // Status log
        $categoryLabel = match ($category) {
            'SEBELUM_BERANGKAT' => 'Pengecekan Sebelum Berangkat',
            'ISI_BBM' => 'Pengisian BBM (' . ($validated['fuel_liters'] ?? '0') . ' L / Rp ' . number_format($validated['fuel_cost'] ?? 0, 0, ',', '.') . ')',
            'SAMPAI_TUJUAN' => 'Tiba di Lokasi Tujuan',
            'SELESAI' => 'Dokumentasi Selesai Perjalanan',
            default => $category,
        };

        logTripStatus::create([
            'intDutyTrip_ID' => $trip->intDutyTrip_ID,
            'txtPreviousStatus' => $trip->txtTripStatus,
            'txtNewStatus' => $trip->txtTripStatus,
            'txtActionNotes' => "Driver menyimpan bukti: {$categoryLabel}. " . ($validated['notes'] ?? ''),
            'txtInsertedBy' => 'DRIVER_' . $driver->intDriver_ID,
            'dtmInserted' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Bukti {$categoryLabel} berhasil disimpan!",
            'trip_status' => $trip->fresh()->txtTripStatus,
            'documentation' => [
                'id' => $doc->intDocumentation_ID,
                'category' => $doc->txtCategory,
                'photo_url' => str_starts_with($doc->txtPhotoPath, 'http') ? $doc->txtPhotoPath : asset('storage/' . $doc->txtPhotoPath),
                'odometer' => $doc->intOdometer,
                'fuel_liters' => $doc->floatFuelLiters,
                'fuel_cost' => $doc->floatFuelCost,
                'location_name' => $doc->txtLocationName,
                'notes' => $doc->txtNotes,
                'created_at' => $doc->dtmInserted ? $doc->dtmInserted->format('d M Y, H:i') : null,
            ],
        ]);
    }

    /**
     * Delete a documentation record (e.g., fuel entry).
     */
    public function destroy(Request $request, $id, $docId)
    {
        $driver = $request->user();
        $trip = trDutyTrip::where('intDriver_ID', $driver->intDriver_ID)->findOrFail($id);
        $doc = trDutyTrip_Documentations::where('intDutyTrip_ID', $trip->intDutyTrip_ID)->findOrFail($docId);

        $category = $doc->txtCategory;
        if ($doc->txtPhotoPath && !str_starts_with($doc->txtPhotoPath, 'http')) {
            Storage::disk('public')->delete($doc->txtPhotoPath);
        }
        $doc->delete();

        if ($category === 'ISI_BBM') {
            $totalFuelCost = trDutyTrip_Documentations::where('intDutyTrip_ID', $trip->intDutyTrip_ID)
                ->where('txtCategory', 'ISI_BBM')
                ->sum('floatFuelCost');
            $totalFuelLiters = trDutyTrip_Documentations::where('intDutyTrip_ID', $trip->intDutyTrip_ID)
                ->where('txtCategory', 'ISI_BBM')
                ->sum('floatFuelLiters');

            $trip->update([
                'floatTotalFuelCost' => $totalFuelCost,
                'floatTotalFuelLiters' => $totalFuelLiters,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Dokumentasi berhasil dihapus.',
        ]);
    }
}
