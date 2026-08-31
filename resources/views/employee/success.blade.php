@extends('layouts.app')

@section('title', 'Pengajuan Berhasil Terkirim | KMI Road')

@section('content')
<div class="py-8 sm:py-12 px-3 sm:px-6 lg:px-8 max-w-2xl mx-auto">
    <div class="glass-card rounded-2xl sm:rounded-3xl p-5 sm:p-8 lg:p-10 shadow-xl shadow-slate-200/50 border border-slate-200 text-center">
        <!-- Success Icon -->
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-emerald-100 text-kalbe-500 mx-auto flex items-center justify-center text-2xl sm:text-3xl shadow-lg shadow-emerald-500/10 mb-4 sm:mb-6">
            <i class="fa-solid fa-check"></i>
        </div>

        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
            Pengajuan Berhasil Disimpan
        </span>

        <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 mt-3 sm:mt-4">Terima Kasih, {{ $booking->txtEmployeeName }}!</h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1.5 sm:mt-2">
            Data pengajuan dinas Anda telah berhasil masuk ke sistem antrean Tim Human Capital (HC).
        </p>

        <!-- Booking Ticket Card -->
        <div class="mt-6 sm:mt-8 p-4 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200/80 text-left space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor Registrasi Tiket</span>
                    <p class="text-lg font-extrabold text-kalbe-600 font-mono">#KMI-REQ-{{ str_pad($booking->intDutyTrip_Detail_ID, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Permohonan</span>
                    <div>
                        @if ($booking->txtBookingStatus === 'PENDING')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            <i class="fa-solid fa-clock text-[10px] mr-1"></i> Menunggu Jadwal Driver
                        </span>
                        @elseif ($booking->txtBookingStatus === 'ASSIGNED')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                            <i class="fa-solid fa-check text-[10px] mr-1"></i> Telah Dijadwalkan
                        </span>
                        @elseif ($booking->txtBookingStatus === 'COMPLETED')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            <i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Perjalanan Selesai
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            {{ $booking->txtBookingStatus }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block font-semibold">Departemen</span>
                    <span class="font-bold text-slate-800">{{ $booking->txtDepartment }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Tanggal Dinas</span>
                    <span class="font-bold text-slate-800">{{ $booking->dtmTripDate ? $booking->dtmTripDate->format('d M Y') : '-' }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400 block font-semibold">Kendaraan yang Diajukan</span>
                    <span class="font-bold text-slate-800">
                        {{ $booking->requestedVehicle ? $booking->requestedVehicle->txtVehicleName . ' (' . $booking->requestedVehicle->txtPlateNumber . ')' : '-' }}
                    </span>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400 block font-semibold">Tujuan Lokasi</span>
                    <span class="font-bold text-slate-800">{{ $booking->txtDestination }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400 block font-semibold">Keperluan Dinas</span>
                    <span class="text-slate-700">{{ $booking->txtPurpose }}</span>
                </div>
            </div>

            @if ($booking->trip && $booking->trip->driver)
            <div class="mt-4 pt-4 border-t border-slate-200 bg-white p-4 rounded-xl">
                <span class="text-[10px] font-bold uppercase tracking-wider text-kalbe-600 block mb-1">Driver yang Ditugaskan</span>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full kalbe-gradient flex items-center justify-center text-white font-bold text-xs">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ $booking->trip->driver->txtDriverName }}</p>
                            <p class="text-[11px] text-slate-500">{{ $booking->trip->driver->txtPhoneNumber }}</p>
                        </div>
                    </div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->trip->driver->txtPhoneNumber) }}" target="_blank" class="px-3 py-1 text-xs font-bold rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center space-x-1">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Hubungi</span>
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('employee.form') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl kalbe-gradient text-white text-xs font-bold shadow-md shadow-kalbe-500/20 hover:opacity-95 transition-all">
                <i class="fa-solid fa-plus mr-1.5"></i> Buat Pengajuan Lain
            </a>
            <a href="{{ route('employee.status') }}?q={{ $booking->intDutyTrip_Detail_ID }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-magnifying-glass mr-1.5"></i> Cek Status Tiket
            </a>
        </div>
    </div>
</div>
@endsection