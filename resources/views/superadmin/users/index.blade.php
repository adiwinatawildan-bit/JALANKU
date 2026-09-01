@extends('layouts.admin')

@section('title', 'User Management - ' . \App\Models\SystemSetting::appName())
@section('header_title', 'Kelola Pengguna Sistem')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action & Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-navy-900">Manajemen Pengguna</h2>
            <p class="text-xs text-slate-500">Kelola akun Masyarakat, Admin, OPD/Petugas, dan Super Admin beserta foto profil.</p>
        </div>
        <button type="button" onclick="openModal('modal-add-user')" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-navy-950 font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah User Baru</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / email / HP..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div>
                <select name="role_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                    <option value="">Semua Peran (Role)</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>{{ $r->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
                    Filter Pengguna
                </button>
                <a href="{{ route('superadmin.users.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Email & Kontak</th>
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4">Instansi OPD</th>
                        <th class="py-3.5 px-4">Status Akun</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-200 overflow-hidden flex items-center justify-center border border-slate-300 shrink-0">
                                        @if($user->getAvatar())
                                            <img src="{{ $user->getAvatar() }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fa-solid fa-user text-xs text-slate-400"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-navy-900">{{ $user->name }}</p>
                                        <span class="text-[10px] text-slate-400 font-normal">ID: #{{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="block text-slate-700">{{ $user->email }}</span>
                                <span class="text-[10px] text-slate-400">{{ $user->phone ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800 uppercase border border-slate-200">
                                    {{ $user->role->display_name }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-slate-600">
                                {{ $user->opd?->name ?? '-' }}
                            </td>
                            <td class="py-4 px-4">
                                @if($user->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right space-x-1.5 whitespace-nowrap">
                                <button type="button" onclick="editUser({{ json_encode($user) }}, '{{ $user->getAvatar() ?? '' }}')" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] rounded-lg transition inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-pen-to-square text-amber-500"></i>
                                    <span>Edit</span>
                                </button>
                                @if($user->id !== Auth::id())
                                    <form method="POST" action="{{ route('superadmin.users.delete', $user->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }} secara permanen? Data user akan dihapus total dari database.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 font-bold text-[11px] rounded-lg border border-rose-200 transition inline-flex items-center space-x-1">
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

</div>

<!-- Modal Add User -->
<div id="modal-add-user" class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
        <h3 class="text-base font-bold text-navy-900 border-b border-slate-100 pb-3">Tambah Pengguna Baru</h3>
        <form method="POST" action="{{ route('superadmin.users.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">No. HP / WhatsApp</label>
                    <input type="text" name="phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Role / Peran <span class="text-rose-500">*</span></label>
                    <select name="role_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">OPD Terkait (Jika Ada)</label>
                    <select name="opd_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                        <option value="">-- Tidak Terikat OPD --</option>
                        @foreach($opds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Foto Profil Pengguna (Opsional)</label>
                <input type="file" name="avatar" accept="image/png,image/jpeg,image/jpg,image/webp" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                <span class="text-[10px] text-slate-400">Format: JPG, PNG, WEBP. Maks 2 MB.</span>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Password <span class="text-rose-500">*</span></label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-add-user')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow">Simpan User</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="modal-edit-user" class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
        <h3 class="text-base font-bold text-navy-900 border-b border-slate-100 pb-3">Edit Pengguna</h3>
        <form id="form-edit-user" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Nama Lengkap</label>
                <input type="text" id="edit-name" name="name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Email</label>
                    <input type="email" id="edit-email" name="email" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">No. HP</label>
                    <input type="text" id="edit-phone" name="phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Role</label>
                    <select id="edit-role-id" name="role_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">OPD</label>
                    <select id="edit-opd-id" name="opd_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                        <option value="">-- Non-OPD --</option>
                        @foreach($opds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Status</label>
                    <select id="edit-is-active" name="is_active" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Avatar upload & preview -->
            <div class="space-y-2 bg-slate-50 p-3 rounded-2xl border border-slate-200">
                <label class="block text-xs font-bold text-slate-700">Ubah / Pasang Foto Profil</label>
                <div class="flex items-center space-x-3">
                    <div id="edit-avatar-preview-wrap" class="w-12 h-12 rounded-full bg-slate-200 border border-slate-300 overflow-hidden flex items-center justify-center shrink-0">
                        <img id="edit-avatar-img" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                        <i id="edit-avatar-icon" class="fa-solid fa-user text-slate-400"></i>
                    </div>
                    <div class="flex-1 space-y-1">
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/jpg,image/webp" class="w-full text-xs">
                        <label id="edit-remove-avatar-wrap" class="hidden items-center space-x-1.5 text-[11px] text-rose-600 cursor-pointer">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded text-rose-600">
                            <span>Hapus Foto Profil Saat Ini</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Password Baru (Opsional)</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-edit-user')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow">Update User</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    function editUser(user, avatarUrl) {
        document.getElementById('form-edit-user').action = '/super-admin/users/' + user.id;
        document.getElementById('edit-name').value = user.name;
        document.getElementById('edit-email').value = user.email;
        document.getElementById('edit-phone').value = user.phone || '';
        document.getElementById('edit-role-id').value = user.role_id;
        document.getElementById('edit-opd-id').value = user.opd_id || '';
        document.getElementById('edit-is-active').value = user.is_active ? 1 : 0;

        const avatarImg = document.getElementById('edit-avatar-img');
        const avatarIcon = document.getElementById('edit-avatar-icon');
        const removeWrap = document.getElementById('edit-remove-avatar-wrap');

        if (avatarUrl) {
            avatarImg.src = avatarUrl;
            avatarImg.classList.remove('hidden');
            avatarIcon.classList.add('hidden');
            removeWrap.classList.remove('hidden');
            removeWrap.classList.add('inline-flex');
        } else {
            avatarImg.src = '';
            avatarImg.classList.add('hidden');
            avatarIcon.classList.remove('hidden');
            removeWrap.classList.add('hidden');
            removeWrap.classList.remove('inline-flex');
        }

        openModal('modal-edit-user');
    }
</script>
@endpush
