@extends('layouts.app')

@section('title', 'Detail Pengaduan: ' . $report->ticket_number . ' - JALAN KU')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- Top Nav -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('masyarakat.reports.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-slate-600 hover:text-amber-600">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Laporan</span>
        </a>
        <div class="flex items-center space-x-2 text-xs">
            <span class="font-mono bg-navy-900 text-amber-400 font-bold px-3 py-1 rounded-lg">#{{ $report->ticket_number }}</span>
            <span class="px-3 py-1 rounded-lg font-bold {{ $report->status_badge_class }} border">{{ $report->status }}</span>
        </div>
    </div>

    <!-- Overview Banner -->
    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <span class="text-xs font-bold text-amber-600 uppercase tracking-widest">{{ $report->kecamatan }}, {{ $report->desa }}</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 mt-1">{{ $report->road_name }}</h1>
                <p class="text-sm text-slate-600 mt-1">{{ $report->title }}</p>
            </div>
            
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center min-w-[160px]">
                <p class="text-[11px] font-bold text-slate-500 uppercase">Progres Perbaikan</p>
                <p class="text-3xl font-extrabold text-navy-900 mt-0.5">{{ $report->current_progress }}%</p>
                <div class="w-full h-2 bg-slate-200 rounded-full mt-2 overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $report->current_progress }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block font-semibold">Jenis Cacat Jalan</span>
                <span class="font-bold text-navy-900">{{ ucfirst($report->damage_type) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">Tingkat Gangguan</span>
                <span class="font-bold text-navy-900">{{ ucfirst($report->disturbance_level) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">OPD Pelaksana</span>
                <span class="font-bold text-navy-900">{{ $report->opd?->name ?? 'Dalam Proses Penugasan' }}</span>
            </div>
        </div>
    </div>

    <!-- Timeline Transparansi Mingguan -->
    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm space-y-8">
        <h2 class="text-xl font-bold text-navy-900 flex items-center border-b border-slate-100 pb-4">
            <i class="fa-solid fa-timeline text-amber-500 mr-2.5"></i> Perkembangan Penanganan Jalan
        </h2>

        <div class="space-y-10">
            
            <!-- Tahap 1: Foto Kondisi Awal -->
            <div class="timeline-step pl-10">
                <div class="absolute left-0 top-0 w-8 h-8 rounded-full bg-navy-900 text-amber-400 flex items-center justify-center font-bold text-xs shadow border-2 border-white">
                    01
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-bold text-navy-900">Kondisi Awal Laporan Pengaduan</h3>
                        <span class="text-xs text-slate-400">{{ $report->created_at->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                    <p class="text-xs text-slate-600">{{ $report->description }}</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        @foreach($report->initialPhotos as $photo)
                            <a href="{{ $photo->file_url }}" class="glightbox block relative rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition h-40 bg-slate-900" data-gallery="citizen-timeline">
                                <img src="{{ $photo->file_url }}" class="w-full h-full object-cover">
                                <span class="absolute bottom-1 left-2 text-[10px] font-bold text-white bg-navy-950/80 px-2 py-0.5 rounded">{{ $photo->caption ?? 'Foto Awal' }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Progres Mingguan -->
            @foreach($report->progressUpdates as $upd)
                <div class="timeline-step pl-10">
                    <div class="absolute left-0 top-0 w-8 h-8 rounded-full {{ $upd->progress_percentage >= 100 ? 'bg-emerald-600 text-white' : 'bg-navy-900 text-amber-400' }} flex items-center justify-center font-bold text-xs shadow border-2 border-white">
                        {{ $upd->week_number }}
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <h3 class="text-base font-bold text-navy-900">Minggu {{ $upd->week_number }} (Progres: {{ $upd->progress_percentage }}%)</h3>
                            <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($upd->date)->translatedFormat('d F Y') }}</span>
                        </div>
                        <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200">{{ $upd->description }}</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                            @foreach($upd->photos as $pPhoto)
                                <a href="{{ $pPhoto->file_url }}" class="glightbox block relative rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition h-40 bg-slate-900" data-gallery="citizen-timeline">
                                    <img src="{{ $pPhoto->file_url }}" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 left-2 text-[10px] font-bold text-white bg-navy-950/80 px-2 py-0.5 rounded">{{ $pPhoto->caption ?? 'Dokumentasi Progres' }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    <!-- 5. FEEDBACK MASYARAKAT SETELAH SELESAI -->
    @if($report->status === \App\Models\Report::STATUS_SELESAI)
        <div class="bg-gradient-to-tr from-emerald-950 to-navy-950 text-white rounded-3xl p-8 border border-emerald-800 shadow-xl space-y-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-navy-950 flex items-center justify-center font-bold text-xl">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Ulasan & Feedback Kepuasan Anda</h3>
                    <p class="text-xs text-slate-300">Berikan penilaian terhadap hasil pekerjaan perbaikan jalan yang telah diselesaikan.</p>
                </div>
            </div>

            @if($report->citizen_rating)
                <div class="bg-white/10 p-6 rounded-2xl border border-white/10 space-y-2">
                    <p class="text-xs font-bold text-amber-400 uppercase tracking-wider">Penilaian Anda Telah Dikirim:</p>
                    <div class="flex text-amber-400 text-lg">
                        @for($s=1; $s<=5; $s++)
                            <i class="fa-solid fa-star {{ $s <= $report->citizen_rating ? 'text-amber-400' : 'text-slate-600' }}"></i>
                        @endfor
                    </div>
                    <p class="text-sm text-slate-200 italic">"{{ $report->citizen_feedback }}"</p>
                </div>
            @else
                <form method="POST" action="{{ route('masyarakat.reports.feedback', $report->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Beri Bintang Kepuasan (1 - 5)</label>
                        <select name="citizen_rating" required class="bg-slate-900 border border-slate-700 text-amber-400 font-bold text-xs rounded-xl px-4 py-2.5 focus:outline-none">
                            <option value="5">⭐⭐⭐⭐⭐ 5 Bintang - Sangat Puas & Cepat</option>
                            <option value="4">⭐⭐⭐⭐ 4 Bintang - Puas</option>
                            <option value="3">⭐⭐⭐ 3 Bintang - Cukup</option>
                            <option value="2">⭐⭐ 2 Bintang - Kurang Memuaskan</option>
                            <option value="1">⭐ 1 Bintang - Tidak Puas</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Komentar / Masukan</label>
                        <textarea name="citizen_feedback" rows="3" required placeholder="Tuliskan pengalaman atau masukan Anda..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-navy-950 font-bold text-xs rounded-xl transition">
                        Kirim Feedback Sekarang
                    </button>
                </form>
            @endif
        </div>
    @endif

</div>
@endsection
