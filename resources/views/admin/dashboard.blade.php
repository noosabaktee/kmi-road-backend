@extends('layouts.admin')

@section('title', 'Dashboard Monitoring Fleet & Dinas | KMI Road')
@section('header_title', 'Dashboard Monitoring & Dispatcher')

@section('content')
<div class="space-y-6 sm:space-y-8">
    <!-- Top Fleet Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Metric 1: Mobil Sedang Dinas (Live) -->
        <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Mobil Sedang Dinas</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $activeTripsCount }}</h3>
                <div class="flex items-center space-x-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-[11px] sm:text-xs font-semibold text-emerald-600">Live GPS Tracking</span>
                </div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-100 text-kalbe-600 flex items-center justify-center text-lg sm:text-xl shadow-inner flex-shrink-0">
                <i class="fa-solid fa-car-side"></i>
            </div>
        </div>

        <!-- Metric 2: Penumpang Hari Ini -->
        <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Penumpang Hari Ini</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $passengersTodayCount }} <span class="text-xs font-medium text-slate-500">Orang</span></h3>
                <p class="text-[11px] sm:text-xs font-medium text-slate-400 mt-2">Total karyawan dinas</p>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg sm:text-xl shadow-inner flex-shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Metric 3: Pengajuan Menunggu Dispatch -->
        <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Pengajuan Menunggu</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold {{ $pendingSubmissionsCount > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1">
                    {{ $pendingSubmissionsCount }}
                </h3>
                <p class="text-[11px] sm:text-xs font-medium text-slate-400 mt-2">Formulir karyawan</p>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg sm:text-xl shadow-inner flex-shrink-0">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
        </div>

        <!-- Metric 4: Total Armada Kendaraan -->
        <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Total Kendaraan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $totalVehicles }} <span class="text-xs font-medium text-slate-500">Unit</span></h3>
                <p class="text-[11px] sm:text-xs font-medium text-slate-400 mt-2">{{ $totalDrivers }} Driver terdaftar</p>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg sm:text-xl shadow-inner flex-shrink-0">
                <i class="fa-solid fa-van-shuttle"></i>
            </div>
        </div>
    </div>

    <!-- Active Live Trips & Radar Section -->
    <div class="p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse flex-shrink-0"></div>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900">Kendaraan Yang Sedang Berjalan (Live Monitoring)</h2>
            </div>
            <a href="{{ route('admin.tracking') }}" class="text-xs font-bold text-kalbe-600 hover:text-kalbe-700 flex items-center space-x-1.5 self-start sm:self-auto">
                <span>Buka Peta Live Tracking</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        @if ($activeTrips->isEmpty())
        <div class="py-12 text-center text-slate-400">
            <i class="fa-solid fa-route text-3xl mb-3 text-slate-300"></i>
            <p class="text-xs sm:text-sm font-semibold">Saat ini tidak ada mobil yang sedang dalam perjalanan dinas.</p>
        </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            @foreach ($activeTrips as $trip)
            <div class="p-4 sm:p-6 rounded-2xl border-2 border-emerald-100 bg-emerald-50/30 hover:border-emerald-300 transition-all space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-mono font-extrabold text-xs sm:text-sm text-slate-900">{{ $trip->txtTripCode }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase animate-pulse">
                                <i class="fa-solid fa-satellite-dish mr-1"></i> Live
                            </span>
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-sm sm:text-base mt-1">{{ $trip->vehicle ? $trip->vehicle->txtVehicleName : '-' }}</h3>
                        <p class="text-xs font-bold text-kalbe-600">{{ $trip->vehicle ? $trip->vehicle->txtPlateNumber : '-' }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Driver</span>
                        <span class="text-xs font-bold text-slate-800">{{ $trip->driver ? $trip->driver->txtDriverName : '-' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs pt-3 border-t border-emerald-100/80">
                    <div>
                        <span class="text-slate-400 block font-medium text-[11px]">Tujuan</span>
                        <span class="font-bold text-slate-800 text-xs truncate block" title="{{ $trip->txtDestination }}">{{ $trip->txtDestination }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium text-[11px]">Kecepatan</span>
                        <span class="font-bold text-slate-800 text-xs">
                            {{ $trip->latestLocation ? round($trip->latestLocation->floatSpeed, 0) . ' km/jam' : '0 km/jam' }}
                        </span>
                    </div>
                </div>

                <!-- Passengers pills -->
                <div class="pt-3 border-t border-emerald-100/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center space-x-1.5 text-xs text-slate-600 overflow-hidden">
                        <i class="fa-solid fa-users text-slate-400 flex-shrink-0"></i>
                        <span class="font-bold flex-shrink-0">{{ $trip->passengers->count() }} Org:</span>
                        <span class="text-slate-500 truncate max-w-[150px] sm:max-w-[200px]">{{ $trip->passengers->pluck('txtEmployeeName')->implode(', ') }}</span>
                    </div>
                    <a href="{{ route('admin.trips.show', $trip->intDutyTrip_ID) }}" class="px-3 py-1.5 rounded-lg kalbe-gradient text-white text-xs font-bold shadow-xs hover:opacity-95 transition-opacity text-center flex-shrink-0">
                        Detail & Foto
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Two Columns: Pending Employee Submissions & Fleet Capacity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Left: Pending Employee Submissions -->
        <div class="p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900">Pengajuan Dinas Karyawan Baru</h2>
                <a href="{{ route('admin.trips.index') }}" class="text-xs font-bold text-kalbe-600 hover:text-kalbe-700">Lihat Semua</a>
            </div>

            @if ($pendingBookings->isEmpty())
            <div class="py-8 text-center text-slate-400 text-xs">
                Tidak ada permohonan dinas baru yang menunggu penugasan driver.
            </div>
            @else
            <div class="space-y-3">
                @foreach ($pendingBookings as $booking)
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-slate-100/70 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-0.5">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-xs text-slate-900">{{ $booking->txtEmployeeName }}</span>
                            <span class="px-2 py-0.2 rounded text-[10px] font-semibold bg-white border border-slate-200 text-slate-600">{{ $booking->txtDepartment }}</span>
                        </div>
                        <p class="text-xs text-slate-500">{{ $booking->txtDestination }} • <span class="font-semibold text-slate-700">{{ $booking->dtmTripDate ? $booking->dtmTripDate->format('d M Y') : '-' }}</span></p>
                    </div>
                    <a href="{{ route('admin.trips.index') }}" class="self-start sm:self-auto px-3 py-1.5 text-xs font-bold rounded-lg bg-kalbe-50 text-kalbe-700 border border-kalbe-200 hover:bg-kalbe-100 transition-colors text-center">
                        Jadwalkan
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Right: Status Kendaraan & Kapasitas Kursi Hari Ini -->
        <div class="p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900">Kapasitas Kursi Armada Hari Ini</h2>
                <a href="{{ route('admin.vehicles.index') }}" class="text-xs font-bold text-kalbe-600 hover:text-kalbe-700">Kelola Armada</a>
            </div>

            <div class="space-y-3">
                @foreach ($vehicles as $v)
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-700 font-bold flex-shrink-0">
                            <i class="fa-solid fa-car"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-900">{{ $v->txtVehicleName }}</h3>
                            <p class="text-[11px] font-semibold text-slate-500">{{ $v->txtPlateNumber }} • {{ $v->txtBrandModel }}</p>
                        </div>
                    </div>

                    <div class="self-start sm:self-auto text-right">
                        @if ($v->remaining_seats <= 0)
                            <span class="px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-extrabold bg-red-100 text-red-700 inline-block">
                            Penuh (0/{{ $v->intMaxSeat }})
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-bold bg-emerald-100 text-emerald-800 inline-block">
                                Tersedia {{ $v->remaining_seats }} / {{ $v->intMaxSeat }} Kursi
                            </span>
                            @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection