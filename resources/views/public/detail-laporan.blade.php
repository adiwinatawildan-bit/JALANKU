@extends('layouts.app')

@section('title', 'Transparansi Perbaikan: ' . $report->road_name . ' - JALAN KU')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- Top Back Navigation & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('public.reports.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-amber-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Laporan Publik</span>
        </a>
        <div class="flex items-center space-x-2 text-xs text-slate-500">
            <span class="font-mono bg-slate-200 text-slate-800 px-2.5 py-1 rounded-lg font-bold">Tiket: {{ $report->ticket_number }}</span>
            <span>•</span>
            <span>Diajukan: {{ $report->created_at->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <!-- 31. HALAMAN DETAIL LAPORAN PUBLIK HEADER -->
    <div class="bg-navy-950 text-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-slate-800 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-8 space-y-4">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="px-3.5 py-1 rounded-full text-xs font-bold shadow {{ $report->status_badge_class }} border">
                        {{ $report->status }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-amber-300 border border-slate-700">
                        <i class="fa-solid fa-triangle-exclamation mr-1.5 text-amber-400"></i> {{ ucfirst($report->damage_type) }} (Tingkat: {{ ucfirst($report->disturbance_level) }})
                    </span>
                    @if($report->priorityResult)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $report->priorityResult->badge_class }} border">
                            {{ $report->priorityResult->priority_level }} (Skor: {{ $report->priorityResult->score }})
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white uppercase">
                    {{ $report->road_name }}
                </h1>

                <p class="text-base text-slate-300 font-medium leading-relaxed">
                    {{ $report->title }}
                </p>

                <div class="flex flex-wrap items-center gap-6 pt-2 text-xs text-slate-300">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-location-dot text-amber-400 text-sm"></i>
                        <span>{{ $report->location?->address_detail ?? $report->road_name }}, {{ $report->kecamatan }}, {{ $report->desa }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-building-user text-sky-400 text-sm"></i>
                        <span>OPD Pelaksana: <strong class="text-white">{{ $report->opd?->name ?? 'Dinas Terkait (Dalam Penugasan)' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Progress Meter Widget -->
            <div class="lg:col-span-4 bg-slate-900/90 rounded-2xl p-6 border border-slate-800 text-center space-y-3">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Progres Pengerjaan</p>
                <p class="text-4xl font-extrabold text-amber-400">{{ $report->current_progress }}%</p>
                
                <!-- Visual Bar (Section 31 representation) -->
                <div class="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-amber-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ $report->current_progress }}%"></div>
                </div>

                <p class="text-xs text-slate-400">
                    @if($report->status === \App\Models\Report::STATUS_SELESAI)
                        <span class="text-emerald-400 font-bold"><i class="fa-solid fa-circle-check mr-1"></i> Perbaikan Tuntas</span>
                    @else
                        Tahap: <strong class="text-white">{{ $report->status }}</strong>
                    @endif
                </p>
            </div>

        </div>
    </div>

    <!-- 29 & 31. TIMELINE & DOKUMENTASI FOTO MINGGUAN -->
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm space-y-10">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-6 gap-4">
            <div>
                <span class="text-amber-600 font-bold text-xs uppercase tracking-widest">Dokumentasi Transparan</span>
                <h2 class="text-2xl font-extrabold text-navy-900 mt-1">Timeline & Perkembangan Perbaikan Mingguan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Setiap tahap menyajikan maksimal 3 foto dokumentasi yang dapat diklik untuk melihat resolusi penuh.</p>
            </div>
            <div class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700">
                <i class="fa-solid fa-camera mr-1.5 text-amber-500"></i> Klik foto untuk perbesar (Lightbox)
            </div>
        </div>

        <!-- TIMELINE FLOW CONTAINER -->
        <div class="space-y-12">
            
            <!-- STAGE 1: KONDISI AWAL (Foto Pengaduan Masyarakat) -->
            <div class="timeline-step pl-10">
                <!-- Icon marker -->
                <div class="absolute left-0 top-0 w-8 h-8 rounded-full bg-navy-900 text-amber-400 flex items-center justify-center font-bold text-xs shadow-md border-2 border-white">
                    <i class="fa-solid fa-flag"></i>
                </div>

                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-lg border border-amber-200">Tahap 01</span>
                            <h3 class="text-lg font-bold text-navy-900 mt-1">KONDISI AWAL</h3>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">
                            <i class="fa-regular fa-calendar mr-1.5 text-amber-500"></i> {{ $report->created_at->translatedFormat('d F Y') }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-600 max-w-3xl leading-relaxed">
                        {{ $report->description }}
                    </p>

                    <!-- Initial Photos Gallery (Max 3) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        @forelse($report->initialPhotos as $photo)
                            <a href="{{ $photo->file_url }}" class="glightbox block relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition photo-card-zoom bg-slate-900 h-48 group" data-gallery="timeline-gallery">
                                <img src="{{ $photo->file_url }}" alt="{{ $photo->caption ?? 'Foto Kondisi Awal' }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                <div class="absolute inset-0 bg-navy-950/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                    <i class="fa-solid fa-magnifying-glass-plus text-2xl text-amber-400"></i>
                                </div>
                                <div class="absolute bottom-0 inset-x-0 bg-navy-950/80 p-2 text-center">
                                    <span class="text-[11px] font-bold text-slate-200">{{ $photo->caption ?? 'Foto Kondisi Awal' }}</span>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs text-slate-400 italic">Tidak ada foto kondisi awal yang tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- STAGE 2: SURVEI LAPANGAN (Jika ada) -->
            @if($report->surveyPhotos->count() > 0 || $report->survey_notes)
                <div class="timeline-step pl-10">
                    <div class="absolute left-0 top-0 w-8 h-8 rounded-full bg-navy-900 text-sky-400 flex items-center justify-center font-bold text-xs shadow-md border-2 border-white">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>

                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-sky-600 bg-sky-50 px-2.5 py-0.5 rounded-lg border border-sky-200">Survei Teknis</span>
                                <h3 class="text-lg font-bold text-navy-900 mt-1">SURVEI LAPANGAN OPD</h3>
                            </div>
                            @if($report->survey_at)
                                <span class="text-xs font-semibold text-slate-500">
                                    <i class="fa-regular fa-calendar mr-1.5 text-sky-500"></i> {{ $report->survey_at->translatedFormat('d F Y') }}
                                </span>
                            @endif
                        </div>

                        @if($report->survey_notes)
                            <div class="p-4 bg-sky-50/60 rounded-2xl border border-sky-100 text-xs text-sky-950 leading-relaxed">
                                <strong>Catatan Tim Survei:</strong> {{ $report->survey_notes }}
                            </div>
                        @endif

                        <!-- Survey Photos (Max 3) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                            @foreach($report->surveyPhotos as $sPhoto)
                                <a href="{{ $sPhoto->file_url }}" class="glightbox block relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition photo-card-zoom bg-slate-900 h-48 group" data-gallery="timeline-gallery">
                                    <img src="{{ $sPhoto->file_url }}" alt="{{ $sPhoto->caption }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    <div class="absolute inset-0 bg-navy-950/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                        <i class="fa-solid fa-magnifying-glass-plus text-2xl text-sky-400"></i>
                                    </div>
                                    <div class="absolute bottom-0 inset-x-0 bg-navy-950/80 p-2 text-center">
                                        <span class="text-[11px] font-bold text-slate-200">{{ $sPhoto->caption }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- STAGE 3, 4, 5...: WEEKLY PROGRESS UPDATES (Minggu 1, 2, 3, 4) -->
            @forelse($report->progressUpdates as $progress)
                <div class="timeline-step pl-10">
                    <div class="absolute left-0 top-0 w-8 h-8 rounded-full {{ $progress->progress_percentage >= 100 ? 'bg-emerald-600 text-white' : 'bg-navy-900 text-amber-400' }} flex items-center justify-center font-bold text-xs shadow-md border-2 border-white">
                        {{ $progress->week_number }}
                    </div>

                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider {{ $progress->progress_percentage >= 100 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-amber-700 bg-amber-50 border-amber-200' }} px-2.5 py-0.5 rounded-lg border">
                                    Progres {{ $progress->progress_percentage }}%
                                </span>
                                <h3 class="text-lg font-bold text-navy-900 mt-1">
                                    MINGGU {{ $progress->week_number }}
                                    @if($progress->progress_percentage >= 100)
                                        <span class="text-sm font-extrabold text-emerald-600 ml-2">(SELESAI)</span>
                                    @endif
                                </h3>
                            </div>
                            <span class="text-xs font-semibold text-slate-500">
                                <i class="fa-regular fa-calendar mr-1.5 text-amber-500"></i> {{ \Carbon\Carbon::parse($progress->date)->translatedFormat('d F Y') }}
                            </span>
                        </div>

                        <!-- Progress percentage bar -->
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ $progress->progress_percentage }}%"></div>
                        </div>

                        <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <strong>Keterangan Pekerjaan:</strong> {{ $progress->description }}
                        </p>

                        <!-- Weekly Photos (Max 3 per week) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                            @forelse($progress->photos as $pPhoto)
                                <a href="{{ $pPhoto->file_url }}" class="glightbox block relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition photo-card-zoom bg-slate-900 h-48 group" data-gallery="timeline-gallery">
                                    <img src="{{ $pPhoto->file_url }}" alt="{{ $pPhoto->caption }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    <div class="absolute inset-0 bg-navy-950/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                        <i class="fa-solid fa-magnifying-glass-plus text-2xl text-amber-400"></i>
                                    </div>
                                    <div class="absolute bottom-0 inset-x-0 bg-navy-950/80 p-2 text-center">
                                        <span class="text-[11px] font-bold text-slate-200">{{ $pPhoto->caption ?? 'Minggu ' . $progress->week_number }}</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-xs text-slate-400 italic">Dokumentasi foto sedang diproses.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                @if($report->status !== \App\Models\Report::STATUS_SELESAI)
                    <div class="pl-10 text-xs text-slate-500 italic">
                        <i class="fa-solid fa-clock mr-1 text-amber-500"></i> Laporan saat ini berada pada tahap <strong>{{ $report->status }}</strong>. Dokumentasi mingguan akan tampil setelah pekerjaan fisik dimulai oleh OPD.
                    </div>
                @endif
            @endforelse

            <!-- STAGE FINAL: STATUS SELESAI -->
            @if($report->status === \App\Models\Report::STATUS_SELESAI)
                <div class="timeline-step pl-10">
                    <div class="absolute left-0 top-0 w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-md border-2 border-white">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <div class="p-6 bg-emerald-50 rounded-3xl border border-emerald-200 space-y-2">
                        <div class="flex items-center space-x-2 text-emerald-800 font-extrabold text-base">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                            <span>PEKERJAAN PERBAIKAN TELAH SELESAI 100%</span>
                        </div>
                        <p class="text-xs text-emerald-900 leading-relaxed">
                            Ruas jalan pada laporan ini telah selesai ditangani secara menyeluruh dan telah diverifikasi oleh tim pengawas infrastruktur pada tanggal {{ $report->completed_at ? $report->completed_at->translatedFormat('d F Y') : 'Terbaru' }}.
                        </p>
                        @if($report->citizen_rating)
                            <div class="pt-3 border-t border-emerald-200 flex items-center space-x-2 text-xs">
                                <span class="font-bold text-emerald-950">Feedback Masyarakat:</span>
                                <div class="flex text-amber-500">
                                    @for($s=1; $s<=5; $s++)
                                        <i class="fa-solid fa-star {{ $s <= $report->citizen_rating ? 'text-amber-500' : 'text-slate-300' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-slate-600 italic">"{{ $report->citizen_feedback }}"</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- 32. KEBIJAKAN PRIVASI & DATA TRANSPARANSI -->
    <div class="bg-slate-100 rounded-2xl p-4 border border-slate-200 text-xs text-slate-500 flex items-start space-x-3">
        <i class="fa-solid fa-shield-halved text-sky-600 text-lg mt-0.5"></i>
        <p>
            <strong>Prinsip Privasi & Keterbukaan Informasi Publik:</strong> Data pelapor (nama lengkap, nomor HP, email, dan identitas pribadi lainnya) dilindungi dan disamarkan dari publik sesuai Undang-Undang Keterbukaan Informasi Publik dan Kebijakan Privasi Portal JALAN KU.
        </p>
    </div>

</div>
@endsection
