@extends('layouts.app')

@section('title', 'Alur Cara Kerja Sistem - ' . \App\Models\SystemSetting::appName())

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    
    <div class="text-center space-y-3">
        <span class="text-amber-600 font-bold text-xs uppercase tracking-widest">Panduan Pengguna & Alur Proses</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-navy-900">Bagaimana Cara Kerja {{ \App\Models\SystemSetting::appName() }}?</h1>
        <p class="text-slate-600 text-sm max-w-2xl mx-auto">Sistem pengaduan dan pemantauan kerusakan jalan umum dirancang dengan 5 tahap transparan dari aduan warga hingga selesai diperbaiki.</p>
    </div>

    <!-- 5 Steps Container -->
    <div class="space-y-8">
        
        <!-- Step 1 -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="w-16 h-16 rounded-2xl bg-navy-900 text-amber-400 font-extrabold text-2xl flex items-center justify-center shrink-0 shadow-lg">
                01
            </div>
            <div class="space-y-2">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Tahap 1: Pengaduan Masyarakat</span>
                <h3 class="text-xl font-bold text-navy-900">Masyarakat Melaporkan Kondisi Jalan Rusak</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Warga dapat mengambil foto kerusakan jalan di lokasi (maksimal 3 foto kondisi awal), menentukan titik koordinat GPS secara presisi menggunakan peta interaktif, serta mengisi rincian seperti nama jalan, kecamatan, desa, dan tingkat gangguan.
                </p>
                <div class="pt-2 flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                    <span class="bg-slate-100 px-3 py-1 rounded-full"><i class="fa-solid fa-camera text-amber-500 mr-1"></i> Maks. 3 Foto Awal</span>
                    <span class="bg-slate-100 px-3 py-1 rounded-full"><i class="fa-solid fa-map-pin text-rose-500 mr-1"></i> Titik Koordinat GPS</span>
                    <span class="bg-slate-100 px-3 py-1 rounded-full"><i class="fa-solid fa-shield-halved text-sky-500 mr-1"></i> Privasi Pelapor Terlindungi</span>
                </div>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="w-16 h-16 rounded-2xl bg-navy-900 text-sky-400 font-extrabold text-2xl flex items-center justify-center shrink-0 shadow-lg">
                02
            </div>
            <div class="space-y-2">
                <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Tahap 2: Verifikasi & Duplikasi</span>
                <h3 class="text-xl font-bold text-navy-900">Admin Memeriksa dan Memverifikasi Laporan</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Tim admin memeriksa keabsahan foto dan deskripsi laporan. Jika terdapat banyak laporan di ruas jalan yang sama/berdekatan, sistem secara otomatis mengelompokkan laporan sebagai satu klaster prioritas.
                </p>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="w-16 h-16 rounded-2xl bg-navy-900 text-purple-400 font-extrabold text-2xl flex items-center justify-center shrink-0 shadow-lg">
                03
            </div>
            <div class="space-y-2">
                <span class="text-xs font-bold text-purple-600 uppercase tracking-wider">Tahap 3: AI Vision & SPK TOPSIS</span>
                <h3 class="text-xl font-bold text-navy-900">Analisis Otomatis AI & Perhitungan Prioritas TOPSIS</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Model AI mendeteksi jenis kerusakan jalan seperti lubang (pothole), retakan (crack), dan longsor (landslide) beserta estimasi kerusakan. Selanjutnya, algoritma SPK TOPSIS menghitung nilai preferensi berdasarkan kriteria prioritas untuk menghasilkan urutan pengerjaan yang adil dan transparan bagi pengambil keputusan.
                </p>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="w-16 h-16 rounded-2xl bg-navy-900 text-orange-400 font-extrabold text-2xl flex items-center justify-center shrink-0 shadow-lg">
                04
            </div>
            <div class="space-y-2">
                <span class="text-xs font-bold text-orange-600 uppercase tracking-wider">Tahap 4: Pelaksanaan Lapangan</span>
                <h3 class="text-xl font-bold text-navy-900">Penugasan OPD & Pelaksanaan Perbaikan Fisik</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Admin menugaskan laporan ke OPD teknis berwenang (misal Dinas Bina Marga). Tim lapangan melakukan survei teknis dan memulai tahapan perbaikan fisik di lokasi.
                </p>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="w-16 h-16 rounded-2xl bg-navy-900 text-emerald-400 font-extrabold text-2xl flex items-center justify-center shrink-0 shadow-lg">
                05
            </div>
            <div class="space-y-2">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Tahap 5: Transparansi Publik</span>
                <h3 class="text-xl font-bold text-navy-900">Pemantauan Progres Mingguan Hingga Selesai</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Setiap minggu, petugas mengunggah update progres secara berkala hingga selesai (100%) lengkap dengan foto dokumentasi. Masyarakat dapat melihat seluruh riwayat perbaikan secara transparan dan memberikan ulasan/rating.
                </p>
            </div>
        </div>

    </div>

    <!-- CTA Box -->
    <div class="text-center pt-6">
        <a href="{{ Auth::check() ? route('masyarakat.reports.create') : route('login') }}" class="inline-flex items-center space-x-2 px-8 py-4 bg-amber-500 hover:bg-amber-600 text-navy-950 font-bold text-base rounded-2xl shadow-xl shadow-amber-500/25 transition">
            <i class="fa-solid fa-plus-circle mr-1"></i>
            <span>Mulai Buat Laporan Pengaduan</span>
        </a>
    </div>

</div>
@endsection