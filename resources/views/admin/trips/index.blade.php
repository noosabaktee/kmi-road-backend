@extends('layouts.admin')

@section('title', 'Manajemen Jadwal & Penugasan Dinas | KMI Road')
@section('header_title', 'Jadwal & Penugasan Perjalanan Dinas')

@section('content')
<div class="space-y-6 sm:space-y-8">
    <!-- Section 1: Antrean Permohonan Karyawan (Pending Employee Submissions) -->
    <div class="p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5 sm:space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3 sm:gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-amber-500 flex-shrink-0"></span>
                    <h2 class="text-sm sm:text-base font-extrabold text-slate-900">Antrean Formulir Dinas Karyawan (Menunggu Penugasan)</h2>
                </div>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Pilih beberapa permohonan untuk digabungkan ke dalam 1 mobil dinas atau buatkan jadwal penugasan driver.</p>
            </div>

            @if ($pendingBookings->isNotEmpty())
            <button type="button" onclick="openAssignModal()" class="w-full sm:w-auto justify-center px-4 py-2 text-xs font-bold rounded-xl kalbe-gradient text-white shadow-md shadow-kalbe-500/20 hover:opacity-95 transition-all flex items-center space-x-2 flex-shrink-0">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tugaskan Driver & Mobil Terpilih</span>
            </button>
            @endif
        </div>

        @if ($pendingBookings->isEmpty())
        <div class="py-8 text-center text-slate-400 text-xs">
            <i class="fa-solid fa-clipboard-check text-2xl mb-2 text-slate-300"></i>
            <p>Semua permohonan karyawan telah ditugaskan.</p>
        </div>
        @else
        <form id="bulkAssignForm" action="{{ route('admin.trips.assign') }}" method="POST">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 min-w-[700px]">
                    <thead class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80 rounded-xl">
                        <tr>
                            <th class="p-3 w-10 text-center"><input type="checkbox" id="selectAll" class="w-4 h-4 rounded text-kalbe-600 focus:ring-kalbe-500"></th>
                            <th class="p-3">Nama Karyawan</th>
                            <th class="p-3">Departemen</th>
                            <th class="p-3">No. HP/WA</th>
                            <th class="p-3">Tgl Dinas</th>
                            <th class="p-3">Mobil Diminta</th>
                            <th class="p-3">Tujuan</th>
                            <th class="p-3">Keperluan</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pendingBookings as $booking)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="p-3 text-center">
                                <input type="checkbox" name="booking_ids[]" value="{{ $booking->intDutyTrip_Detail_ID }}" class="booking-checkbox w-4 h-4 rounded text-kalbe-600 focus:ring-kalbe-500">
                            </td>
                            <td class="p-3 font-bold text-slate-900">{{ $booking->txtEmployeeName }}</td>
                            <td class="p-3"><span class="px-2 py-0.5 rounded bg-slate-100 font-semibold text-slate-700">{{ $booking->txtDepartment }}</span></td>
                            <td class="p-3">{{ $booking->txtPhoneNumber }}</td>
                            <td class="p-3 font-bold text-kalbe-700">{{ $booking->dtmTripDate ? $booking->dtmTripDate->format('d M Y') : '-' }}</td>
                            <td class="p-3 font-semibold text-slate-800">{{ $booking->requestedVehicle ? $booking->requestedVehicle->txtVehicleName : '-' }}</td>
                            <td class="p-3 font-medium text-slate-800">{{ $booking->txtDestination }}</td>
                            <td class="p-3 text-slate-500 max-w-[200px] truncate" title="{{ $booking->txtPurpose }}">{{ $booking->txtPurpose }}</td>
                            <td class="p-3 text-right">
                                <button type="button" onclick="assignSingle({{ $booking->intDutyTrip_Detail_ID }}, '{{ $booking->dtmTripDate ? $booking->dtmTripDate->format('Y-m-d') : '' }}', {{ $booking->intRequestedVehicle_ID ?? 'null' }})"
                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-kalbe-50 text-kalbe-700 border border-kalbe-200 hover:bg-kalbe-100">
                                    Jadwalkan
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modal Dialog for Assigning Driver & Vehicle -->
            <div id="assignModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-3 sm:p-4">
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-5 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-extrabold text-sm sm:text-base text-slate-900">Penugasan Driver & Mobil Dinas</h3>
                        <button type="button" onclick="closeAssignModal()" class="text-slate-400 hover:text-slate-600 p-1"><i class="fa-solid fa-xmark text-lg"></i></button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Kendaraan Operasional <span class="text-red-500">*</span></label>
                            <select name="intVehicle_ID" id="modalVehicleSelect" required class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-kalbe-500">
                                <option value="">-- Pilih Kendaraan --</option>
                                @foreach ($vehicles as $v)
                                <option value="{{ $v->intVehicle_ID }}" data-max="{{ $v->intMaxSeat }}">
                                    {{ $v->txtVehicleName }} ({{ $v->txtPlateNumber }}) - Kapasitas {{ $v->intMaxSeat }} Kursi
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tugaskan Driver (Supir) <span class="text-red-500">*</span></label>
                            <select name="intDriver_ID" id="modalDriverSelect" required class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-kalbe-500">
                                <option value="">-- Pilih Driver --</option>
                                @foreach ($drivers as $d)
                                <option value="{{ $d->intDriver_ID }}">
                                    {{ $d->txtDriverName }} ({{ $d->txtPhoneNumber }}) - [{{ $d->txtStatus }}]
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Berangkat <span class="text-red-500">*</span></label>
                            <input type="date" name="dtmTripDate" id="modalTripDate" required class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-kalbe-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Tugas ke Driver (Opsional)</label>
                            <input type="text" name="txtNotes" placeholder="Contoh: Jemput karyawan di lobby gedung utama jam 07.30" class="w-full p-2.5 sm:p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-kalbe-500">
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2 sm:space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeAssignModal()" class="w-full sm:w-auto px-4 py-2.5 text-xs font-bold rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-center">Batal</button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 text-xs font-bold rounded-xl kalbe-gradient text-white shadow-md shadow-kalbe-500/20 hover:opacity-95 text-center">Konfirmasi & Terbitkan Jadwal</button>
                    </div>
                </div>
            </div>
        </form>
        @endif
    </div>

    <!-- Section 2: Daftar Seluruh Jadwal Dinas (Trips List) -->
    <div class="p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5 sm:space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3 sm:gap-4">
            <div>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900">Daftar Jadwal Perjalanan Dinas Armada</h2>
                <p class="text-[11px] sm:text-xs text-slate-500">Seluruh tugas dinas yang dijadwalkan, sedang berjalan, maupun telah selesai.</p>
            </div>

            <!-- Filters -->
            <form action="{{ route('admin.trips.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2 text-xs w-full sm:w-auto">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode/tujuan/karyawan..." class="w-full sm:w-48 px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                <select name="status" class="w-full sm:w-auto px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                    <option value="">Semua Status</option>
                    <option value="SCHEDULED" {{ $status == 'SCHEDULED' ? 'selected' : '' }}>Dijadwalkan</option>
                    <option value="IN_PROGRESS" {{ $status == 'IN_PROGRESS' ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="COMPLETED" {{ $status == 'COMPLETED' ? 'selected' : '' }}>Selesai</option>
                    <option value="CANCELLED" {{ $status == 'CANCELLED' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-900 text-center">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 min-w-[700px]">
                <thead class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80 rounded-xl">
                    <tr>
                        <th class="p-3.5">Kode Trip</th>
                        <th class="p-3.5">Tgl Dinas</th>
                        <th class="p-3.5">Mobil (Nopol)</th>
                        <th class="p-3.5">Driver</th>
                        <th class="p-3.5">Tujuan</th>
                        <th class="p-3.5">Penumpang</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($trips as $t)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3.5 font-mono font-extrabold text-kalbe-700">{{ $t->txtTripCode }}</td>
                        <td class="p-3.5 font-semibold text-slate-900">{{ $t->dtmTripDate ? $t->dtmTripDate->format('d M Y') : '-' }}</td>
                        <td class="p-3.5">
                            <span class="font-bold text-slate-900 block">{{ $t->vehicle ? $t->vehicle->txtVehicleName : '-' }}</span>
                            <span class="text-[11px] font-semibold text-slate-400">{{ $t->vehicle ? $t->vehicle->txtPlateNumber : '-' }}</span>
                        </td>
                        <td class="p-3.5 font-bold text-slate-800">{{ $t->driver ? $t->driver->txtDriverName : '-' }}</td>
                        <td class="p-3.5 font-medium text-slate-800 max-w-[180px] truncate" title="{{ $t->txtDestination }}">{{ $t->txtDestination }}</td>
                        <td class="p-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                <i class="fa-solid fa-users text-[9px] mr-1"></i> {{ $t->passengers->count() }} Orang
                            </span>
                        </td>
                        <td class="p-3.5">
                            @if ($t->txtTripStatus === 'IN_PROGRESS')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 animate-pulse">
                                <i class="fa-solid fa-car text-[9px] mr-1"></i> Sedang Berjalan
                            </span>
                            @elseif ($t->txtTripStatus === 'SCHEDULED')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                <i class="fa-solid fa-calendar text-[9px] mr-1"></i> Dijadwalkan
                            </span>
                            @elseif ($t->txtTripStatus === 'COMPLETED')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                <i class="fa-solid fa-check text-[9px] mr-1"></i> Selesai
                            </span>
                            @elseif ($t->txtTripStatus === 'CANCELLED')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                                Dibatalkan
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $t->txtTripStatus }}
                            </span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right space-x-1.5">
                            <a href="{{ route('admin.trips.show', $t->intDutyTrip_ID) }}" class="px-3 py-1.5 rounded-lg kalbe-gradient text-white text-xs font-bold shadow-xs hover:opacity-95 inline-block">
                                Detail & Foto
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-400">Tidak ada jadwal perjalanan dinas ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $trips->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.booking-checkbox');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        }
    });

    function openAssignModal() {
        const selected = document.querySelectorAll('.booking-checkbox:checked');
        if (selected.length === 0) {
            alert('Silakan pilih minimal 1 pengajuan dinas karyawan pada checkbox.');
            return;
        }
        document.getElementById('assignModal').classList.remove('hidden');
    }

    function closeAssignModal() {
        document.getElementById('assignModal').classList.add('hidden');
    }

    function assignSingle(id, date, vehicleId) {
        document.querySelectorAll('.booking-checkbox').forEach(cb => cb.checked = false);
        const cb = document.querySelector(`.booking-checkbox[value="${id}"]`);
        if (cb) cb.checked = true;

        if (date) document.getElementById('modalTripDate').value = date;
        if (vehicleId) document.getElementById('modalVehicleSelect').value = vehicleId;

        document.getElementById('assignModal').classList.remove('hidden');
    }
</script>
@endpush