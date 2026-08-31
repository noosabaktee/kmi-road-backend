@extends('layouts.app')

@section('title', 'Form Pengajuan Kendaraan Dinas | Kalbe Nutritionals')

@section('content')
<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <!-- Header Banner -->
    <div class="rounded-3xl kalbe-gradient p-8 text-white shadow-xl shadow-kalbe-900/10 relative overflow-hidden mb-8">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-kalbe-400/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-kalbe-200 mb-4">
                <i class="fa-solid fa-building-circle-check"></i>
                <span>PT Sanghiang Perkasa (Kalbe Nutritionals)</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Formulir Pengajuan <br><span class="text-kalbe-lime">Kendaraan Dinas Operasional</span></h1>
            <p class="mt-3 text-slate-100 text-sm leading-relaxed">
                Silakan lengkapi data perjalanan dinas di bawah ini. Sistem secara otomatis memvalidasi ketersediaan kapasitas kursi kendaraan secara real-time.
            </p>
        </div>
    </div>

    <!-- Alert / Validation Errors -->
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-start space-x-3 shadow-sm">
            <i class="fa-solid fa-circle-exclamation text-red-600 text-lg mt-0.5"></i>
            <div>
                <p class="font-bold">Mohon periksa kembali formulir Anda:</p>
                <ul class="mt-1 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Booking Form Card -->
    <form action="{{ route('employee.store') }}" method="POST" id="bookingForm" class="glass-card rounded-3xl p-8 sm:p-10 shadow-lg shadow-slate-200/50 space-y-8">
        @csrf

        <!-- Section 1: Data Karyawan -->
        <div>
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 mb-6">
                <div class="w-8 h-8 rounded-lg bg-kalbe-100 text-kalbe-600 flex items-center justify-center font-bold text-sm">1</div>
                <h2 class="text-lg font-extrabold text-slate-900">Identitas Karyawan Pemohon</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Nama Lengkap -->
                <div>
                    <label for="txtEmployeeName" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap Karyawan <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="txtEmployeeName" id="txtEmployeeName" value="{{ old('txtEmployeeName') }}" required placeholder="Contoh: Rian Hidayat"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-kalbe-500 focus:border-transparent transition-all shadow-sm">
                    </div>
                </div>

                <!-- NIK Karyawan -->
                <div>
                    <label for="txtEmployeeNIK" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIK / ID Karyawan (Opsional)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fa-solid fa-id-badge"></i></span>
                        <input type="text" name="txtEmployeeNIK" id="txtEmployeeNIK" value="{{ old('txtEmployeeNIK') }}" placeholder="Contoh: KMI-2023-089"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-kalbe-500 focus:border-transparent transition-all shadow-sm">
                    </div>
                </div>

                <!-- Departemen -->
                <div>
                    <label for="txtDepartment" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Departemen / Divisi <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fa-solid fa-building"></i></span>
                        <select name="txtDepartment" id="txtDepartment" required
                            class="w-full pl-10 pr-10 py-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-kalbe-500 focus:border-transparent transition-all shadow-sm appearance-none">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->txtDepartmentName }}" {{ old('txtDepartment') == $dept->txtDepartmentName ? 'selected' : '' }}>
                                    {{ $dept->txtDepartmentName }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-xs"></i></span>
                    </div>
                </div>

                <!-- Nomor WhatsApp / HP -->
                <div>
                    <label for="txtPhoneNumber" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">No. Handphone / WhatsApp <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fa-brands fa-whatsapp text-base"></i></span>
                        <input type="tel" name="txtPhoneNumber" id="txtPhoneNumber" value="{{ old('txtPhoneNumber') }}" required placeholder="Contoh: 081234567890"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-kalbe-500 focus:border-transparent transition-all shadow-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Jadwal & Kendaraan -->
        <div>
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 mb-6">
                <div class="w-8 h-8 rounded-lg bg-kalbe-100 text-kalbe-600 flex items-center justify-center font-bold text-sm">2</div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Jadwal & Pemilihan Kendaraan Dinas</h2>
                    <p class="text-xs text-slate-500">Pilih tanggal terlebih dahulu untuk memeriksa sisa kursi kendaraan secara otomatis.</p>
                </div>
            </div>

            <!-- Tanggal Dinas -->
            <div class="mb-6 max-w-md">
                <label for="dtmTripDate" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Perjalanan Dinas <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fa-solid fa-calendar-day"></i></span>
                    <input type="date" name="dtmTripDate" id="dtmTripDate" value="{{ old('dtmTripDate', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-kalbe-500 focus:border-transparent transition-all shadow-sm">
                </div>
            </div>

            <!-- Vehicle Selection Grid -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Pilih Mobil Operasional <span class="text-red-500">*</span></label>
                
                <div id="vehicleLoading" class="hidden py-8 text-center text-slate-500 text-sm">
                    <i class="fa-solid fa-circle-notch fa-spin text-kalbe-500 text-2xl mb-2"></i>
                    <p>Memperbarui ketersediaan kursi...</p>
                </div>

                <div id="vehicleList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($vehicles as $vehicle)
                        @php
                            $isFull = $vehicle->remaining_seats <= 0;
                        @endphp
                        <label class="vehicle-card relative flex flex-col p-5 rounded-2xl border-2 cursor-pointer transition-all {{ $isFull ? 'bg-slate-100/70 border-slate-200 opacity-60 cursor-not-allowed' : 'bg-white border-slate-200 hover:border-kalbe-400 hover:shadow-md' }}" data-id="{{ $vehicle->intVehicle_ID }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <input type="radio" name="intRequestedVehicle_ID" value="{{ $vehicle->intVehicle_ID }}" {{ $isFull ? 'disabled' : '' }} {{ old('intRequestedVehicle_ID') == $vehicle->intVehicle_ID ? 'checked' : '' }}
                                        class="w-4 h-4 text-kalbe-600 focus:ring-kalbe-500 border-slate-300">
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm">{{ $vehicle->txtVehicleName }}</h3>
                                        <p class="text-xs font-semibold text-slate-500">{{ $vehicle->txtPlateNumber }} • {{ $vehicle->txtBrandModel }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 text-slate-700">{{ $vehicle->txtVehicleType }}</span>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div class="text-xs text-slate-500">
                                    <i class="fa-solid fa-gas-pump text-slate-400 mr-1"></i> {{ $vehicle->txtFuelType }}
                                </div>
                                <div class="seat-badge">
                                    @if ($isFull)
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-extrabold bg-red-100 text-red-700">
                                            <i class="fa-solid fa-ban text-[10px]"></i>
                                            <span>Penuh (0 Kursi Tersisa)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <i class="fa-solid fa-chair text-[10px]"></i>
                                            <span>Sisa {{ $vehicle->remaining_seats }} / {{ $vehicle->intMaxSeat }} Kursi</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Section 3: Detail Dinas -->
        <div>
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 mb-6">
                <div class="w-8 h-8 rounded-lg bg-kalbe-100 text-kalbe-600 flex items-center justify-center font-bold text-sm">3</div>
                <h2 class="text-lg font-extrabold text-slate-900">Tujuan & Keperluan Dinas</h2>
            </div>

            <div class="space-y-6">
                <!-- Tujuan Dinas -->
                <div>
                    <label for="txtDestination" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tujuan Lokasi Perjalanan Dinas <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fa-solid fa-location-dot"></i></span>
                        <input type="text" name="txtDestination" id="txtDestination" value="{{ old('txtDestination') }}" required placeholder="Contoh: PT Kalbe Farma Tbk (Pabrik Cikarang) / BPOM RI Jakarta"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-kalbe-500 focus:border-transparent transition-all shadow-sm">
                    </div>
                </div>

                <!-- Keperluan Dinas -->
                <div>
                    <label for="txtPurpose" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keperluan / Deskripsi Tugas Dinas <span class="text-red-500">*</span></label>
                    <textarea name="txtPurpose" id="txtPurpose" rows="3" required placeholder="Jelaskan agenda dinas secara spesifik (Contoh: Audit mutu vendor bahan baku, pengujian line filling, dll)"
                        class="w-full p-4 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-kalbe-500 focus:border-transparent transition-all shadow-sm">{{ old('txtPurpose') }}</textarea>
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <label for="txtNotes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="txtNotes" id="txtNotes" value="{{ old('txtNotes') }}" placeholder="Contoh: Membawa sampel botol kaca 2 box, berangkat jam 07.30 pagi"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-kalbe-500 focus:border-transparent transition-all shadow-sm">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500 flex items-center space-x-2">
                <i class="fa-solid fa-shield-heart text-kalbe-500 text-base"></i>
                <span>Data tersimpan aman di database operasional KMI Road HC.</span>
            </div>
            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl kalbe-gradient text-white font-bold text-sm shadow-lg shadow-kalbe-500/30 hover:opacity-95 transition-all flex items-center justify-center space-x-2">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Kirim Pengajuan Dinas</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('dtmTripDate');
        const vehicleLoading = document.getElementById('vehicleLoading');
        const vehicleList = document.getElementById('vehicleList');

        // Dynamic fetch vehicle seats when date changes
        dateInput.addEventListener('change', function () {
            const selectedDate = this.value;
            if (!selectedDate) return;

            vehicleLoading.classList.remove('hidden');
            vehicleList.classList.add('opacity-40');

            fetch(`/api/check-vehicles?date=${selectedDate}`)
                .then(res => res.json())
                .then(data => {
                    vehicleLoading.classList.add('hidden');
                    vehicleList.classList.remove('opacity-40');

                    if (data.vehicles) {
                        data.vehicles.forEach(v => {
                            const card = vehicleList.querySelector(`[data-id="${v.id}"]`);
                            if (card) {
                                const radio = card.querySelector('input[type="radio"]');
                                const seatBadge = card.querySelector('.seat-badge');

                                if (v.is_full) {
                                    card.className = "vehicle-card relative flex flex-col p-5 rounded-2xl border-2 bg-slate-100/70 border-slate-200 opacity-60 cursor-not-allowed";
                                    radio.disabled = true;
                                    radio.checked = false;
                                    seatBadge.innerHTML = `
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-extrabold bg-red-100 text-red-700">
                                            <i class="fa-solid fa-ban text-[10px]"></i>
                                            <span>Penuh (0 Kursi Tersisa)</span>
                                        </span>
                                    `;
                                } else {
                                    card.className = "vehicle-card relative flex flex-col p-5 rounded-2xl border-2 bg-white border-slate-200 hover:border-kalbe-400 hover:shadow-md cursor-pointer";
                                    radio.disabled = false;
                                    seatBadge.innerHTML = `
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <i class="fa-solid fa-chair text-[10px]"></i>
                                            <span>Sisa ${v.remaining_seats} / ${v.max_seat} Kursi</span>
                                        </span>
                                    `;
                                }
                            }
                        });
                    }
                })
                .catch(err => {
                    console.error('Error fetching seats:', err);
                    vehicleLoading.classList.add('hidden');
                    vehicleList.classList.remove('opacity-40');
                });
        });
    });
</script>
@endpush
