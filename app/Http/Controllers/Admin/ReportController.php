<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\trDutyTrip;
use App\Models\mVehicle;
use App\Models\mDriver;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->endOfMonth()->toDateString());
        $vehicleId = $request->query('vehicle_id');
        $driverId = $request->query('driver_id');
        $status = $request->query('status');

        $query = trDutyTrip::with(['vehicle', 'driver', 'passengers', 'documentations'])
            ->whereBetween('dtmTripDate', [$startDate, $endDate])
            ->orderBy('dtmTripDate', 'desc');

        if ($vehicleId) {
            $query->where('intVehicle_ID', $vehicleId);
        }

        if ($driverId) {
            $query->where('intDriver_ID', $driverId);
        }

        if ($status) {
            $query->where('txtTripStatus', $status);
        }

        $trips = $query->get();

        // Calculate aggregates
        $totalTrips = $trips->count();
        $completedTrips = $trips->where('txtTripStatus', 'COMPLETED')->count();
        $totalFuelCost = $trips->sum('floatTotalFuelCost');
        $totalFuelLiters = $trips->sum('floatTotalFuelLiters');
        $totalPassengers = $trips->sum(fn($t) => $t->passengers->count());

        $vehicles = mVehicle::where('bitActive', 1)->orderBy('txtVehicleName')->get();
        $drivers = mDriver::where('bitActive', 1)->orderBy('txtDriverName')->get();

        if ($request->query('export') === 'csv') {
            return $this->exportCsv($trips, $startDate, $endDate);
        }

        return view('admin.reports.index', compact(
            'trips',
            'totalTrips',
            'completedTrips',
            'totalFuelCost',
            'totalFuelLiters',
            'totalPassengers',
            'vehicles',
            'drivers',
            'startDate',
            'endDate',
            'vehicleId',
            'driverId',
            'status'
        ));
    }

    private function exportCsv($trips, $start, $end)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"Laporan_Dinas_KMI_Road_{$start}_{$end}.csv\"",
        ];

        $callback = function () use ($trips) {
            $file = fopen('php://output', 'w');
            // Header
            fputcsv($file, [
                'Kode Trip',
                'Tanggal Dinas',
                'Kendaraan (Nopol)',
                'Driver',
                'Tujuan Dinas',
                'Keperluan Dinas',
                'Status',
                'Jumlah Penumpang',
                'Daftar Penumpang',
                'Odometer Awal',
                'Odometer Akhir',
                'Total BBM (Liter)',
                'Biaya BBM (Rp)',
            ]);

            foreach ($trips as $t) {
                $passengersList = $t->passengers->map(fn($p) => "{$p->txtEmployeeName} ({$p->txtDepartment})")->implode(', ');

                fputcsv($file, [
                    $t->txtTripCode,
                    $t->dtmTripDate ? $t->dtmTripDate->format('Y-m-d') : '',
                    $t->vehicle ? "{$t->vehicle->txtVehicleName} ({$t->vehicle->txtPlateNumber})" : '-',
                    $t->driver ? $t->driver->txtDriverName : '-',
                    $t->txtDestination,
                    $t->txtPurpose,
                    $t->txtTripStatus,
                    $t->passengers->count(),
                    $passengersList,
                    $t->intStartOdometer ?? 0,
                    $t->intEndOdometer ?? 0,
                    $t->floatTotalFuelLiters ?? 0,
                    $t->floatTotalFuelCost ?? 0,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
