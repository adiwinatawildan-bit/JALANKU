@extends('layouts.admin')

@section('title', 'Master Data OPD - JALAN KU')
@section('header_title', 'Kelola Organisasi Perangkat Daerah (OPD)')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-navy-900">Master Data OPD Pelaksana</h2>
            <p class="text-xs text-slate-500">Daftar dinas dan instansi teknis yang berwenang melakukan perbaikan infrastruktur jalan.</p>
        </div>
        <button type="button" onclick="openModal('modal-add-opd')" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-navy-950 font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Tambah OPD Baru</span>
        </button>
    </div>

    <!-- OPD Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Nama OPD / Dinas</th>
                        <th class="py-3.5 px-4">Kontak & Email</th>
                        <th class="py-3.5 px-4">Alamat Kantor</th>
                        <th class="py-3.5 px-4 text-center">Petugas</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($opds as $opd)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4 font-mono font-bold text-amber-600">
                                {{ $opd->code }}
                            </td>
                            <td class="py-4 px-4 font-bold text-navy-900">
                                {{ $opd->name }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="block">{{ $opd->phone ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $opd->email ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-4 text-slate-600 max-w-xs truncate">
                                {{ $opd->address ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-center font-bold">
                                {{ $opd->users_count }} Petugas
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($opd->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right space-x-1.5 whitespace-nowrap">
                                <button type="button" onclick="editOpd({{ json_encode($opd) }})" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] rounded-lg transition inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-pen-to-square text-amber-500"></i>
                                    <span>Edit</span>
                                </button>
                                <form method="POST" action="{{ route('superadmin.opds.delete', $opd->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus OPD {{ $opd->name }} secara permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 font-bold text-[11px] rounded-lg border border-rose-200 transition inline-flex items-center space-x-1">
                                        <i class="fa-solid fa-trash-can"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada OPD terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Add OPD -->
<div id="modal-add-opd" class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl border border-slate-200">
        <h3 class="text-base font-bold text-navy-900 border-b border-slate-100 pb-3">Tambah OPD Baru</h3>
        <form method="POST" action="{{ route('superadmin.opds.store') }}" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Nama OPD / Dinas <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Dinas Bina Marga dan Penataan Ruang" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Kode Singkat <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="Contoh: DBMPR-01" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Nomor Telepon</label>
                    <input type="text" name="phone" placeholder="022-xxxxxxx" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Email Instansi</label>
                <input type="email" name="email" placeholder="opd@jalanku.go.id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Alamat Kantor</label>
                <textarea name="address" rows="2" placeholder="Alamat lengkap instansi..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-add-opd')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow">Simpan OPD</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit OPD -->
<div id="modal-edit-opd" class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl border border-slate-200">
        <h3 class="text-base font-bold text-navy-900 border-b border-slate-100 pb-3">Edit OPD</h3>
        <form id="form-edit-opd" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Nama OPD</label>
                <input type="text" id="edit-opd-name" name="name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Kode</label>
                    <input type="text" id="edit-opd-code" name="code" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Telepon</label>
                    <input type="text" id="edit-opd-phone" name="phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Email</label>
                    <input type="email" id="edit-opd-email" name="email" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Status</label>
                    <select id="edit-opd-is-active" name="is_active" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Alamat</label>
                <textarea id="edit-opd-address" name="address" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-edit-opd')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow">Update OPD</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function editOpd(opd) {
        document.getElementById('form-edit-opd').action = '/super-admin/opd/' + opd.id;
        document.getElementById('edit-opd-name').value = opd.name;
        document.getElementById('edit-opd-code').value = opd.code;
        document.getElementById('edit-opd-phone').value = opd.phone || '';
        document.getElementById('edit-opd-email').value = opd.email || '';
        document.getElementById('edit-opd-address').value = opd.address || '';
        document.getElementById('edit-opd-is-active').value = opd.is_active ? 1 : 0;
        openModal('modal-edit-opd');
    }
</script>
@endpush
