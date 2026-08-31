@extends('layouts.app')

@section('title', 'Cek Status Pengajuan Dinas | KMI Road')

@section('content')
<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <!-- Search Bar Card -->
    <div class="glass-card rounded-3xl p-8 shadow-lg shadow-slate-200/50 mb-8">
        <div class="max-w-xl mx-auto text-center mb-6">
            <h1 class="text-2xl font-extrabold text-slate-900">Lacak Status Pengajuan Dinas</h1>
            <p class="text-xs text-slate-500 mt-1">Masukkan Nomor Tiket, Nama Karyawan, atau No. WhatsApp Anda untuk melacak proses penugasan.</p>
        </div>

        <form action="{{ route('employee.status') }}" method="GET" class="max-w-lg mx-auto flex items-center gap-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="q" value="{{ $search }}" placeholder="Contoh: Rian Hidayat atau 0812345..." required
                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-kalbe-500 shadow-sm">
            </div>
            <button type="submit" class="px-6 py-3 rounded-xl kalbe-gradient text-white text-xs font-bold shadow-md shadow-kalbe-500/20 hover:opacity-95 transition-all">
                Cari
            </button>
        </form>
    </div>

    <!-- Search Results -->
    @if ($results !== null)
        <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h2 class="text-sm font-bold text-slate-700">Hasil Pencarian ({{ $results->count() }} pengajuan ditemukan)</h2>
            </div>

            @if ($results->isEmpty())
                <div class="p-12 text-center rounded-3xl bg-white border border-slate-200 shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-4">
                        <i class="fa-solid fa-file-circle-xmark"></i>
                    </div>
                    <h3 class="font-bold text-slate-700">Tidak ada data ditemukan</h3>
                    <p class="text-xs text-slate-400 mt-1">Pastikan nama atau kata kunci yang dimasukkan sudah sesuai.</p>
                </div>
            @else
                @foreach ($results as $item)
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">ID Tiket: #KMI-REQ-{{ str_pad($item->intDutyTrip_Detail_ID, 5, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="text-base font-bold text-slate-900">{{ $item->txtEmployeeName }} <span class="text-xs font-normal text-slate-500">({{ $item->txtDepartment }})</span></h3>
                            </div>
                            <div>
                                @if ($item->txtBookingStatus === 'PENDING')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-clock text-[10px] mr-1"></i> Menunggu Penugasan Driver
                                    </span>
                                @elseif ($item->txtBookingStatus === 'ASSIGNED')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                        <i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Telah Dijadwalkan
                                    </span>
                                @elseif ($item->txtBookingStatus === 'COMPLETED')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check-double text-[10px] mr-1"></i> Perjalanan Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                        {{ $item->txtBookingStatus }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 block font-semibold">Tanggal Dinas</span>
                                <span class="font-bold text-slate-800">{{ $item->dtmTripDate ? $item->dtmTripDate->format('d M Y') : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-semibold">Tujuan</span>
                                <span class="font-bold text-slate-800">{{ $item->txtDestination }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-semibold">Kendaraan</span>
                                <span class="font-bold text-slate-800">
                                    {{ $item->requestedVehicle ? $item->requestedVehicle->txtVehicleName . ' (' . $item->requestedVehicle->txtPlateNumber . ')' : '-' }}
                                </span>
                            </div>
                        </div>

                        @if ($item->trip && $item->trip->driver)
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                <div class="flex items-center space-x-2">
                                    <span class="text-slate-500 font-semibold">Driver:</span>
                                    <span class="font-bold text-slate-900">{{ $item->trip->driver->txtDriverName }} ({{ $item->trip->driver->txtPhoneNumber }})</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 uppercase">
                                    Status: {{ $item->trip->txtTripStatus }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    @endif
</div>
@endsection
