@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem - ' . \App\Models\SystemSetting::appName())
@section('header_title', 'Konfigurasi & Pengaturan Sistem')

@section('content')
<div class="space-y-6 max-w-4xl">
    
    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-xl font-extrabold text-navy-900">Pengaturan Umum & Identitas {{ \App\Models\SystemSetting::appName() }}</h2>
            <p class="text-xs text-slate-500">Konfigurasi branding, upload logo sistem resmi, nama platform, slogan, dan parameter geolokasi.</p>
        </div>

        <form method="POST" action="{{ route('superadmin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- System Logo & Visual Branding Section -->
            <div class="bg-gradient-to-r from-navy-950 to-slate-900 text-white rounded-2xl p-6 border border-slate-800 shadow-inner space-y-4">
                <div>
                    <h3 class="text-sm font-extrabold text-white flex items-center space-x-2">
                        <i class="fa-solid fa-image text-amber-400"></i>
                        <span>Logo & Foto Identitas Sistem</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Logo ini akan otomatis disinkronkan ke seluruh halaman: Navbar Publik, Footer, Sidebar Portal Petugas/Admin, dan Layar Login.</p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-6 pt-2">
                    <!-- Current Logo Display / Preview -->
                    <div class="relative group">
                        <div class="w-24 h-24 rounded-2xl bg-navy-900 border-2 border-amber-500/40 p-1 flex items-center justify-center overflow-hidden shadow-lg shadow-black/40">
                            <img id="system-logo-preview" src="{{ \App\Models\SystemSetting::getLogo() }}" alt="Logo Sistem" class="w-full h-full object-contain rounded-xl">
                        </div>
                        <span class="absolute -bottom-2 -right-2 bg-amber-500 text-navy-950 text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow">
                            Aktif
                        </span>
                    </div>

                    <!-- Upload Controls -->
                    <div class="flex-1 space-y-3 w-full">
                        <div class="flex flex-wrap items-center gap-3">
                            <label for="app_logo_input" class="cursor-pointer px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-navy-950 font-bold text-xs rounded-xl shadow transition inline-flex items-center space-x-2">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <span>Pilih File Logo Baru</span>
                            </label>
                            <input type="file" id="app_logo_input" name="app_logo" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" class="hidden" onchange="previewSystemLogo(this)">

                            @if(\App\Models\SystemSetting::get('app_logo'))
                                <label class="inline-flex items-center space-x-1.5 text-xs text-rose-400 hover:text-rose-300 cursor-pointer">
                                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-700 text-rose-600 focus:ring-rose-500">
                                    <span>Reset ke Logo Default</span>
                                </label>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Format yang didukung: <strong>PNG, JPG, JPEG, WEBP, SVG</strong> (Maksimal 2 MB). Disarankan menggunakan rasio 1:1 atau gambar logo dengan background transparan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Text & General Settings -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- System Title -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Platform Sistem</label>
                    <input type="text" name="app_name" value="{{ \App\Models\SystemSetting::get('app_name', 'JALAN KU') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <span class="text-[10px] text-slate-400">Contoh: JALAN KU, JALAN saya, atau Nama Dinas</span>
                </div>

                <!-- Slogan -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Slogan Resmi</label>
                    <input type="text" name="app_slogan" value="{{ \App\Models\SystemSetting::get('app_slogan', 'Laporkan. Pantau. Perbaiki.') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <span class="text-[10px] text-slate-400">Ditampilkan di bawah nama aplikasi pada header</span>
                </div>

                <!-- Max Photos Initial -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Batas Maksimal Foto Laporan</label>
                    <input type="number" name="max_report_photos" value="3" readonly class="w-full px-4 py-2.5 bg-slate-200 border border-slate-300 rounded-xl text-xs font-semibold text-slate-500 cursor-not-allowed">
                    <span class="text-[10px] text-slate-400">Terkunci: 3 foto per laporan sesuai regulasi SOP.</span>
                </div>

                <!-- Max Photos Progress -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Batas Maksimal Foto Progress Mingguan</label>
                    <input type="number" name="max_progress_photos" value="3" readonly class="w-full px-4 py-2.5 bg-slate-200 border border-slate-300 rounded-xl text-xs font-semibold text-slate-500 cursor-not-allowed">
                    <span class="text-[10px] text-slate-400">Terkunci: 3 foto per update progres.</span>
                </div>

                <!-- Default Center Map Lat -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Default Center Latitude Peta</label>
                    <input type="text" name="default_map_lat" value="{{ \App\Models\SystemSetting::get('default_map_lat', '-6.9200000') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Default Center Map Lng -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Default Center Longitude Peta</label>
                    <input type="text" name="default_map_lng" value="{{ \App\Models\SystemSetting::get('default_map_lng', '107.6250000') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function previewSystemLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('system-logo-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
