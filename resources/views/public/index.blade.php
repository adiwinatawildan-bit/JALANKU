@extends('layouts.app')

@section('title', \App\Models\SystemSetting::appName() . ' - ' . \App\Models\SystemSetting::appSlogan() . ' Bersama Menjaga Kondisi Jalan')

@section('content')

<!-- 11. HERO SECTION -->
<section class="relative bg-navy-900 text-white overflow-hidden py-20 lg:py-28">
    <!-- Background Gradient & Road Elements -->
    <div class="absolute inset-0 bg-gradient-to-br from-navy-950 via-navy-900 to-slate-900 opacity-95"></div>
    <div class="absolute inset-0 bg-[radial-gradient(#f59e0b_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <!-- Badge -->
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold tracking-wide uppercase">
                    <i class="fa-solid fa-satellite-dish animate-pulse"></i>
                    <span>Sistem Layanan Publik Pengaduan Terpadu</span>
                </div>

                <!-- Main Slogan (Section 11) -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                    {{ \App\Models\SystemSetting::appName() }}
                    <span class="block text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-300 mt-2">
                        {{ \App\Models\SystemSetting::appSlogan() }}
                    </span>
                </h1>

                <!-- Sub-slogan -->
                <p class="text-lg sm:text-xl text-slate-300 max-w-2xl font-normal leading-relaxed">
                    Bersama menjaga dan memantau kondisi jalan di sekitar kita secara transparan.
                </p>

                <!-- Action Buttons -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center lg:justify-start space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ Auth::check() ? route('masyarakat.reports.create') : route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-7 py-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-navy-950 font-extrabold text-base shadow-xl shadow-amber-500/30 hover:scale-105 active:scale-95 transition duration-200">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        <span>Laporkan Kerusakan</span>
                    </a>
                    
                    <a href="{{ route('public.peta') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-7 py-4 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-base border border-white/20 hover:border-white/40 transition duration-200">
                        <i class="fa-solid fa-map-location-dot text-amber-400 text-lg"></i>
                        <span>Lihat Peta Jalan</span>
                    </a>
                </div>

                <!-- Micro Highlights -->
                <div class="pt-6 border-t border-slate-800/80 text-left">
                    <div class="inline-flex items-center space-x-3 bg-slate-800/60 px-4 py-2 rounded-xl border border-slate-700/60">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">Transparansi Publik</span>
                            <span class="text-sm font-bold text-white flex items-center">
                                <i class="fa-solid fa-check text-emerald-400 mr-1.5"></i> Update Mingguan
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Graphic / Interactive Preview -->
            <div class="lg:col-span-5 relative">
                @php
                    $liveFeedData = $liveFeedReports->map(function($r) {
                        $stages = [];
                        
                        // 1. Kondisi Awal
                        $initialPhoto = $r->initialPhotos->first()?->file_url ?? $r->photos->first()?->file_url;
                        $stages[] = [
                            'key' => 'awal',
                            'short_name' => 'Awal',
                            'label' => 'Kondisi Awal',
                            'progress' => 'Kondisi Awal Dilaporkan',
                            'photo' => $initialPhoto,
                            'caption' => 'Kondisi awal saat pertama dilaporkan'
                        ];

                        // 2. Survei Teknis (if surveyed or later stage)
                        $surveyPhoto = $r->surveyPhotos->first()?->file_url;
                        $hasSurvey = in_array($r->status, [
                            \App\Models\Report::STATUS_SURVEI,
                            \App\Models\Report::STATUS_MENUNGGU_PERBAIKAN,
                            \App\Models\Report::STATUS_SEDANG_DIPERBAIKI,
                            \App\Models\Report::STATUS_SELESAI
                        ]) || !empty($r->survey_notes) || !empty($surveyPhoto);

                        if ($hasSurvey) {
                            $surveyDesc = !empty($r->survey_notes) ? $r->survey_notes : 'Survei Teknis & Pengukuran Lapangan';
                            $stages[] = [
                                'key' => 'survei',
                                'short_name' => 'Survei',
                                'label' => 'Tahap Survei',
                                'progress' => 'Survei: ' . $surveyDesc,
                                'photo' => $surveyPhoto ?? $initialPhoto,
                                'caption' => $surveyDesc
                            ];
                        }

                        // 3. Progres Mingguan Dinamis Dari OPD (M-1, M-2, M-3, M-4, M-5, dst...)
                        $updates = $r->progressUpdates->sortBy('week_number');
                        foreach ($updates as $up) {
                            $wNum = $up->week_number;
                            $upPhoto = $up->photos->first()?->file_url;
                            $upDesc = !empty($up->description) ? $up->description : "Pengerjaan fisik jalan minggu ke-{$wNum}";
                            
                            $stages[] = [
                                'key' => "m-{$wNum}",
                                'short_name' => "M-{$wNum}",
                                'label' => "Minggu ke-{$wNum}",
                                'progress' => "M-{$wNum}: {$upDesc}",
                                'photo' => $upPhoto ?? $initialPhoto,
                                'caption' => $upDesc
                            ];
                        }

                        // 4. Selesai (Hanya jika status laporan sudah Selesai atau ada dokumentasi penyelesaian)
                        $isFinished = ($r->status === \App\Models\Report::STATUS_SELESAI);
                        if ($isFinished) {
                            $finalPhoto = $updates->last()?->photos?->first()?->file_url 
                                ?? $initialPhoto;

                            $finalDesc = !empty($r->final_repair_notes) 
                                ? $r->final_repair_notes 
                                : 'Jalan Selesai Diperbaiki Mulus (Lolos Uji)';

                            $stages[] = [
                                'key' => 'selesai',
                                'short_name' => 'Selesai',
                                'label' => 'Selesai Diperbaiki',
                                'progress' => $finalDesc,
                                'photo' => $finalPhoto,
                                'caption' => $finalDesc
                            ];
                        }

                        $activeIdx = max(0, count($stages) - 1);

                        return [
                            'id' => $r->id,
                            'ticket_number' => $r->ticket_number,
                            'road_name' => $r->road_name,
                            'kecamatan' => $r->kecamatan,
                            'opd_name' => $r->opd?->name ?? 'Dinas Pekerjaan Umum & Penataan Ruang',
                            'status' => $r->status,
                            'status_class' => $r->status_badge_class,
                            'detail_url' => route('public.reports.show', $r->id),
                            'damage_type' => $r->damage_type,
                            'stages' => $stages,
                            'active_stage_index' => $activeIdx,
                        ];
                    });
                @endphp

                <div class="relative mx-auto max-w-md rounded-2xl bg-gradient-to-tr from-slate-800/90 to-navy-900/90 p-5 sm:p-6 border border-slate-700 shadow-2xl backdrop-blur-xl space-y-4">
                    
                    <div class="flex items-center justify-between border-b border-slate-700/80 pb-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-semibold text-slate-300 ml-2">Transparansi Live Feed</span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-ping"></span> Live Monitoring
                        </span>
                    </div>

                    <div class="bg-navy-950/90 rounded-xl p-4 border border-slate-800 space-y-3.5">
                        
                        <!-- Custom Sleek Road Selector Dropdown -->
                        <div class="relative" id="custom-live-dropdown-container">
                            <label class="block text-[10px] font-bold text-amber-400 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                <span><i class="fa-solid fa-road mr-1 text-amber-400"></i> Pilih Ruas Jalan Yang Dipantau:</span>
                                <span class="text-slate-400 font-normal">({{ $liveFeedReports->count() }} Laporan Aktif)</span>
                            </label>

                            <!-- Dropdown Trigger Button -->
                            <button type="button" id="live-dropdown-trigger" onclick="toggleLiveFeedDropdown()" class="w-full bg-slate-900 hover:bg-slate-850 text-left py-2.5 px-3 rounded-xl border border-slate-700 hover:border-amber-500/80 focus:outline-none focus:ring-2 focus:ring-amber-500/40 transition duration-200 flex items-center justify-between shadow-inner group">
                                <div class="flex items-center space-x-2.5 overflow-hidden">
                                    <span id="selected-road-status-badge" class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase shrink-0 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        {{ strtoupper($liveFeedReports->first()?->status ?? 'SEDANG DIPERBAIKI') }}
                                    </span>
                                    <span id="selected-road-text" class="text-xs font-bold text-white truncate group-hover:text-amber-300 transition">
                                        {{ $liveFeedReports->first()?->road_name ?? 'Pilih Ruas Jalan' }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0 ml-2">
                                    <span id="selected-road-ticket" class="text-[10px] font-mono text-slate-400 hidden sm:inline">
                                        #{{ $liveFeedReports->first()?->ticket_number }}
                                    </span>
                                    <i id="live-dropdown-arrow" class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"></i>
                                </div>
                            </button>

                            <!-- Custom Dropdown Menu -->
                            <div id="live-dropdown-menu" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-navy-950/98 backdrop-blur-xl border border-slate-700 rounded-2xl shadow-2xl z-30 p-2.5 space-y-2 overflow-hidden animate-in fade-in slide-in-from-top-1 duration-150">
                                <!-- Search inside dropdown -->
                                <div class="relative">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                    <input type="text" id="live-dropdown-search" oninput="filterLiveFeedDropdown(this.value)" placeholder="Cari nama jalan / kecamatan / tiket..." class="w-full pl-8 pr-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                </div>

                                <!-- Items List -->
                                <div id="live-dropdown-list" class="max-h-60 overflow-y-auto space-y-1.5 pr-1 custom-scrollbar">
                                    @forelse($liveFeedReports as $index => $lReport)
                                        @php
                                            $itemBadgeClass = match($lReport->status) {
                                                \App\Models\Report::STATUS_SEDANG_DIPERBAIKI => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                                \App\Models\Report::STATUS_SELESAI => 'bg-teal-500/20 text-teal-300 border-teal-500/30',
                                                \App\Models\Report::STATUS_SURVEI => 'bg-sky-500/20 text-sky-400 border-sky-500/30',
                                                \App\Models\Report::STATUS_MENUNGGU_PERBAIKAN, \App\Models\Report::STATUS_DITUGASKAN => 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30',
                                                default => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                            };
                                        @endphp
                                        <button type="button" 
                                                onclick="selectLiveFeedRoad({{ $index }})" 
                                                data-search="{{ strtolower($lReport->road_name . ' ' . $lReport->kecamatan . ' ' . $lReport->ticket_number . ' ' . $lReport->status) }}"
                                                class="live-dropdown-item w-full text-left p-2.5 rounded-xl transition flex items-center justify-between border {{ $index === 0 ? 'bg-amber-500/10 border-amber-500/40' : 'bg-slate-900/60 border-slate-800 hover:bg-slate-800 hover:border-slate-700' }} group">
                                            <div class="space-y-0.5 overflow-hidden pr-2">
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase border shrink-0 {{ $itemBadgeClass }}">
                                                        {{ strtoupper($lReport->status) }}
                                                    </span>
                                                    <p class="text-xs font-bold text-white truncate group-hover:text-amber-300 transition">
                                                        {{ $lReport->road_name }}
                                                    </p>
                                                </div>
                                                <p class="text-[10px] text-slate-400 truncate">
                                                    Kec. {{ $lReport->kecamatan }}
                                                </p>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold text-amber-400/90 shrink-0">
                                                #{{ $lReport->ticket_number }}
                                            </span>
                                        </button>
                                    @empty
                                        <div class="text-center py-4 text-xs text-slate-400">
                                            Tidak ada laporan aktif saat ini
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Active Road Meta -->
                        <div class="flex justify-between items-start pt-1 border-t border-slate-800/80">
                            <div>
                                <span id="live-feed-ticket" class="text-[10px] font-mono font-bold text-amber-400 uppercase">
                                    #{{ $liveFeedReports->first()?->ticket_number ?? 'JLK-202608-0001' }}
                                </span>
                                <h4 id="live-feed-road-name" class="text-sm font-bold text-white mt-0.5">
                                    {{ $liveFeedReports->first()?->road_name ?? 'Jalan Cikajang' }}
                                </h4>
                                <p id="live-feed-opd-name" class="text-xs text-slate-400">
                                    {{ $liveFeedReports->first()?->opd?->name ?? 'Dinas Pekerjaan Umum dan Penataan Ruang' }}
                                </p>
                            </div>
                            <span id="live-feed-status-badge" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 whitespace-nowrap">
                                {{ strtoupper($liveFeedReports->first()?->status ?? 'SEDANG DIPERBAIKI') }}
                            </span>
                        </div>

                        <!-- Interactive Photo Display -->
                        <div class="relative h-48 w-full rounded-xl overflow-hidden bg-slate-900 border border-slate-700/80 group">
                            <img id="live-feed-img" src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='600' height='400' viewBox='0 0 600 400'><rect width='600' height='400' fill='%231e293b'/><path d='M150 400 L250 150 L350 150 L450 400 Z' fill='%23334155'/><line x1='300' y1='150' x2='300' y2='400' stroke='%23fbbf24' stroke-width='4' stroke-dasharray='12 8'/><circle cx='270' cy='290' r='25' fill='%230f172a'/><text x='300' y='80' fill='%23f8fafc' font-size='18' font-family='sans-serif' font-weight='bold' text-anchor='middle'>Dokumentasi Progres Jalan</text></svg>" 
                                 alt="Live Feed Progres" 
                                 class="w-full h-full object-cover transition duration-300">
                            
                            <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between pointer-events-none">
                                <span id="live-feed-stage-label" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-navy-950/80 text-white backdrop-blur-sm border border-slate-700">
                                    Tahap: Selesai Diperbaiki
                                </span>
                                <span class="text-[11px] font-medium text-slate-300 bg-navy-950/80 px-2 py-0.5 rounded backdrop-blur-sm">
                                    <i class="fa-solid fa-camera mr-1 text-amber-400"></i> Dokumentasi
                                </span>
                            </div>
                        </div>

                        <!-- Progress Bar & Status Text (Uses OPD Custom Written Description) -->
                        <div>
                            <div class="flex justify-between items-center text-xs font-semibold text-slate-300 mb-1.5">
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider">Progres Lapangan:</span>
                                <span id="live-feed-progress-text" class="text-emerald-400 font-bold truncate max-w-[250px] text-right text-xs">
                                    Selesai Diperbaiki
                                </span>
                            </div>
                            <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                                <div id="live-feed-progress-bar" class="h-full bg-gradient-to-r from-amber-500 via-emerald-500 to-emerald-400 rounded-full transition-all duration-500" style="width: 100%"></div>
                            </div>
                        </div>

                        <!-- Dynamic Clickable Timeline Pills (Awal, Survei, M-1, M-2, M-3, ..., Selesai) -->
                        <div>
                            <p class="text-[10px] font-semibold text-slate-400 mb-1.5 flex items-center">
                                <i class="fa-solid fa-hand-pointer text-amber-400 mr-1 text-[9px]"></i> Klik untuk melihat tahapan pengerjaan:
                            </p>
                            <div id="live-feed-timeline-buttons" class="flex flex-wrap gap-1.5 text-center text-[10px] font-bold">
                                <button type="button" onclick="switchLiveFeedStage(0)" class="live-stage-btn flex-1 min-w-[50px] py-1.5 px-2 rounded transition bg-slate-800 text-slate-300 hover:bg-slate-700">Awal</button>
                                <button type="button" onclick="switchLiveFeedStage(1)" class="live-stage-btn flex-1 min-w-[50px] py-1.5 px-2 rounded transition bg-slate-800 text-slate-300 hover:bg-slate-700">Survei</button>
                                <button type="button" onclick="switchLiveFeedStage(2)" class="live-stage-btn flex-1 min-w-[50px] py-1.5 px-2 rounded transition bg-emerald-600 text-white shadow-sm shadow-emerald-500/50">Selesai</button>
                            </div>
                        </div>

                        <!-- Full Detail Link -->
                        <div class="pt-1 text-center">
                            <a id="live-feed-detail-link" href="{{ route('public.reports.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-amber-400 hover:text-amber-300 transition">
                                <span>Lihat Detail Laporan & Histori Lengkap</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- 13. STATISTIK LANDING PAGE (Real-time from MySQL) -->
<section class="relative -mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-20">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Total Pengaduan -->
        <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-200/80 hover:shadow-2xl transition duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengaduan</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">{{ number_format($stats['total_pengaduan']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Laporan kerusakan masuk</p>
        </div>

        <!-- Sedang Diproses -->
        <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-200/80 hover:shadow-2xl transition duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Diproses</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-extrabold text-amber-600 tracking-tight">{{ number_format($stats['sedang_diproses']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Verifikasi & survei teknis</p>
        </div>

        <!-- Sedang Diperbaiki -->
        <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-200/80 hover:shadow-2xl transition duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Diperbaiki</span>
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-person-digging"></i>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-extrabold text-orange-600 tracking-tight">{{ number_format($stats['sedang_diperbaiki']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Pekerjaan fisik oleh OPD</p>
        </div>

        <!-- Selesai -->
        <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-200/80 hover:shadow-2xl transition duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Selesai Diperbaiki</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <p class="text-3xl sm:text-4xl font-extrabold text-emerald-600 tracking-tight">{{ number_format($stats['selesai']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Lolos uji kelayakan 100%</p>
        </div>

    </div>
</section>

<!-- 14. CARA KERJA (5 Tahapan) -->
<section class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
        <span class="text-amber-600 font-bold text-xs uppercase tracking-widest">Alur Penanganan Transparan</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">
            Bagaimana JALAN KU Bekerja?
        </h2>
        <p class="text-slate-600 text-base">
            Dari laporan pertama Anda hingga perbaikan tuntas, setiap tahapan tercatat dan dapat dipantau oleh seluruh masyarakat.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 relative">
        
        <!-- Step 1 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition text-center space-y-3">
            <div class="w-12 h-12 rounded-xl bg-navy-900 text-amber-400 font-extrabold text-lg flex items-center justify-center mx-auto shadow-md">
                01
            </div>
            <h3 class="text-lg font-bold text-navy-900">Laporkan</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Masyarakat melaporkan kondisi jalan rusak dengan maksimal 3 foto awal dan titik GPS presisi.
            </p>
        </div>

        <!-- Step 2 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition text-center space-y-3">
            <div class="w-12 h-12 rounded-xl bg-navy-900 text-amber-400 font-extrabold text-lg flex items-center justify-center mx-auto shadow-md">
                02
            </div>
            <h3 class="text-lg font-bold text-navy-900">Verifikasi</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Admin memeriksa keaslian laporan, menandai duplikasi, dan mengelompokkan laporan lokasi berdekatan.
            </p>
        </div>

        <!-- Step 3 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition text-center space-y-3">
            <div class="w-12 h-12 rounded-xl bg-navy-900 text-amber-400 font-extrabold text-lg flex items-center justify-center mx-auto shadow-md">
                03
            </div>
            <h3 class="text-lg font-bold text-navy-900">Analisis AI & SPK</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                YOLO mendeteksi kerusakan foto dan TOPSIS menghitung skor prioritas.
            </p>
        </div>

        <!-- Step 4 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition text-center space-y-3">
            <div class="w-12 h-12 rounded-xl bg-navy-900 text-amber-400 font-extrabold text-lg flex items-center justify-center mx-auto shadow-md">
                04
            </div>
            <h3 class="text-lg font-bold text-navy-900">Perbaikan OPD</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Laporan ditugaskan ke OPD berwenang. Petugas melakukan survei dan memulai pengerjaan fisik.
            </p>
        </div>

        <!-- Step 5 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition text-center space-y-3">
            <div class="w-12 h-12 rounded-xl bg-navy-900 text-amber-400 font-extrabold text-lg flex items-center justify-center mx-auto shadow-md">
                05
            </div>
            <h3 class="text-lg font-bold text-navy-900">Pantau Mingguan</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Masyarakat memantau progres mingguan (Minggu 1 s/d Selesai) lengkap dengan foto dokumentasi.
            </p>
        </div>

    </div>
</section>

<!-- 56. FITUR UTAMA & PEMBEDA -->
<section class="py-20 bg-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="text-amber-400 font-bold text-xs uppercase tracking-widest">Inovasi Layanan Publik</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                Fitur Unggulan Sistem JALAN KU
            </h2>
            <p class="text-slate-400 text-base">
                Dirancang khusus untuk monitoring infrastruktur jalan dengan teknologi AI Computer Vision dan Decision Support System terdepan.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Feature 1: YOLO Vision AI -->
            <div class="bg-slate-800/80 rounded-2xl p-8 border border-slate-700 hover:border-amber-500/50 transition duration-200 space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-2xl border border-amber-500/30">
                    <i class="fa-solid fa-brain"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Analisis Foto AI (YOLO)</h3>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Sistem mendeteksi jenis cacat jalan seperti pothole (lubang), crack (retakan), dan landslide (longsor) secara otomatis beserta estimasi kerusakan.
                </p>
            </div>

            <!-- Feature 2: TOPSIS Ranking -->
            <div class="bg-slate-800/80 rounded-2xl p-8 border border-slate-700 hover:border-amber-500/50 transition duration-200 space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-2xl border border-sky-500/30">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Rekomendasi Prioritas (TOPSIS)</h3>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Sistem Pendukung Keputusan multi-kriteria berbasis parameter bobot dinamis untuk memberikan peringkat jalan mana yang wajib didahulukan secara adil dan transparan.
                </p>
            </div>

            <!-- Feature 3: Weekly Progress Timeline -->
            <div class="bg-slate-800/80 rounded-2xl p-8 border border-slate-700 hover:border-amber-500/50 transition duration-200 space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-2xl border border-emerald-500/30">
                    <i class="fa-solid fa-images"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Transparansi Progres Mingguan</h3>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Dokumentasi visual foto kondisi awal, progres mingguan berkala, hingga selesai (100%) yang dapat dilihat bebas oleh publik melalui lightbox gallery.
                </p>
            </div>

        </div>

    </div>
</section>

<!-- 56. PETA JALAN PREVIEW SECTION -->
<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
        <div>
            <span class="text-amber-600 font-bold text-xs uppercase tracking-widest">GIS Monitoring</span>
            <h2 class="text-3xl font-extrabold text-navy-900 mt-1">Peta Sebaran Kerusakan Jalan</h2>
            <p class="text-slate-600 text-sm mt-1">Pantau titik kerusakan dan status penanganan di seluruh wilayah secara real-time.</p>
        </div>
        <a href="{{ route('public.peta') }}" class="mt-4 md:mt-0 inline-flex items-center space-x-2 text-sm font-bold text-amber-600 hover:text-amber-700">
            <span>Buka Peta Layar Penuh</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <!-- Interactive Map Box -->
    <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-slate-200">
        <div id="home-map" class="w-full h-96 z-10"></div>
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex flex-wrap items-center gap-4">
                <span class="font-bold text-slate-700">Keterangan Marker:</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-red-500 mr-1.5"></span> Sangat Prioritas</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-orange-500 mr-1.5"></span> Prioritas Tinggi</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-yellow-500 mr-1.5"></span> Sedang</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-emerald-500 mr-1.5"></span> Selesai</span>
            </div>
            <a href="{{ route('public.peta') }}" class="font-bold text-navy-900 hover:underline">Jelajahi Peta Lengkap &rarr;</a>
        </div>
    </div>
</section>

<!-- 56. PANTAU PROGRES PERBAIKAN & LAPORAN TERBARU -->
<section class="py-20 bg-slate-100 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-amber-600 font-bold text-xs uppercase tracking-widest">Feed Publik</span>
                <h2 class="text-3xl font-extrabold text-navy-900 mt-1">Laporan & Perkembangan Terbaru</h2>
                <p class="text-slate-600 text-sm mt-1">Laporan masyarakat dan dokumentasi pengerjaan aktif oleh tim dinas terkait.</p>
            </div>
            <a href="{{ route('public.reports.index') }}" class="mt-4 md:mt-0 inline-flex items-center space-x-2 text-sm font-bold text-amber-600 hover:text-amber-700">
                <span>Lihat Semua Laporan Publik</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($recentReports as $report)
                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl transition duration-200 flex flex-col">
                    
                    <!-- Photo Header with Lightbox -->
                    <div class="relative h-52 bg-slate-900 overflow-hidden group">
                        @php
                            $cover = $report->photos->first()?->file_url ?? asset('images/road-placeholder.svg');
                        @endphp
                        <img src="{{ $cover }}" alt="{{ $report->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="px-3 py-1 rounded-full text-xs font-bold shadow-md {{ $report->status_badge_class }} border">
                                {{ $report->status }}
                            </span>
                        </div>

                        <!-- Damage Type Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-navy-900/80 text-amber-300 backdrop-blur-sm border border-slate-700">
                                {{ $report->damage_type_label }}
                            </span>
                        </div>

                        <!-- Progress Overlay if active -->
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-navy-950/90 to-transparent p-3 pt-6 flex justify-between items-end">
                            <span class="text-[11px] font-mono font-bold text-amber-400">{{ $report->ticket_number }}</span>
                            <span class="text-xs font-bold text-white">{{ $report->current_progress }}% Progres</span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="flex items-center text-xs text-slate-500 space-x-2">
                                <i class="fa-solid fa-location-dot text-amber-500"></i>
                                <span class="font-medium truncate">{{ $report->road_name }}, {{ $report->kecamatan }}</span>
                            </div>
                            <h3 class="text-base font-bold text-navy-900 leading-snug line-clamp-2 hover:text-amber-600">
                                <a href="{{ route('public.reports.show', $report->id) }}">{{ $report->title }}</a>
                            </h3>
                            <p class="text-xs text-slate-600 line-clamp-2">{{ $report->description }}</p>
                        </div>

                        <!-- Timeline Mini Step Indicator -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <div class="text-xs text-slate-500">
                                <span class="font-semibold text-slate-700">{{ $report->photos->count() }}</span> Foto Awal
                                @if($report->progressUpdates->count() > 0)
                                    • <span class="font-semibold text-emerald-600">{{ $report->progressUpdates->count() }} Minggu</span> Update
                                @endif
                            </div>
                            <a href="{{ route('public.reports.show', $report->id) }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-navy-900 hover:text-amber-600 transition">
                                <span>Lihat Detail</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>

                    </div>

                </div>
            @empty
                <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-slate-200">
                    <i class="fa-solid fa-circle-info text-slate-400 text-4xl mb-3"></i>
                    <p class="text-slate-600 font-medium">Belum ada data laporan publik.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>

<!-- 56. AJAKAN MELAPOR (CTA SECTION) -->
<section class="py-20 bg-gradient-to-r from-navy-950 via-navy-900 to-slate-900 text-white relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 relative z-10">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-400 text-navy-950 flex items-center justify-center mx-auto text-3xl font-extrabold shadow-xl shadow-amber-500/20">
            <i class="fa-solid fa-bullhorn"></i>
        </div>
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight">
            Temukan Jalan Rusak di Sekitar Anda?
        </h2>
        <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
            Laporkan sekarang dengan foto dan lokasi GPS. Pantau langsung tahapan perbaikannya dari minggu ke minggu sampai tuntas.
        </p>
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
            <a href="{{ Auth::check() ? route('masyarakat.reports.create') : route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-navy-950 font-extrabold text-base shadow-xl shadow-amber-500/30 hover:scale-105 active:scale-95 transition">
                <i class="fa-solid fa-plus-circle mr-2"></i> Buat Laporan Pengaduan
            </a>
            <a href="{{ route('public.reports.index') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-base border border-slate-700 transition">
                <i class="fa-solid fa-eye mr-2 text-amber-400"></i> Pantau Laporan Publik
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    const liveFeedReports = @json($liveFeedData ?? []);
    let currentRoadIdx = 0;
    let currentStageIdx = 0;

    function createSvgDataUrl(title, stageIndex) {
        let colors = ["#1e293b", "#0f172a", "#1e1e38", "#111827", "#064e3b"];
        let bg = colors[stageIndex % colors.length] || "#1e293b";
        let roadColor = stageIndex >= 3 ? "#1e293b" : "#334155";
        let stripeColor = stageIndex >= 3 ? "#10b981" : "#fbbf24";
        
        let svg = `<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400">
            <rect width="600" height="400" fill="${bg}"/>
            <path d="M120 400 L240 140 L360 140 L480 400 Z" fill="${roadColor}"/>
            <line x1="300" y1="140" x2="300" y2="400" stroke="${stripeColor}" stroke-width="4" stroke-dasharray="14 10"/>
            <rect x="20" y="20" width="560" height="50" rx="8" fill="#000000" opacity="0.6"/>
            <text x="300" y="52" fill="#f8fafc" font-size="16" font-family="sans-serif" font-weight="bold" text-anchor="middle">${title}</text>
        </svg>`;
        return "data:image/svg+xml;utf8," + encodeURIComponent(svg);
    }

    function renderLiveFeedRoad(roadIdx) {
        if (!liveFeedReports || liveFeedReports.length === 0) return;
        currentRoadIdx = roadIdx;
        const report = liveFeedReports[roadIdx];
        if (!report) return;

        // 1. Update Dropdown Trigger Button Meta
        const selectedBadge = document.getElementById('selected-road-status-badge');
        if (selectedBadge) {
            selectedBadge.textContent = report.status.toUpperCase();
            selectedBadge.className = `px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase shrink-0 border ${report.status_class}`;
        }
        const selectedText = document.getElementById('selected-road-text');
        if (selectedText) selectedText.textContent = report.road_name;

        const selectedTicket = document.getElementById('selected-road-ticket');
        if (selectedTicket) selectedTicket.textContent = '#' + report.ticket_number;

        // Highlight Active item in Dropdown List
        const dropdownItems = document.querySelectorAll('.live-dropdown-item');
        dropdownItems.forEach((item, i) => {
            if (i === roadIdx) {
                item.className = "live-dropdown-item w-full text-left p-2.5 rounded-xl transition flex items-center justify-between border bg-amber-500/10 border-amber-500/40 group";
            } else {
                item.className = "live-dropdown-item w-full text-left p-2.5 rounded-xl transition flex items-center justify-between border bg-slate-900/60 border-slate-800 hover:bg-slate-800 hover:border-slate-700 group";
            }
        });

        // 2. Update Road Header Meta
        const ticketEl = document.getElementById('live-feed-ticket');
        if (ticketEl) ticketEl.textContent = '#' + report.ticket_number;

        const nameEl = document.getElementById('live-feed-road-name');
        if (nameEl) nameEl.textContent = report.road_name + ' (' + report.kecamatan + ')';

        const opdEl = document.getElementById('live-feed-opd-name');
        if (opdEl) opdEl.textContent = report.opd_name;

        const linkEl = document.getElementById('live-feed-detail-link');
        if (linkEl) linkEl.href = report.detail_url;

        // 3. Render Dynamic Timeline Buttons (Awal, Survei, M-1, M-2, M-3, M-4, M-5, ..., Selesai)
        const timelineContainer = document.getElementById('live-feed-timeline-buttons');
        if (timelineContainer && report.stages) {
            let html = '';
            report.stages.forEach((stage, idx) => {
                html += `<button type="button" onclick="switchLiveFeedStage(${idx})" class="live-stage-btn flex-1 min-w-[45px] py-1.5 px-2 rounded transition bg-slate-800 text-slate-300 hover:bg-slate-700 text-[10px] font-bold shadow-xs">${stage.short_name}</button>`;
            });
            timelineContainer.innerHTML = html;
        }

        // 4. Switch to default active stage (most recent update or final)
        const activeIdx = Math.max(0, report.stages.length - 1);
        switchLiveFeedStage(activeIdx);
    }

    function switchLiveFeedStage(stageIdx) {
        if (!liveFeedReports || !liveFeedReports[currentRoadIdx]) return;
        const report = liveFeedReports[currentRoadIdx];
        const stage = report.stages[stageIdx];
        if (!stage) return;
        currentStageIdx = stageIdx;

        // Update Image
        const img = document.getElementById('live-feed-img');
        if (img) {
            if (stage.photo) {
                img.src = stage.photo;
            } else {
                img.src = createSvgDataUrl(stage.label + ' - ' + report.road_name, stageIdx);
            }
        }

        // Update Stage Badge on Photo (Clean without % number)
        const stageLabel = document.getElementById('live-feed-stage-label');
        if (stageLabel) stageLabel.textContent = stage.label;

        // Update Progress Description (Displays OPD's custom notes, no %!)
        const progressText = document.getElementById('live-feed-progress-text');
        if (progressText) progressText.textContent = stage.progress;

        // Smooth visual progress bar based on stage progression
        const progressBar = document.getElementById('live-feed-progress-bar');
        if (progressBar) {
            const ratio = report.stages.length > 1 ? Math.round(((stageIdx + 1) / report.stages.length) * 100) : 100;
            progressBar.style.width = ratio + '%';
        }

        // Update Status Badge
        const statusBadge = document.getElementById('live-feed-status-badge');
        if (statusBadge) {
            statusBadge.textContent = report.status.toUpperCase();
            statusBadge.className = `px-2.5 py-1 rounded-full text-[10px] font-bold border ${report.status_class} whitespace-nowrap`;
        }

        // Highlight Active Stage Button
        const buttons = document.querySelectorAll('.live-stage-btn');
        buttons.forEach((btn, i) => {
            if (i === stageIdx) {
                btn.className = "live-stage-btn flex-1 min-w-[45px] py-1.5 px-2 rounded transition bg-emerald-600 text-white shadow-sm shadow-emerald-500/50 font-bold";
            } else {
                btn.className = "live-stage-btn flex-1 min-w-[45px] py-1.5 px-2 rounded transition bg-slate-800 text-slate-300 hover:bg-slate-700 font-bold";
            }
        });
    }

    // Custom Dropdown Handlers
    function toggleLiveFeedDropdown() {
        const menu = document.getElementById('live-dropdown-menu');
        const arrow = document.getElementById('live-dropdown-arrow');
        if (!menu) return;
        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (arrow) arrow.classList.add('rotate-180');
            const search = document.getElementById('live-dropdown-search');
            if (search) {
                search.value = '';
                filterLiveFeedDropdown('');
                setTimeout(() => search.focus(), 50);
            }
        } else {
            closeLiveFeedDropdown();
        }
    }

    function closeLiveFeedDropdown() {
        const menu = document.getElementById('live-dropdown-menu');
        const arrow = document.getElementById('live-dropdown-arrow');
        if (menu) menu.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');
    }

    function filterLiveFeedDropdown(query) {
        const q = (query || '').toLowerCase().trim();
        const items = document.querySelectorAll('.live-dropdown-item');
        items.forEach(item => {
            const text = item.getAttribute('data-search') || '';
            if (!q || text.includes(q)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function selectLiveFeedRoad(idx) {
        renderLiveFeedRoad(idx);
        closeLiveFeedDropdown();
    }

    // Close custom dropdown on click outside or Escape key
    document.addEventListener('click', function(e) {
        const container = document.getElementById('custom-live-dropdown-container');
        if (container && !container.contains(e.target)) {
            closeLiveFeedDropdown();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLiveFeedDropdown();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (liveFeedReports && liveFeedReports.length > 0) {
            renderLiveFeedRoad(0);
        }

        var map = L.map('home-map', {
            zoomControl: true,
            scrollWheelZoom: false
        }).setView([-6.9200, 107.6250], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        fetch("{{ route('api.geo-reports') }}")
            .then(res => res.json())
            .then(data => {
                var bounds = [];
                if (data.reports && data.reports.length > 0) {
                    data.reports.forEach(function(r) {
                        var color = r.marker_color || '#3b82f6';
                        var circleMarker = L.circleMarker([r.latitude, r.longitude], {
                            radius: 8,
                            fillColor: color,
                            color: '#ffffff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.9
                        }).addTo(map);

                        circleMarker.bindPopup(`
                            <div class="p-2 space-y-1.5 font-sans" style="min-width: 180px;">
                                <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">${r.ticket_number}</span>
                                <h4 class="text-xs font-bold text-slate-900">${r.road_name}</h4>
                                <p class="text-[11px] text-slate-600">Status: <strong class="text-slate-900">${r.status}</strong></p>
                                <p class="text-[11px] text-slate-600">Progres: <strong class="text-emerald-600">${r.progress}%</strong></p>
                                <a href="${r.detail_url}" class="inline-block mt-2 px-3 py-1 bg-navy-900 text-white text-[11px] font-bold rounded hover:bg-amber-600">Lihat Detail &rarr;</a>
                            </div>
                        `);

                        bounds.push([r.latitude, r.longitude]);
                    });

                    if (bounds.length > 0) {
                        map.fitBounds(bounds, { padding: [30, 30] });
                    }
                }
            })
            .catch(err => console.error('Error fetching map markers:', err));
            
        setTimeout(function() {
            map.invalidateSize();
        }, 400);
    });
</script>
@endpush
