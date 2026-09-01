@extends('layouts.admin')

@section('title', 'Admin Executive Dashboard - JALAN KU')
@section('header_title', 'Admin Executive Dashboard')

@section('content')
<div class="space-y-8">
    
    <!-- 25. DASHBOARD ADMIN STATISTIK METRICS (8 Metric Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3.5">
        
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total</span>
            <p class="text-2xl font-extrabold text-navy-900 mt-1">{{ number_format($stats['total_laporan']) }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Laporan Baru</span>
            <p class="text-2xl font-extrabold text-sky-600 mt-1">{{ number_format($stats['laporan_baru']) }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Diverifikasi</span>
            <p class="text-2xl font-extrabold text-blue-600 mt-1">{{ number_format($stats['diverifikasi']) }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ditugaskan</span>
            <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ number_format($stats['ditugaskan']) }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Survei</span>
            <p class="text-2xl font-extrabold text-yellow-600 mt-1">{{ number_format($stats['survei']) }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Diperbaiki</span>
            <p class="text-2xl font-extrabold text-orange-600 mt-1">{{ number_format($stats['sedang_diperbaiki']) }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Selesai</span>
            <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['selesai']) }}</p>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-rose-200 shadow-sm text-center bg-rose-50/50">
            <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wider block">Terlambat</span>
            <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ number_format($stats['terlambat']) }}</p>
        </div>

    </div>

    <!-- 25. PERLU TINDAKAN (Action Required Cards) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Action 1: Belum Diverifikasi -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-navy-900 flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500 mr-2"></span>
                    Belum Diverifikasi ({{ $actionRequired['belum_diverifikasi']->count() }})
                </h3>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-sky-50 text-sky-700">Perlu Review</span>
            </div>

            <div class="space-y-3">
                @forelse($actionRequired['belum_diverifikasi'] as $rep)
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs hover:bg-slate-100 transition">
                        <div>
                            <span class="font-mono font-bold text-amber-600">{{ $rep->ticket_number }}</span>
                            <h4 class="font-bold text-navy-900 truncate max-w-[170px]">{{ $rep->road_name }}</h4>
                        </div>
                        <a href="{{ route('admin.reports.show', $rep->id) }}" class="px-3 py-1 bg-navy-900 text-white font-bold text-[10px] rounded-lg">
                            Periksa &rarr;
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Semua laporan telah diverifikasi.</p>
                @endforelse
            </div>
        </div>

        <!-- Action 2: Belum Ditugaskan -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-navy-900 flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2"></span>
                    Belum Ditugaskan ke OPD
                </h3>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-700">Tugaskan</span>
            </div>

            <div class="space-y-3">
                @forelse($actionRequired['belum_ditugaskan'] as $rep)
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs hover:bg-slate-100 transition">
                        <div>
                            <span class="font-mono font-bold text-amber-600">{{ $rep->ticket_number }}</span>
                            <h4 class="font-bold text-navy-900 truncate max-w-[170px]">{{ $rep->road_name }}</h4>
                        </div>
                        <a href="{{ route('admin.reports.show', $rep->id) }}" class="px-3 py-1 bg-amber-500 text-navy-950 font-bold text-[10px] rounded-lg">
                            Tugaskan &rarr;
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Tidak ada laporan antrean penugasan.</p>
                @endforelse
            </div>
        </div>

        <!-- Action 3: Terlambat / Tidak ada update -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-navy-900 flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-2"></span>
                    Terlambat / Perlu Follow Up
                </h3>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-700">Peringatan</span>
            </div>

            <div class="space-y-3">
                @forelse($actionRequired['terlambat'] as $rep)
                    <div class="p-3 bg-rose-50/60 rounded-2xl border border-rose-100 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-mono font-bold text-rose-600">{{ $rep->ticket_number }}</span>
                            <h4 class="font-bold text-navy-900 truncate max-w-[170px]">{{ $rep->road_name }}</h4>
                            <span class="text-[10px] text-slate-500">{{ $rep->opd?->name ?? 'OPD Belum Ada' }}</span>
                        </div>
                        <a href="{{ route('admin.reports.show', $rep->id) }}" class="px-3 py-1 bg-rose-600 text-white font-bold text-[10px] rounded-lg">
                            Cek &rarr;
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Semua penanganan berjalan tepat waktu.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- 25. TOP 10 JALAN PRIORITAS (TOPSIS Ranking Table) & Recalculate Button -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-bold text-amber-600 uppercase tracking-widest">Decision Support System</span>
                <h2 class="text-xl font-extrabold text-navy-900 mt-0.5 flex items-center">
                    <i class="fa-solid fa-ranking-star text-amber-500 mr-2.5"></i>
                    TOP 10 Rekomendasi Prioritas Penanganan Jalan (TOPSIS)
                </h2>
            </div>
            
            <form method="POST" action="{{ route('admin.recalculate-topsis') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-calculator text-amber-400"></i>
                    <span>Hitung Ulang TOPSIS</span>
                </button>
            </form>
        </div>

        <!-- Desktop Table View -->
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 text-center">Rank</th>
                        <th class="py-3 px-3">Nama Jalan / Tiket</th>
                        <th class="py-3 px-3">Tingkat Kerusakan</th>
                        <th class="py-3 px-3 text-center">Skor TOPSIS</th>
                        <th class="py-3 px-3">Kategori Prioritas</th>
                        <th class="py-3 px-3">Alasan Rekomendasi</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($topPriorities as $index => $rep)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-3 text-center font-extrabold text-sm {{ $index < 3 ? 'text-amber-600' : 'text-slate-600' }}">
                                #{{ $index + 1 }}
                            </td>
                            <td class="py-4 px-3">
                                <span class="font-bold text-navy-900 block">{{ $rep->road_name }}</span>
                                <span class="font-mono text-[10px] text-slate-400">#{{ $rep->ticket_number }} • {{ $rep->kecamatan }}</span>
                            </td>
                            <td class="py-4 px-3">
                                @php
                                    $det = $rep->damageDetections->first();
                                    $classes = $det?->detected_classes ?? [];
                                    $landslides = $classes['landslide'] ?? 0;
                                    $potholes = $classes['pothole'] ?? 0;
                                    $cracks = $classes['crack'] ?? 0;

                                    if ($landslides > 0) {
                                        $label = "LANDSLIDE ({$landslides}x)";
                                        $badgeStyle = "bg-rose-50 text-rose-700 border-rose-200";
                                        $icon = "fa-triangle-exclamation";
                                    } elseif ($potholes > 0) {
                                        $label = "POTHOLE ({$potholes} Lubang)";
                                        $badgeStyle = "bg-amber-50 text-amber-700 border-amber-200";
                                        $icon = "fa-circle-dot";
                                    } elseif ($cracks > 0) {
                                        $label = "CRACK ({$cracks} Retak)";
                                        $badgeStyle = "bg-sky-50 text-sky-700 border-sky-200";
                                        $icon = "fa-bolt";
                                    } elseif ($det) {
                                        $label = "NORMAL / MINOR";
                                        $badgeStyle = "bg-emerald-50 text-emerald-700 border-emerald-200";
                                        $icon = "fa-circle-check";
                                    } else {
                                        $label = strtoupper($rep->damage_type) . " (" . strtoupper($rep->disturbance_level) . ")";
                                        $badgeStyle = "bg-slate-100 text-slate-700 border-slate-200";
                                        $icon = "fa-road";
                                    }
                                @endphp
                                <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg {{ $badgeStyle }} font-bold text-[10px] border whitespace-nowrap">
                                    <i class="fa-solid {{ $icon }} text-[9px]"></i>
                                    <span>{{ $label }}</span>
                                </span>
                            </td>
                            <td class="py-4 px-3 text-center font-mono font-extrabold text-sm text-navy-900">
                                {{ number_format($rep->priorityResult?->score ?? 0, 4) }}
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $rep->priorityResult?->badge_class }} border whitespace-nowrap">
                                    {{ $rep->priorityResult?->priority_level ?? 'Normal' }}
                                </span>
                            </td>
                            <td class="py-4 px-3 max-w-xs text-[11px] text-slate-600 leading-snug">
                                {{ $rep->priorityResult?->reasoning ?? 'Berdasarkan akumulasi bobot 8 kriteria SPK.' }}
                            </td>
                            <td class="py-4 px-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.reports.show', $rep->id) }}" class="px-3.5 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-sm transition inline-flex items-center space-x-1.5 whitespace-nowrap">
                                    <span>Tindak Lanjuti</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data prioritas yang dihitung.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3">
            @forelse($topPriorities as $index => $rep)
                <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 space-y-3 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-8 h-8 rounded-xl flex items-center justify-center font-extrabold text-sm {{ $index < 3 ? 'bg-amber-500 text-navy-950' : 'bg-slate-200 text-slate-600' }}">
                                #{{ $index + 1 }}
                            </span>
                            <div>
                                <h4 class="font-bold text-navy-900 text-sm leading-snug">{{ $rep->road_name }}</h4>
                                <span class="font-mono text-[10px] text-slate-400">#{{ $rep->ticket_number }} • {{ $rep->kecamatan }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold uppercase text-[10px]">
                            {{ $rep->damage_type }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $rep->priorityResult?->badge_class }} border">
                            {{ $rep->priorityResult?->priority_level ?? 'Normal' }}
                        </span>
                        <span class="font-mono font-extrabold text-xs text-navy-900 ml-auto">
                            Skor: {{ number_format($rep->priorityResult?->score ?? 0, 4) }}
                        </span>
                    </div>
                    <a href="{{ route('admin.reports.show', $rep->id) }}" class="block w-full text-center py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition">
                        Tindak Lanjuti &rarr;
                    </a>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-6">Belum ada data prioritas yang dihitung.</p>
            @endforelse
        </div>
    </div>

    <!-- Charts & Map Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Monthly Chart -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold text-navy-900 flex items-center">
                <i class="fa-solid fa-chart-area text-amber-500 mr-2"></i> Tren Laporan Masuk Per Bulan
            </h3>
            <div id="admin-chart-monthly" class="h-64"></div>
        </div>

        <!-- Damage Types Chart -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold text-navy-900 flex items-center">
                <i class="fa-solid fa-chart-pie text-sky-500 mr-2"></i> Proporsi Kategori Kerusakan Jalan
            </h3>
            <div id="admin-chart-damage" class="h-64"></div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly trend chart
        var mData = @json($monthlyData);
        var mCategories = mData.map(d => d.month);
        var mSeries = mData.map(d => d.count);

        var monthlyOptions = {
            series: [{ name: 'Laporan Baru', data: mSeries }],
            chart: { type: 'area', height: 250, toolbar: { show: false } },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#f59e0b'],
            xaxis: { categories: mCategories },
            dataLabels: { enabled: false }
        };
        new ApexCharts(document.querySelector("#admin-chart-monthly"), monthlyOptions).render();

        // Damage type chart
        var dData = @json($damageTypeData);
        var dCategories = Object.keys(dData).map(k => k.charAt(0).toUpperCase() + k.slice(1));
        var dSeries = Object.values(dData);

        var damageOptions = {
            series: [{ name: 'Total', data: dSeries }],
            chart: { type: 'bar', height: 250, toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 6, horizontal: true } },
            colors: ['#0ea5e9'],
            xaxis: { categories: dCategories },
            dataLabels: { enabled: true }
        };
        new ApexCharts(document.querySelector("#admin-chart-damage"), damageOptions).render();
    });
</script>
@endpush
