@extends('layouts.admin')

@section('title', 'Master Kendaraan Operasional Dinas | KMI Road')
@section('header_title', 'Master Kendaraan Operasional')

@section('content')
<div class="space-y-6">
    <div class="p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5 sm:space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3 sm:gap-4">
            <div>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900">Daftar Armada Mobil Dinas</h2>
                <p class="text-[11px] sm:text-xs text-slate-500">Atur nomor polisi, kapasitas maksimal tempat duduk (kursi), dan status operasional kendaraan.</p>
            </div>
            <button type="button" onclick="openAddVehicleModal()" class="w-full sm:w-auto justify-center px-4 py-2 text-xs font-bold rounded-xl kalbe-gradient text-white shadow-md shadow-kalbe-500/20 hover:opacity-95 flex items-center space-x-2 flex-shrink-0">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Kendaraan Baru</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 min-w-[700px]">
                <thead class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80 rounded-xl">
                    <tr>
                        <th class="p-3.5">Nama Kendaraan</th>
                        <th class="p-3.5">Nomor Polisi (Nopol)</th>
                        <th class="p-3.5">Merek / Tipe</th>
                        <th class="p-3.5">Kapasitas Kursi (Max Seat)</th>
                        <th class="p-3.5">Odometer Saat Ini</th>
                        <th class="p-3.5">Bahan Bakar</th>
                        <th class="p-3.5">Status Armada</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($vehicles as $v)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3.5 font-bold text-slate-900">{{ $v->txtVehicleName }}</td>
                        <td class="p-3.5 font-mono font-extrabold text-kalbe-700 bg-slate-50 px-2.5 py-1 rounded inline-block my-2">{{ $v->txtPlateNumber }}</td>
                        <td class="p-3.5 font-medium text-slate-800">{{ $v->txtBrandModel }} ({{ $v->txtVehicleType }})</td>
                        <td class="p-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-kalbe-50 text-kalbe-700 border border-kalbe-200">
                                <i class="fa-solid fa-chair text-[10px] mr-1.5"></i> {{ $v->intMaxSeat }} Kursi
                            </span>
                        </td>
                        <td class="p-3.5 font-mono font-semibold text-slate-700">{{ number_format($v->intCurrentOdometer, 0, ',', '.') }} KM</td>
                        <td class="p-3.5 font-medium text-slate-700">{{ $v->txtFuelType }}</td>
                        <td class="p-3.5">
                            @if ($v->txtStatus === 'IN_USE')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                Sedang Dinas
                            </span>
                            @elseif ($v->txtStatus === 'AVAILABLE')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Tersedia
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $v->txtStatus }}
                            </span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right space-x-2">
                            <button type="button" onclick='editVehicle(@json($v))' class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">
                                Edit
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Kendaraan -->
<div id="vehicleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-extrabold text-sm sm:text-base text-slate-900" id="modalTitle">Tambah Kendaraan Dinas</h3>
            <button type="button" onclick="closeVehicleModal()" class="text-slate-400 hover:text-slate-600 p-1"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form id="vehicleForm" action="{{ route('admin.vehicles.store') }}" method="POST" class="space-y-4">
            @csrf
            <div id="methodSpoof"></div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kendaraan <span class="text-red-500">*</span></label>
                <input type="text" name="txtVehicleName" id="txtVehicleName" required placeholder="Contoh: Innova Zenix V Silver" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Polisi <span class="text-red-500">*</span></label>
                    <input type="text" name="txtPlateNumber" id="txtPlateNumber" required placeholder="Contoh: B 1024 KMI" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs uppercase font-mono font-bold focus:ring-2 focus:ring-kalbe-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Maksimal Kursi (Seat) <span class="text-red-500">*</span></label>
                    <input type="number" name="intMaxSeat" id="intMaxSeat" required min="1" max="60" value="7" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-kalbe-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Merek & Tipe <span class="text-red-500">*</span></label>
                    <input type="text" name="txtBrandModel" id="txtBrandModel" required placeholder="Contoh: Toyota Innova 2.0" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kendaraan</label>
                    <select name="txtVehicleType" id="txtVehicleType" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                        <option value="MPV">MPV</option>
                        <option value="SUV">SUV</option>
                        <option value="Minibus">Minibus / HiAce</option>
                        <option value="Van Logistik">Van Logistik</option>
                        <option value="Sedan">Sedan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bahan Bakar</label>
                    <select name="txtFuelType" id="txtFuelType" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                        <option value="Pertalite">Pertalite</option>
                        <option value="Pertamax">Pertamax</option>
                        <option value="Dexlite">Dexlite</option>
                        <option value="Solar">Solar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Odometer Terakhir (KM)</label>
                    <input type="number" name="intCurrentOdometer" id="intCurrentOdometer" required min="0" value="0" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                </div>
            </div>

            <div id="statusGroup" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Operasional</label>
                    <select name="txtStatus" id="txtStatus" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs">
                        <option value="AVAILABLE">Tersedia (AVAILABLE)</option>
                        <option value="IN_USE">Sedang Dinas (IN_USE)</option>
                        <option value="MAINTENANCE">Perawatan / Servis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Aktif</label>
                    <select name="bitActive" id="bitActive" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs">
                        <option value="1">Aktif</option>
                        <option value="0">Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2 sm:space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeVehicleModal()" class="w-full sm:w-auto px-4 py-2 text-xs font-bold rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-center">Batal</button>
                <button type="submit" class="w-full sm:w-auto px-6 py-2 text-xs font-bold rounded-xl kalbe-gradient text-white shadow-md shadow-kalbe-500/20 hover:opacity-95 text-center">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAddVehicleModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Kendaraan Dinas Baru';
        document.getElementById('vehicleForm').action = "{{ route('admin.vehicles.store') }}";
        document.getElementById('methodSpoof').innerHTML = '';
        document.getElementById('txtVehicleName').value = '';
        document.getElementById('txtPlateNumber').value = '';
        document.getElementById('txtBrandModel').value = '';
        document.getElementById('intMaxSeat').value = '7';
        document.getElementById('intCurrentOdometer').value = '0';
        document.getElementById('statusGroup').classList.add('hidden');
        document.getElementById('vehicleModal').classList.remove('hidden');
    }

    function editVehicle(v) {
        document.getElementById('modalTitle').innerText = 'Edit Data Kendaraan: ' + v.txtVehicleName;
        document.getElementById('vehicleForm').action = "/admin/vehicles/" + v.intVehicle_ID;
        document.getElementById('methodSpoof').innerHTML = '@method("PUT")';
        document.getElementById('txtVehicleName').value = v.txtVehicleName;
        document.getElementById('txtPlateNumber').value = v.txtPlateNumber;
        document.getElementById('txtBrandModel').value = v.txtBrandModel;
        document.getElementById('txtVehicleType').value = v.txtVehicleType;
        document.getElementById('intMaxSeat').value = v.intMaxSeat;
        document.getElementById('txtFuelType').value = v.txtFuelType;
        document.getElementById('intCurrentOdometer').value = v.intCurrentOdometer;
        document.getElementById('txtStatus').value = v.txtStatus;
        document.getElementById('bitActive').value = v.bitActive;
        document.getElementById('statusGroup').classList.remove('hidden');
        document.getElementById('vehicleModal').classList.remove('hidden');
    }

    function closeVehicleModal() {
        document.getElementById('vehicleModal').classList.add('hidden');
    }
</script>
@endpush