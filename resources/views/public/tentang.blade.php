@extends('layouts.app')

@section('title', 'Tentang Platform ' . \App\Models\SystemSetting::appName())

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    
    <div class="text-center space-y-3">
        <span class="text-amber-600 font-bold text-xs uppercase tracking-widest">Portal Layanan Publik Pemerintah</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-navy-900">Tentang {{ \App\Models\SystemSetting::appName() }}</h1>
        <p class="text-slate-600 text-sm max-w-2xl mx-auto">Sistem Inovasi Pelayanan Publik untuk Pengaduan, Pemantauan, dan Transparansi Penanganan Kerusakan Jalan Umum.</p>
    </div>

    <!-- Vision & Mission -->
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm space-y-6">
        <div class="flex items-center space-x-4">
            <img src="{{ \App\Models\SystemSetting::getLogo() }}" alt="{{ \App\Models\SystemSetting::appName() }} Logo" class="w-12 h-12 rounded-2xl object-contain bg-navy-900 border border-slate-700 p-0.5 shadow">
            <div>
                <h2 class="text-2xl font-bold text-navy-900">{{ \App\Models\SystemSetting::appName() }}: {{ \App\Models\SystemSetting::appSlogan() }}</h2>
                <p class="text-xs text-slate-500 font-medium">Bersama menjaga dan memantau kondisi jalan di sekitar kita secara transparan.</p>
            </div>
        </div>

        <div class="space-y-4 text-sm text-slate-700 leading-relaxed">
            <p>
                <strong>{{ \App\Models\SystemSetting::appName() }}</strong> adalah platform teknologi layanan publik yang dirancang untuk mengatasi tantangan keterlambatan dan ketidakpastian dalam penanganan kerusakan jalan umum. Melalui sistem ini, aduan masyarakat tidak hanya dicatat, namun dianalisis dan diurutkan prioritasnya berdasarkan prinsip keadilan dan kebutuhan mendesak masyarakat.
            </p>
            <p>
                Berbeda dari platform pengaduan konvensional, <strong>{{ \App\Models\SystemSetting::appName() }}</strong> memberikan transparansi penuh dari minggu ke minggu, di mana masyarakat dapat melihat perkembangan fisik pengerjaan jalan (Kondisi Awal hingga selesai) secara visual.
            </p>
        </div>
    </div>

    <!-- Fitur Unggulan Sistem -->
    <div class="space-y-6">
        <div class="text-center max-w-3xl mx-auto space-y-2">
            <span class="text-amber-600 font-bold text-xs uppercase tracking-widest">Inovasi Layanan Publik</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy-900 tracking-tight">
                Fitur Unggulan Sistem {{ \App\Models\SystemSetting::appName() }}
            </h2>
            <p class="text-slate-600 text-sm">
                Dirancang khusus untuk monitoring infrastruktur jalan dengan teknologi AI Computer Vision dan Decision Support System terdepan.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Feature 1: YOLO Vision AI -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm hover:border-amber-500/50 hover:shadow-md transition duration-200 space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-2xl border border-amber-500/30">
                    <i class="fa-solid fa-brain"></i>
                </div>
                <h3 class="text-lg font-bold text-navy-900">Analisis Foto AI (YOLO)</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                    Sistem mendeteksi jenis cacat jalan seperti pothole (lubang), crack (retakan), dan landslide (longsor) secara otomatis beserta estimasi kerusakan.
                </p>
            </div>

            <!-- Feature 2: TOPSIS Ranking -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm hover:border-sky-500/50 hover:shadow-md transition duration-200 space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-500 flex items-center justify-center text-2xl border border-sky-500/30">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <h3 class="text-lg font-bold text-navy-900">Rekomendasi Prioritas (TOPSIS)</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                    Sistem Pendukung Keputusan multi-kriteria berbasis parameter bobot dinamis untuk memberikan peringkat jalan mana yang wajib didahulukan secara adil dan transparan.
                </p>
            </div>

            <!-- Feature 3: Weekly Progress Timeline -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm hover:border-emerald-500/50 hover:shadow-md transition duration-200 space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-2xl border border-emerald-500/30">
                    <i class="fa-solid fa-images"></i>
                </div>
                <h3 class="text-lg font-bold text-navy-900">Transparansi Progres Mingguan</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                    Dokumentasi visual foto kondisi awal, progres mingguan berkala, hingga selesai (100%) yang dapat dilihat bebas oleh publik melalui lightbox gallery.
                </p>
            </div>
        </div>
    </div>

    <!-- Privacy Card -->
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm space-y-4">
        <div class="w-12 h-12 rounded-2xl bg-navy-900 text-sky-400 flex items-center justify-center text-xl">
            <i class="fa-solid fa-user-lock"></i>
        </div>
        <h3 class="text-xl font-bold text-navy-900">Perlindungan Privasi Pelapor</h3>
        <p class="text-sm text-slate-600 leading-relaxed max-w-3xl">
            Portal JALAN KU menjamin kerahasiaan identitas pelapor. Pada setiap tampilan publik, nama lengkap, nomor telepon, dan email pelapor otomatis disamarkan untuk melindungi privasi dan rasa aman masyarakat dalam menyampaikan aspirasi publik.
        </p>
    </div>

</div>
@endsection