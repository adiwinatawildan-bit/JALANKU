@extends('layouts.app')

@section('title', 'Profil Pengguna - ' . \App\Models\SystemSetting::appName())

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-xl space-y-8">
        
        <!-- Header User Profile -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-5 border-b border-slate-100 pb-6 text-center sm:text-left">
            <div class="w-20 h-20 rounded-2xl bg-navy-900 text-amber-400 flex items-center justify-center text-3xl font-bold shadow-lg overflow-hidden border-2 border-amber-500/30 shrink-0">
                @if($user->getAvatar())
                    <img src="{{ $user->getAvatar() }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <i class="fa-solid fa-user"></i>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-navy-900">{{ $user->name }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">Peran Akun: <strong class="text-amber-600 uppercase">{{ $user->role->display_name }}</strong> • Terdaftar sejak {{ $user->created_at->format('d M Y') }}</p>
                <div class="flex flex-wrap gap-2 mt-2 justify-center sm:justify-start">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-navy-950 text-amber-400 border border-slate-700 uppercase">
                        {{ $user->role->name }}
                    </span>
                    @if($user->opd)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                            {{ $user->opd->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Upload Foto Profil Section -->
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 space-y-4">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-camera text-amber-500"></i>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Foto Profil Pengguna</h3>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-5">
                    <!-- Avatar Preview Box -->
                    <div class="w-20 h-20 rounded-2xl bg-slate-200 border-2 border-slate-300 overflow-hidden flex items-center justify-center shadow-inner shrink-0">
                        <img id="avatar-preview" src="{{ $user->getAvatar() ?: asset('images/logo.png') }}" alt="Preview Foto" class="w-full h-full object-cover {{ $user->getAvatar() ? '' : 'opacity-40 grayscale' }}">
                    </div>

                    <!-- Upload Controls -->
                    <div class="space-y-2 text-center sm:text-left flex-1">
                        <div class="flex flex-wrap items-center gap-3 justify-center sm:justify-start">
                            <label for="avatar_input" class="cursor-pointer px-4 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition inline-flex items-center space-x-2">
                                <i class="fa-solid fa-upload"></i>
                                <span>Pilih Foto Profil Baru</span>
                            </label>
                            <input type="file" id="avatar_input" name="avatar" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden" onchange="previewUserAvatar(this)">

                            @if($user->hasAvatar())
                                <label class="inline-flex items-center space-x-1.5 text-xs text-rose-600 hover:text-rose-700 cursor-pointer font-medium">
                                    <input type="checkbox" name="remove_avatar" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    <span>Hapus Foto Profil</span>
                                </label>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500">Mendukung format JPG, PNG, atau WEBP. Maksimal ukuran 2 MB.</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Akun -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Nama Lengkap -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Email (Readonly) -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email (Akun)</label>
                    <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-2.5 bg-slate-200 border border-slate-300 rounded-xl text-xs font-semibold text-slate-500 cursor-not-allowed">
                </div>

                <!-- Telepon -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- OPD Asal jika ada -->
                @if($user->opd)
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Instansi / OPD</label>
                        <input type="text" value="{{ $user->opd->name }}" disabled class="w-full px-4 py-2.5 bg-slate-200 border border-slate-300 rounded-xl text-xs font-semibold text-slate-500 cursor-not-allowed">
                    </div>
                @endif
            </div>

            <!-- Ubah Password -->
            <div class="border-t border-slate-100 pt-6 space-y-4">
                <h3 class="text-sm font-bold text-navy-900 flex items-center space-x-2">
                    <i class="fa-solid fa-key text-amber-500"></i>
                    <span>Ubah Password (Kosongkan jika tidak ingin mengganti)</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password Baru</label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>

        </form>

    </div>

</div>

<script>
function previewUserAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            preview.src = e.target.result;
            preview.classList.remove('opacity-40', 'grayscale');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
