@extends('layouts.app')

@section('title', 'Statistik & Analitik Penanganan Jalan - JALAN KU')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- Title Section -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
        <span class="text-amber-600 font-bold text-xs uppercase tracking-widest">Keterbukaan Data Publik</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">Statistik & Analitik Kinerja Penanganan Jalan</h1>
        <p class="text-slate-600 text-sm sm:text-base">Data real-time mengenai pengaduan masyarakat, waktu respon, efektivitas penanganan OPD, dan sebaran jenis kerusakan jalan.</p>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Aduan Masuk</span>
            <p class="text-3xl sm:text-4xl font-extrabold text-navy-900 mt-2">{{ number_format($totalReports) }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tuntas Diperbaiki</span>
            <p class="text-3xl sm:text-4xl font-extrabold text-emerald-600 mt-2">{{ number_format($statusCounts['SELESAI'] ?? 0) }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengerjaan Aktif</span>
            <p class="text-3xl sm:text-4xl font-extrabold text-amber-500 mt-2">{{ number_format(($statusCounts['SEDANG DIPERBAIKI'] ?? 0) + ($statusCounts['SURVEI'] ?? 0)) }}</p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Persentase Selesai</span>
            @php
                $resolvedPct = $totalReports > 0 ? round((($statusCounts['SELESAI'] ?? 0) / $totalReports) * 100, 1) : 0;
            @endphp
            <p class="text-3xl sm:text-4xl font-extrabold text-sky-600 mt-2">{{ $resolvedPct }}%</p>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Chart 1: Status Laporan Breakdown -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-navy-900 flex items-center">
                <i class="fa-solid fa-chart-pie text-amber-500 mr-2.5"></i> Distribusi Status Laporan
            </h3>
            <div id="chart-status" class="h-72"></div>
        </div>

        <!-- Chart 2: Jenis Kerusakan Jalan -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-navy-900 flex items-center">
                <i class="fa-solid fa-chart-simple text-sky-500 mr-2.5"></i> Jenis Kerusakan Terbanyak
            </h3>
            <div id="chart-damage" class="h-72"></div>
        </div>

    </div>

    <!-- OPD Performance Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8 space-y-6">
        <h3 class="text-lg font-bold text-navy-900 flex items-center">
            <i class="fa-solid fa-building-user text-emerald-500 mr-2.5"></i> Rekapitulasi Kinerja OPD Pelaksana
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Nama OPD / Dinas</th>
                        <th class="py-3.5 px-4 text-center">Total Tugas</th>
                        <th class="py-3.5 px-4 text-center">Sedang Dikerjakan</th>
                        <th class="py-3.5 px-4 text-center">Selesai 100%</th>
                        <th class="py-3.5 px-4 text-center">Tingkat Penyelesaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($opdPerformance as $opd)
                        @php
                            $rate = $opd->total_tasks > 0 ? round(($opd->finished_tasks / $opd->total_tasks) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4 font-bold text-navy-900">{{ $opd->name }}</td>
                            <td class="py-4 px-4 text-center font-bold">{{ $opd->total_tasks }}</td>
                            <td class="py-4 px-4 text-center text-amber-600 font-bold">{{ $opd->active_tasks }}</td>
                            <td class="py-4 px-4 text-center text-emerald-600 font-bold">{{ $opd->finished_tasks }}</td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="font-bold text-slate-900">{{ $rate }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Belum ada data OPD yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status Pie Chart
        var statusData = @json($statusCounts);
        var statusLabels = Object.keys(statusData);
        var statusSeries = Object.values(statusData);

        var statusOptions = {
            series: statusSeries.length ? statusSeries : [1],
            labels: statusLabels.length ? statusLabels : ['Belum Ada Data'],
            chart: { type: 'donut', height: 280 },
            colors: ['#3b82f6', '#0ea5e9', '#f59e0b', '#eab308', '#f97316', '#10b981', '#ef4444', '#a855f7'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true }
        };
        new ApexCharts(document.querySelector("#chart-status"), statusOptions).render();

        // Damage Bar Chart
        var damageData = @json($damageTypeCounts);
        var damageLabels = Object.keys(damageData).map(k => k.charAt(0).toUpperCase() + k.slice(1));
        var damageSeries = Object.values(damageData);

        var damageOptions = {
            series: [{ name: 'Jumlah Laporan', data: damageSeries }],
            chart: { type: 'bar', height: 280, toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
            xaxis: { categories: damageLabels },
            colors: ['#f59e0b'],
            dataLabels: { enabled: false }
        };
        new ApexCharts(document.querySelector("#chart-damage"), damageOptions).render();
    });
</script>
@endpush
