@extends('layouts.admin')

@section('title', 'Live Tracking GPS Armada Dinas | KMI Road')
@section('header_title', 'Live GPS Tracking Command Center')

@section('content')
<div class="min-h-[calc(100vh-120px)] lg:h-[calc(100vh-140px)] flex flex-col lg:flex-row gap-4 sm:gap-6">
    <!-- Left Panel: Active Vehicles List & Status -->
    <div class="w-full lg:w-96 flex flex-col bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex-shrink-0 max-h-80 lg:max-h-none order-2 lg:order-1">
        <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <h2 class="font-extrabold text-xs sm:text-sm text-slate-900">Kendaraan Aktif (<span id="activeCountDisplay">{{ $activeTrips->count() }}</span>)</h2>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">Pembaruan otomatis setiap 5 detik</p>
            </div>
            <button id="refreshBtn" onclick="fetchLiveTelemetry()" class="p-2 rounded-xl text-slate-400 hover:text-kalbe-600 hover:bg-slate-100 transition-colors" title="Refresh Sekarang">
                <i class="fa-solid fa-arrows-rotate text-sm" id="refreshIcon"></i>
            </button>
        </div>

        <!-- Vehicle List Container -->
        <div class="flex-1 p-3 sm:p-4 space-y-3 overflow-y-auto" id="activeVehicleList">
            @forelse ($activeTrips as $trip)
            <div class="trip-item p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 hover:border-kalbe-500 bg-white hover:bg-emerald-50/20 cursor-pointer transition-all space-y-2.5"
                onclick="focusOnTrip({{ $trip->intDutyTrip_ID }})" id="tripCard_{{ $trip->intDutyTrip_ID }}">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="font-mono font-bold text-xs text-kalbe-600">{{ $trip->txtTripCode }}</span>
                        <h3 class="font-extrabold text-xs sm:text-sm text-slate-900">{{ $trip->vehicle ? $trip->vehicle->txtVehicleName : '-' }}</h3>
                        <p class="text-[11px] sm:text-xs font-semibold text-slate-500">{{ $trip->vehicle ? $trip->vehicle->txtPlateNumber : '-' }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase animate-pulse">
                        Live
                    </span>
                </div>

                <div class="text-xs text-slate-600 space-y-1 pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-[11px]">Driver:</span>
                        <span class="font-bold text-slate-800">{{ $trip->driver ? $trip->driver->txtDriverName : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-[11px]">Tujuan:</span>
                        <span class="font-bold text-slate-800 truncate max-w-[140px] sm:max-w-[160px]">{{ $trip->txtDestination }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-[11px]">Kecepatan:</span>
                        <span class="font-extrabold text-kalbe-600" id="speed_{{ $trip->intDutyTrip_ID }}">
                            {{ $trip->latestLocation ? round($trip->latestLocation->floatSpeed, 0) : 0 }} km/jam
                        </span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[10px] text-slate-400">
                        <i class="fa-solid fa-users mr-1"></i> {{ $trip->passengers->count() }} Penumpang
                    </span>
                    <a href="{{ route('admin.trips.show', $trip->intDutyTrip_ID) }}" class="text-[11px] font-bold text-kalbe-600 hover:text-kalbe-700">
                        Detail <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-400 text-xs">
                <i class="fa-solid fa-map-location text-2xl mb-2 text-slate-300"></i>
                <p>Tidak ada armada yang sedang aktif.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Right Panel: Interactive Leaflet Map -->
    <div class="w-full flex-1 bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden relative flex flex-col min-h-[380px] sm:min-h-[450px] lg:min-h-0 order-1 lg:order-2">
        <!-- Live Status Overlay Badge -->
        <div class="absolute top-3 left-3 sm:top-4 sm:left-4 z-[400] bg-white/95 backdrop-blur-md px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl sm:rounded-2xl border border-slate-200 shadow-md flex items-center space-x-2.5 sm:space-x-3">
            <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-emerald-500 animate-ping flex-shrink-0"></span>
            <div>
                <p class="text-[11px] sm:text-xs font-bold text-slate-900">GPS Live Telemetry</p>
                <p class="text-[9px] sm:text-[10px] text-slate-500" id="lastUpdatedText">Sinkronisasi langsung</p>
            </div>
        </div>

        <div id="map" class="w-full flex-1 h-full"></div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .custom-car-marker {
        background: transparent;
        border: none;
    }

    .car-marker-container {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .car-marker-pin {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #008542 0%, #064e2b 100%);
        border: 3px solid #ffffff;
        border-radius: 50%;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px rgba(0, 133, 66, 0.4);
        font-size: 16px;
        transition: transform 0.3s ease;
    }

    .car-marker-pin:hover {
        transform: scale(1.15);
    }

    .radar-pulse-ring {
        position: absolute;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(132, 189, 0, 0.4);
        animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
        pointer-events: none;
    }
</style>
@endpush

@push('scripts')
<script>
    let map;
    let markers = {};
    let polylines = {};
    let selectedTripId = {
        {
            $selectedTripId ?? 'null'
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Leaflet Map centered around Jakarta & Jabodetabek
        map = L.map('map', {
            zoomControl: false
        }).setView([-6.2088, 106.8456], 11);

        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);

        // OpenStreetMap Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Initial fetch
        fetchLiveTelemetry();

        // Auto poll telemetry every 5 seconds (Live Location like WhatsApp)
        setInterval(fetchLiveTelemetry, 5000);
    });

    function fetchLiveTelemetry() {
        const refreshIcon = document.getElementById('refreshIcon');
        if (refreshIcon) refreshIcon.classList.add('fa-spin');

        fetch('{{ route('
                admin.tracking.data ') }}')
            .then(res => res.json())
            .then(data => {
                if (refreshIcon) refreshIcon.classList.remove('fa-spin');
                document.getElementById('lastUpdatedText').innerText = 'Diperbarui: ' + new Date().toLocaleTimeString();

                if (data.trips) {
                    renderTelemetry(data.trips);
                }
            })
            .catch(err => {
                console.error('Error fetching telemetry:', err);
                if (refreshIcon) refreshIcon.classList.remove('fa-spin');
            });
    }

    function renderTelemetry(trips) {
        let latLngBounds = [];

        trips.forEach(trip => {
            if (!trip.current_location) return;

            const lat = trip.current_location.lat;
            const lng = trip.current_location.lng;
            const tripId = trip.trip_id;

            latLngBounds.push([lat, lng]);

            // Update or create marker
            if (markers[tripId]) {
                markers[tripId].setLatLng([lat, lng]);
            } else {
                const customIcon = L.divIcon({
                    className: 'custom-car-marker',
                    html: `
                        <div class="car-marker-container">
                            <div class="radar-pulse-ring"></div>
                            <div class="car-marker-pin">
                                <i class="fa-solid fa-car-side"></i>
                            </div>
                        </div>
                    `,
                    iconSize: [44, 44],
                    iconAnchor: [22, 22],
                    popupAnchor: [0, -22]
                });

                const marker = L.marker([lat, lng], {
                    icon: customIcon
                }).addTo(map);

                const popupContent = `
                    <div class="p-1 font-sans space-y-1">
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-mono font-bold text-xs text-emerald-700">${trip.trip_code}</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">${trip.status}</span>
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900">${trip.vehicle ? trip.vehicle.name : ''} (${trip.vehicle ? trip.vehicle.plate : ''})</h4>
                        <p class="text-xs text-slate-600"><strong>Driver:</strong> ${trip.driver ? trip.driver.name : '-'}</p>
                        <p class="text-xs text-slate-600"><strong>Tujuan:</strong> ${trip.destination}</p>
                        <div class="mt-2 pt-1 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Speed: <strong>${trip.current_location.speed} km/h</strong></span>
                            <a href="/admin/trips/${tripId}" class="text-emerald-600 font-bold hover:underline">Detail</a>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);
                markers[tripId] = marker;
            }

            // Route breadcrumb polyline
            if (trip.trail && trip.trail.length > 0) {
                const trailCoords = trip.trail.map(p => [p.lat, p.lng]);

                if (polylines[tripId]) {
                    polylines[tripId].setLatLngs(trailCoords);
                } else {
                    const polyline = L.polyline(trailCoords, {
                        color: '#008542',
                        weight: 4,
                        opacity: 0.8,
                        dashArray: '6, 8'
                    }).addTo(map);
                    polylines[tripId] = polyline;
                }
            }

            // Update speed display on card
            const speedEl = document.getElementById('speed_' + tripId);
            if (speedEl) {
                speedEl.innerText = trip.current_location.speed + ' km/jam';
            }
        });

        // Fit bounds if first time or selected
        if (latLngBounds.length > 0 && !selectedTripId) {
            map.fitBounds(latLngBounds, {
                padding: [50, 50],
                maxZoom: 14
            });
        } else if (selectedTripId && markers[selectedTripId]) {
            map.setView(markers[selectedTripId].getLatLng(), 15);
            markers[selectedTripId].openPopup();
        }
    }

    function focusOnTrip(tripId) {
        selectedTripId = tripId;
        if (markers[tripId]) {
            map.setView(markers[tripId].getLatLng(), 15, {
                animate: true
            });
            markers[tripId].openPopup();
        }

        // Highlight card
        document.querySelectorAll('.trip-item').forEach(el => el.classList.remove('border-kalbe-500', 'bg-emerald-50/40'));
        const card = document.getElementById('tripCard_' + tripId);
        if (card) card.classList.add('border-kalbe-500', 'bg-emerald-50/40');
    }
</script>
@endpush