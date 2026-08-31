@extends('layouts.admin')

@section('title', 'Laporan Operasional Dinas & Rekap BBM | KMI Road')
@section('header_title', 'Laporan Operasional & Rekapitulasi BBM')

@section('content')
<div class="space-y-6">
    <!-- Filter Card -->
    <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 items-end text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Kendaraan</label>
                <select name="vehicle_id" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                    <option value="">Semua Kendaraan</option>
                    @foreach ($vehicles as $v)
                    <option value="{{ $v->intVehicle_ID }}" {{ $vehicleId == $v->intVehicle_ID ? 'selected' : '' }}>{{ $v->txtVehicleName }} ({{ $v->txtPlateNumber }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Driver</label>
                <select name="driver_id" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                    <option value="">Semua Driver</option>
                    @foreach ($drivers as $d)
                    <option value="{{ $d->intDriver_ID }}" {{ $driverId == $d->intDriver_ID ? 'selected' : '' }}>{{ $d->txtDriverName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <button type="submit" class="w-full sm:flex-1 py-2.5 px-4 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-900 transition-colors text-center">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-colors flex items-center justify-center space-x-1" title="Unduh File CSV / Excel">
                    <i class="fa-solid fa-file-csv text-sm"></i>
                    <span>Export CSV</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Perjalanan</span>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">{{ $totalTrips }} Dinas</h3>
            <p class="text-xs text-slate-500 mt-1">{{ $completedTrips }} Selesai</p>
        </div>

        <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Biaya BBM</span>
            <h3 class="text-xl sm:text-2xl font-extrabold text-kalbe-600 mt-1">Rp {{ number_format($totalFuelCost, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-500 mt-1">{{ round($totalFuelLiters, 2) }} Liter terisi</p>
        </div>

        <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Penumpang</span>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">{{ $totalPassengers }} Karyawan</h3>
            <p class="text-xs text-slate-500 mt-1">Terlayani dinas</p>
        </div>

        <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata-rata Konsumsi / Trip</span>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">
                {{ $totalTrips > 0 ? round($totalFuelLiters / $totalTrips, 1) : 0 }} Liter
            </h3>
            <p class="text-xs text-slate-500 mt-1">Rp {{ $totalTrips > 0 ? number_format($totalFuelCost / $totalTrips, 0, ',', '.') : 0 }} / trip</p>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
        <h3 class="font-extrabold text-sm sm:text-base text-slate-900 pb-3 border-b border-slate-100">Rekapitulasi Data Perjalanan Dinas</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 min-w-[850px]">
                <thead class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80 rounded-xl">
                    <tr>
                        <th class="p-3">Kode Trip</th>
                        <th class="p-3">Tgl Dinas</th>
                        <th class="p-3">Kendaraan</th>
                        <th class="p-3">Driver</th>
                        <th class="p-3">Tujuan</th>
                        <th class="p-3">Penumpang</th>
                        <th class="p-3">Odo Awal - Akhir</th>
                        <th class="p-3">BBM (Liter)</th>
                        <th class="p-3">Biaya BBM</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($trips as $t)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3 font-mono font-extrabold text-kalbe-700">
                            <a href="{{ route('admin.trips.show', $t->intDutyTrip_ID) }}" class="hover:underline">{{ $t->txtTripCode }}</a>
                        </td>
                        <td class="p-3 font-semibold text-slate-900">{{ $t->dtmTripDate ? $t->dtmTripDate->format('d M Y') : '-' }}</td>
                        <td class="p-3 font-medium text-slate-800">{{ $t->vehicle ? $t->vehicle->txtVehicleName . ' (' . $t->vehicle->txtPlateNumber . ')' : '-' }}</td>
                        <td class="p-3 font-bold text-slate-800">{{ $t->driver ? $t->driver->txtDriverName : '-' }}</td>
                        <td class="p-3 text-slate-800 max-w-[150px] truncate" title="{{ $t->txtDestination }}">{{ $t->txtDestination }}</td>
                        <td class="p-3 font-semibold text-slate-800">{{ $t->passengers->count() }} Org</td>
                        <td class="p-3 font-mono text-slate-700">{{ $t->intStartOdometer ?? '-' }} - {{ $t->intEndOdometer ?? '-' }}</td>
                        <td class="p-3 font-semibold text-slate-800">{{ $t->floatTotalFuelLiters ? round($t->floatTotalFuelLiters, 2) . ' L' : '-' }}</td>
                        <td class="p-3 font-bold text-kalbe-700">Rp {{ number_format($t->floatTotalFuelCost ?? 0, 0, ',', '.') }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">{{ $t->txtTripStatus }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="p-8 text-center text-slate-400">Tidak ada data dinas pada periode filter ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection