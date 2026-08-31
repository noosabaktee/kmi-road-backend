@extends('layouts.admin')

@section('title', 'Master Akun Driver (Aplikasi Mobile) | KMI Road')
@section('header_title', 'Master Data & Akun Driver Mobile')

@section('content')
<div class="space-y-6">
    <div class="p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5 sm:space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3 sm:gap-4">
            <div>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900">Daftar Akun Driver Operasional</h2>
                <p class="text-[11px] sm:text-xs text-slate-500">Kelola akun dan kredensial login driver untuk masuk ke aplikasi Android (Flutter).</p>
            </div>
            <button type="button" onclick="openAddDriverModal()" class="w-full sm:w-auto justify-center px-4 py-2 text-xs font-bold rounded-xl kalbe-gradient text-white shadow-md shadow-kalbe-500/20 hover:opacity-95 flex items-center space-x-2 flex-shrink-0">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah Akun Driver Baru</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 min-w-[650px]">
                <thead class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80 rounded-xl">
                    <tr>
                        <th class="p-3.5">Nama Driver</th>
                        <th class="p-3.5">No. WhatsApp / HP</th>
                        <th class="p-3.5">Nomor SIM</th>
                        <th class="p-3.5">Email Login App</th>
                        <th class="p-3.5">Total Tugas</th>
                        <th class="p-3.5">Status Bertugas</th>
                        <th class="p-3.5">Status Akun</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($drivers as $d)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3.5">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full kalbe-gradient text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($d->txtDriverName, 0, 2)) }}
                                </div>
                                <span class="font-bold text-slate-900">{{ $d->txtDriverName }}</span>
                            </div>
                        </td>
                        <td class="p-3.5 font-semibold text-slate-800">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $d->txtPhoneNumber) }}" target="_blank" class="text-emerald-700 hover:underline flex items-center space-x-1">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>{{ $d->txtPhoneNumber }}</span>
                            </a>
                        </td>
                        <td class="p-3.5 font-mono text-slate-700 font-semibold">{{ $d->txtLicenseNumber }}</td>
                        <td class="p-3.5 font-medium text-slate-600">{{ $d->txtEmail }}</td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $d->total_trips }} Dinas
                            </span>
                        </td>
                        <td class="p-3.5">
                            @if ($d->txtStatus === 'ON_DUTY')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 animate-pulse">
                                <i class="fa-solid fa-car text-[9px] mr-1"></i> Sedang Dinas
                            </span>
                            @elseif ($d->txtStatus === 'AVAILABLE')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Siap / Tersedia
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                Istirahat / Off
                            </span>
                            @endif
                        </td>
                        <td class="p-3.5">
                            @if ($d->bitActive)
                            <span class="text-emerald-600 font-bold text-xs">Aktif</span>
                            @else
                            <span class="text-red-500 font-bold text-xs">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right space-x-2">
                            <button type="button" onclick='editDriver(@json($d))' class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">
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

<!-- Modal Tambah / Edit Driver -->
<div id="driverModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-extrabold text-sm sm:text-base text-slate-900" id="modalTitle">Tambah Akun Driver Baru</h3>
            <button type="button" onclick="closeDriverModal()" class="text-slate-400 hover:text-slate-600 p-1"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form id="driverForm" action="{{ route('admin.drivers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div id="methodSpoof"></div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Driver <span class="text-red-500">*</span></label>
                <input type="text" name="txtDriverName" id="txtDriverName" required placeholder="Contoh: Pak Joko Santoso" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. WhatsApp / HP <span class="text-red-500">*</span></label>
                    <input type="tel" name="txtPhoneNumber" id="txtPhoneNumber" required placeholder="081234567890" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor SIM <span class="text-red-500">*</span></label>
                    <input type="text" name="txtLicenseNumber" id="txtLicenseNumber" required placeholder="SIM-A-9281729" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold focus:ring-2 focus:ring-kalbe-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Login Android <span class="text-red-500">*</span></label>
                    <input type="email" name="txtEmail" id="txtEmail" required placeholder="driver@kmi.kalbe.co.id" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1" id="passwordLabel">Kata Sandi <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="passwordInput" placeholder="Min. 6 Karakter" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-kalbe-500">
                </div>
            </div>

            <div id="statusGroup" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Bertugas</label>
                    <select name="txtStatus" id="txtStatus" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs">
                        <option value="AVAILABLE">Tersedia (AVAILABLE)</option>
                        <option value="ON_DUTY">Sedang Dinas (ON_DUTY)</option>
                        <option value="OFF">Istirahat / Off</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Akun</label>
                    <select name="bitActive" id="bitActive" class="w-full p-2.5 rounded-xl border border-slate-200 text-xs">
                        <option value="1">Aktif</option>
                        <option value="0">Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2 sm:space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeDriverModal()" class="w-full sm:w-auto px-4 py-2 text-xs font-bold rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-center">Batal</button>
                <button type="submit" class="w-full sm:w-auto px-6 py-2 text-xs font-bold rounded-xl kalbe-gradient text-white shadow-md shadow-kalbe-500/20 hover:opacity-95 text-center">Simpan Data Driver</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAddDriverModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Akun Driver Baru';
        document.getElementById('driverForm').action = "{{ route('admin.drivers.store') }}";
        document.getElementById('methodSpoof').innerHTML = '';
        document.getElementById('txtDriverName').value = '';
        document.getElementById('txtPhoneNumber').value = '';
        document.getElementById('txtLicenseNumber').value = '';
        document.getElementById('txtEmail').value = '';
        document.getElementById('passwordInput').required = true;
        document.getElementById('passwordLabel').innerHTML = 'Kata Sandi <span class="text-red-500">*</span>';
        document.getElementById('statusGroup').classList.add('hidden');
        document.getElementById('driverModal').classList.remove('hidden');
    }

    function editDriver(d) {
        document.getElementById('modalTitle').innerText = 'Edit Akun Driver: ' + d.txtDriverName;
        document.getElementById('driverForm').action = "/admin/drivers/" + d.intDriver_ID;
        document.getElementById('methodSpoof').innerHTML = '@method("PUT")';
        document.getElementById('txtDriverName').value = d.txtDriverName;
        document.getElementById('txtPhoneNumber').value = d.txtPhoneNumber;
        document.getElementById('txtLicenseNumber').value = d.txtLicenseNumber;
        document.getElementById('txtEmail').value = d.txtEmail;
        document.getElementById('passwordInput').required = false;
        document.getElementById('passwordLabel').innerText = 'Ganti Kata Sandi (Kosongkan jika tetap)';
        document.getElementById('txtStatus').value = d.txtStatus;
        document.getElementById('bitActive').value = d.bitActive;
        document.getElementById('statusGroup').classList.remove('hidden');
        document.getElementById('driverModal').classList.remove('hidden');
    }

    function closeDriverModal() {
        document.getElementById('driverModal').classList.add('hidden');
    }
</script>
@endpush