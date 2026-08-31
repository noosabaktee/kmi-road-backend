@extends('layouts.admin')

@section('title', 'Detail Perjalanan & Dokumentasi ' . $trip->txtTripCode . ' | KMI Road')
@section('header_title', 'Detail & Dokumentasi Perjalanan Dinas')

@section('content')
<div class="space-y-8">
    <!-- Top Trip Header Card -->
    <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center space-x-3">
                <span class="font-mono font-extrabold text-xl text-kalbe-700">{{ $trip->txtTripCode }}</span>
                @if ($trip->txtTripStatus === 'IN_PROGRESS')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 animate-pulse">
                        <i class="fa-solid fa-satellite-dish mr-1.5"></i> Sedang Berjalan (Live)
                    </span>
                @elseif ($trip->txtTripStatus === 'SCHEDULED')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                        <i class="fa-solid fa-calendar mr-1.5"></i> Dijadwalkan
                    </span>
                @elseif ($trip->txtTripStatus === 'COMPLETED')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        <i class="fa-solid fa-check-double mr-1.5"></i> Perjalanan Selesai
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                        {{ $trip->txtTripStatus }}
                    </span>
                @endif
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900">{{ $trip->txtDestination }}</h2>
            <p class="text-xs text-slate-500 max-w-2xl">{{ $trip->txtPurpose }}</p>
        </div>

        <!-- Quick Status Control -->
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('admin.trips.status', $trip->intDutyTrip_ID) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <select name="txtTripStatus" class="p-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50">
                    <option value="SCHEDULED" {{ $trip->txtTripStatus == 'SCHEDULED' ? 'selected' : '' }}>Dijadwalkan</option>
                    <option value="IN_PROGRESS" {{ $trip->txtTripStatus == 'IN_PROGRESS' ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="COMPLETED" {{ $trip->txtTripStatus == 'COMPLETED' ? 'selected' : '' }}>Selesai</option>
                    <option value="CANCELLED" {{ $trip->txtTripStatus == 'CANCELLED' ? 'selected' : '' }}>Batalkan</option>
                </select>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900">
                    Update Status
                </button>
            </form>
        </div>
    </div>

    <!-- 3 Summary Metrics: Odometer, BBM, Penumpang -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-gauge"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Odometer</span>
                <p class="text-base font-extrabold text-slate-900">
                    {{ $trip->intStartOdometer ?? '-' }} <span class="text-xs text-slate-400">s/d</span> {{ $trip->intEndOdometer ?? 'Berjalan' }} KM
                </p>
                <p class="text-[11px] text-slate-500 font-semibold">
                    @if ($trip->intStartOdometer && $trip->intEndOdometer)
                        Jarak: {{ $trip->intEndOdometer - $trip->intStartOdometer }} KM
                    @else
                        Dalam pemantauan
                    @endif
                </p>
            </div>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-gas-pump"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pengeluaran BBM</span>
                <p class="text-base font-extrabold text-slate-900">
                    Rp {{ number_format($trip->floatTotalFuelCost ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-slate-500 font-semibold">
                    Total: {{ round($trip->floatTotalFuelLiters ?? 0, 2) }} Liter
                </p>
            </div>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Penumpang Dinas</span>
                <p class="text-base font-extrabold text-slate-900">
                    {{ $trip->passengers->count() }} Orang
                </p>
                <p class="text-[11px] text-slate-500 font-semibold">
                    Kapasitas {{ $trip->vehicle ? $trip->vehicle->intMaxSeat : 0 }} Kursi
                </p>
            </div>
        </div>
    </div>

    <!-- Two Columns: Driver & Vehicle Info + Passenger List -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Vehicle & Driver Card -->
        <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center space-x-2">
                <i class="fa-solid fa-id-card text-kalbe-500"></i>
                <span>Informasi Armada & Driver</span>
            </h3>

            <div class="space-y-4">
                <!-- Vehicle -->
                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-700 font-bold">
                            <i class="fa-solid fa-car"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">{{ $trip->vehicle ? $trip->vehicle->txtVehicleName : '-' }}</h4>
                            <p class="text-xs font-semibold text-slate-500">{{ $trip->vehicle ? $trip->vehicle->txtPlateNumber : '-' }} • {{ $trip->vehicle ? $trip->vehicle->txtBrandModel : '-' }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-kalbe-50 text-kalbe-700">
                        {{ $trip->vehicle ? $trip->vehicle->intMaxSeat : 0 }} Kursi
                    </span>
                </div>

                <!-- Driver -->
                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl kalbe-gradient text-white flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">{{ $trip->driver ? $trip->driver->txtDriverName : '-' }}</h4>
                            <p class="text-xs font-medium text-slate-500">{{ $trip->driver ? $trip->driver->txtPhoneNumber : '-' }} • SIM: {{ $trip->driver ? $trip->driver->txtLicenseNumber : '-' }}</p>
                        </div>
                    </div>
                    @if ($trip->driver && $trip->driver->txtPhoneNumber)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $trip->driver->txtPhoneNumber) }}" target="_blank"
                            class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-xs hover:bg-emerald-100 flex items-center space-x-1">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Passengers List Card -->
        <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center space-x-2">
                <i class="fa-solid fa-users text-kalbe-500"></i>
                <span>Daftar Karyawan Penumpang ({{ $trip->passengers->count() }} Orang)</span>
            </h3>

            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse ($trip->passengers as $p)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-slate-900">{{ $p->txtEmployeeName }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] bg-white border border-slate-200 font-semibold text-slate-600">{{ $p->txtDepartment }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $p->txtPurpose }}</p>
                        </div>
                        @if ($p->txtPhoneNumber)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->txtPhoneNumber) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-bold">
                                <i class="fa-brands fa-whatsapp text-base"></i>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada daftar karyawan penumpang.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- GPS Route Playback Map -->
    <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Rute Perjalanan & Rekam Jejak GPS</h3>
                <p class="text-xs text-slate-500">Rekam jejak koordinat latitude/longitude yang dikirimkan aplikasi mobile driver.</p>
            </div>
            <span class="text-xs font-bold text-slate-600">Total: {{ $gpsTrail->count() }} Titik Telemetry</span>
        </div>

        <div id="tripDetailMap" class="w-full h-80 rounded-2xl border border-slate-200 overflow-hidden"></div>
    </div>

    <!-- Bukti Dokumentasi & Foto Checkpoints Gallery (Pre-trip, BBM, Lokasi, Selesai) -->
    <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Bukti Dokumentasi & Laporan Foto Driver</h3>
                <p class="text-xs text-slate-500">Bukti foto kondisi mobil sebelum jalan, struk pengisian BBM, tiba di lokasi tujuan, dan foto selesai.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-kalbe-50 text-kalbe-700 border border-kalbe-200">
                {{ $trip->documentations->count() }} Bukti Terunggah
            </span>
        </div>

        @if ($trip->documentations->isEmpty())
            <div class="py-12 text-center text-slate-400 text-xs">
                <i class="fa-solid fa-camera text-3xl mb-3 text-slate-300"></i>
                <p class="font-semibold">Driver belum mengunggah bukti foto dokumentasi perjalanan ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($trip->documentations as $doc)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">
                        <!-- Photo -->
                        <div class="relative h-48 bg-slate-900 overflow-hidden group">
                            <img src="{{ str_starts_with($doc->txtPhotoPath, 'http') ? $doc->txtPhotoPath : asset('storage/' . $doc->txtPhotoPath) }}"
                                alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Category badge overlay -->
                            <div class="absolute top-3 left-3">
                                @if ($doc->txtCategory === 'SEBELUM_BERANGKAT')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-600 text-white shadow">
                                        <i class="fa-solid fa-car-on mr-1"></i> Sebelum Berangkat
                                    </span>
                                @elseif ($doc->txtCategory === 'ISI_BBM')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-500 text-white shadow">
                                        <i class="fa-solid fa-gas-pump mr-1"></i> Pengisian BBM
                                    </span>
                                @elseif ($doc->txtCategory === 'SAMPAI_TUJUAN')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-600 text-white shadow">
                                        <i class="fa-solid fa-location-dot mr-1"></i> Sampai Lokasi
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-800 text-white shadow">
                                        <i class="fa-solid fa-flag-checkered mr-1"></i> Selesai
                                    </span>
                                @endif
                            </div>

                            <a href="{{ str_starts_with($doc->txtPhotoPath, 'http') ? $doc->txtPhotoPath : asset('storage/' . $doc->txtPhotoPath) }}" target="_blank"
                                class="absolute bottom-3 right-3 w-8 h-8 rounded-lg bg-black/60 backdrop-blur-md text-white flex items-center justify-center text-xs hover:bg-black transition-colors">
                                <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                            </a>
                        </div>

                        <!-- Details Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span><i class="fa-regular fa-clock mr-1"></i> {{ $doc->dtmInserted ? $doc->dtmInserted->format('d M Y, H:i') : '-' }}</span>
                                    @if ($doc->intOdometer)
                                        <span class="font-mono font-bold text-slate-700"><i class="fa-solid fa-gauge-simple mr-1"></i> {{ number_format($doc->intOdometer, 0, ',', '.') }} KM</span>
                                    @endif
                                </div>

                                @if ($doc->floatFuelCost || $doc->floatFuelLiters)
                                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-xs font-bold text-amber-900 flex items-center justify-between">
                                        <span>Biaya: Rp {{ number_format($doc->floatFuelCost, 0, ',', '.') }}</span>
                                        <span>{{ $doc->floatFuelLiters }} Liter</span>
                                    </div>
                                @endif

                                @if ($doc->txtLocationName)
                                    <p class="text-xs font-semibold text-slate-700 flex items-start space-x-1.5">
                                        <i class="fa-solid fa-map-pin text-kalbe-500 mt-0.5"></i>
                                        <span>{{ $doc->txtLocationName }}</span>
                                    </p>
                                @endif

                                @if ($doc->txtNotes)
                                    <p class="text-xs text-slate-600 italic bg-white p-2.5 rounded-xl border border-slate-100">
                                        "{{ $doc->txtNotes }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trail = @json($gpsTrail);
        const mapContainer = document.getElementById('tripDetailMap');

        if (mapContainer && trail.length > 0) {
            const detailMap = L.map('tripDetailMap', { zoomControl: true });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(detailMap);

            const latLngs = trail.map(t => [t.floatLatitude, t.floatLongitude]);

            // Draw route polyline
            L.polyline(latLngs, {
                color: '#008542',
                weight: 5,
                opacity: 0.85
            }).addTo(detailMap);

            // Origin Marker
            const startPt = latLngs[0];
            L.marker(startPt).addTo(detailMap).bindPopup('<strong>Titik Awal Berangkat</strong>');

            // Current / Last Position Marker
            const endPt = latLngs[latLngs.length - 1];
            L.marker(endPt).addTo(detailMap).bindPopup('<strong>Posisi Terakhir</strong>').openPopup();

            detailMap.fitBounds(latLngs, { padding: [40, 40] });
        } else if (mapContainer) {
            const detailMap = L.map('tripDetailMap').setView([-6.2088, 106.8456], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(detailMap);
        }
    });
</script>
@endpush
