@extends('layouts.admin')

@section('title', 'Buat Jadwal Tugas Dinas Langsung | KMI Road')
@section('header_title', 'Buat Jadwal & Penugasan Dinas Langsung')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm">
        <div class="pb-4 sm:pb-6 border-b border-slate-100 mb-6">
            <h2 class="text-base sm:text-xl font-extrabold text-slate-900">Formulir Pembuatan Jadwal Dinas Langsung</h2>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-1">Admin HC dapat langsung menentukan mobil operasional, menugaskan driver, dan memasukkan daftar nama karyawan penumpang.</p>
        </div>

        <form action="{{ route('admin.trips.store') }}" method="POST" id="directTripForm" class="space-y-6 sm:space-y-8">
            @csrf

            <!-- Step 1: Penugasan Kendaraan & Driver -->
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center space-x-2">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-md bg-kalbe-100 text-kalbe-700 flex items-center justify-center text-xs flex-shrink-0">1</span>
                    <span>Pilihan Kendaraan & Driver</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="intVehicle_ID" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Mobil Operasional <span class="text-red-500">*</span></label>
                        <select name="intVehicle_ID" id="intVehicle_ID" required class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-kalbe-500">
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach ($vehicles as $v)
                            <option value="{{ $v->intVehicle_ID }}" {{ old('intVehicle_ID') == $v->intVehicle_ID ? 'selected' : '' }}>
                                {{ $v->txtVehicleName }} ({{ $v->txtPlateNumber }}) - Kapasitas {{ $v->intMaxSeat }} Kursi
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="intDriver_ID" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tugaskan Driver (Supir) <span class="text-red-500">*</span></label>
                        <select name="intDriver_ID" id="intDriver_ID" required class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-kalbe-500">
                            <option value="">-- Pilih Driver --</option>
                            @foreach ($drivers as $d)
                            <option value="{{ $d->intDriver_ID }}" {{ old('intDriver_ID') == $d->intDriver_ID ? 'selected' : '' }}>
                                {{ $d->txtDriverName }} ({{ $d->txtPhoneNumber }}) - Status: {{ $d->txtStatus }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="dtmTripDate" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Dinas <span class="text-red-500">*</span></label>
                        <input type="date" name="dtmTripDate" id="dtmTripDate" value="{{ old('dtmTripDate', date('Y-m-d')) }}" required
                            class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-kalbe-500">
                    </div>

                    <div>
                        <label for="dtmDepartureTime" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Waktu / Jam Berangkat (Opsional)</label>
                        <input type="datetime-local" name="dtmDepartureTime" id="dtmDepartureTime" value="{{ old('dtmDepartureTime') }}"
                            class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-kalbe-500">
                    </div>
                </div>
            </div>

            <!-- Step 2: Tujuan & Keperluan Dinas -->
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center space-x-2">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-md bg-kalbe-100 text-kalbe-700 flex items-center justify-center text-xs flex-shrink-0">2</span>
                    <span>Tujuan & Keperluan Dinas</span>
                </h3>

                <div class="space-y-4">
                    <div>
                        <label for="txtDestination" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tujuan Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="txtDestination" id="txtDestination" value="{{ old('txtDestination') }}" required placeholder="Contoh: PT Kalbe Farma Tbk (Pabrik Cikarang) / Kantor BPOM"
                            class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-kalbe-500">
                    </div>

                    <div>
                        <label for="txtPurpose" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Keperluan Dinas <span class="text-red-500">*</span></label>
                        <textarea name="txtPurpose" id="txtPurpose" rows="2" required placeholder="Jelaskan keperluan perjalanan dinas..."
                            class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-kalbe-500">{{ old('txtPurpose') }}</textarea>
                    </div>

                    <div>
                        <label for="txtNotes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Khusus untuk Driver</label>
                        <input type="text" name="txtNotes" id="txtNotes" value="{{ old('txtNotes') }}" placeholder="Contoh: Jemput tim di lobby utama jam 07.30"
                            class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-kalbe-500">
                    </div>
                </div>
            </div>

            <!-- Step 3: Daftar Nama Karyawan Penumpang -->
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2 border-b border-slate-100 mb-4 gap-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                        <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-md bg-kalbe-100 text-kalbe-700 flex items-center justify-center text-xs flex-shrink-0">3</span>
                        <span>Daftar Karyawan yang Ditugaskan</span>
                    </h3>
                    <button type="button" onclick="addPassengerRow()" class="self-start sm:self-auto px-3 py-1.5 rounded-lg bg-kalbe-50 text-kalbe-700 border border-kalbe-200 text-xs font-bold hover:bg-kalbe-100 flex items-center space-x-1">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Tambah Karyawan</span>
                    </button>
                </div>

                <div id="passengerContainer" class="space-y-3">
                    <!-- Default Passenger Row 1 -->
                    <div class="passenger-row p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-4 gap-3 relative">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Nama Karyawan <span class="text-red-500">*</span></label>
                            <input type="text" name="passengers[0][name]" required placeholder="Nama Lengkap" class="w-full p-2.5 rounded-lg border border-slate-200 bg-white text-xs focus:ring-2 focus:ring-kalbe-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Departemen <span class="text-red-500">*</span></label>
                            <select name="passengers[0][dept]" required class="w-full p-2.5 rounded-lg border border-slate-200 bg-white text-xs focus:ring-2 focus:ring-kalbe-500">
                                @foreach ($departments as $dept)
                                <option value="{{ $dept->txtDepartmentName }}">{{ $dept->txtDepartmentName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">No. WhatsApp / HP</label>
                            <input type="tel" name="passengers[0][phone]" placeholder="0812..." class="w-full p-2.5 rounded-lg border border-slate-200 bg-white text-xs focus:ring-2 focus:ring-kalbe-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">NIK (Opsional)</label>
                            <input type="text" name="passengers[0][nik]" placeholder="KMI-..." class="w-full p-2.5 rounded-lg border border-slate-200 bg-white text-xs focus:ring-2 focus:ring-kalbe-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-3">
                <a href="{{ route('admin.trips.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 text-center">Batal</a>
                <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl kalbe-gradient text-white text-xs font-bold shadow-lg shadow-kalbe-500/20 hover:opacity-95 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Terbitkan & Tugaskan Driver</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let passengerIndex = 1;
    const departments = @json($departments);

    function addPassengerRow() {
        let deptOptions = departments.map(d => `<option value="${d.txtDepartmentName}">${d.txtDepartmentName}</option>`).join('');

        const html = `
            <div class="passenger-row p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-4 gap-3 relative animate-fade-in" id="row_${passengerIndex}">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Nama Karyawan <span class="text-red-500">*</span></label>
                    <input type="text" name="passengers[${passengerIndex}][name]" required placeholder="Nama Lengkap" class="w-full p-2.5 rounded-lg border border-slate-200 bg-white text-xs focus:ring-2 focus:ring-kalbe-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Departemen <span class="text-red-500">*</span></label>
                    <select name="passengers[${passengerIndex}][dept]" required class="w-full p-2.5 rounded-lg border border-slate-200 bg-white text-xs focus:ring-2 focus:ring-kalbe-500">
                        ${deptOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">No. WhatsApp / HP</label>
                    <input type="tel" name="passengers[${passengerIndex}][phone]" placeholder="0812..." class="w-full p-2.5 rounded-lg border border-slate-200 bg-white text-xs focus:ring-2 focus:ring-kalbe-500">
                </div>
                <div class="flex items-end space-x-2">
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">NIK (Opsional)</label>
                        <input type="text" name="passengers[${passengerIndex}][nik]" placeholder="KMI-..." class="w-full p-2.5 rounded-lg border border-slate-200 bg-white text-xs focus:ring-2 focus:ring-kalbe-500">
                    </div>
                    <button type="button" onclick="removePassengerRow(${passengerIndex})" class="p-2.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
            </div>
        `;

        document.getElementById('passengerContainer').insertAdjacentHTML('beforeend', html);
        passengerIndex++;
    }

    function removePassengerRow(index) {
        const row = document.getElementById('row_' + index);
        if (row) row.remove();
    }
</script>
@endpush