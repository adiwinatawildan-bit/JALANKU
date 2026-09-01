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