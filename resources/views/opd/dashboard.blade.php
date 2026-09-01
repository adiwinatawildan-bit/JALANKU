@extends('layouts.admin')

@section('title', 'Dashboard Petugas OPD - JALAN KU')
@section('header_title', 'Dashboard Operasional OPD')

@section('content')
<div class="space-y-8">
    
    <!-- 26. DASHBOARD OPD METRICS (Ditugaskan, Survei, Diperbaiki, Selesai, Terlambat) -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Ditugaskan</span>
            <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ number_format($stats['ditugaskan']) }}</p>
            <span class="text-[10px] text-slate-400">Menunggu survei</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Survei</span>
            <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ number_format($stats['survei']) }}</p>
            <span class="text-[10px] text-slate-400">Kajian teknis lapangan</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Diperbaiki</span>
            <p class="text-3xl font-extrabold text-orange-600 mt-1">{{ number_format($stats['diperbaiki']) }}</p>
            <span class="text-[10px] text-slate-400">Pengerjaan fisik aktif</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Selesai</span>
            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['selesai']) }}</p>
            <span class="text-[10px] text-slate-400">Tuntas 100%</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-rose-200 shadow-sm text-center bg-rose-50/40 col-span-2 sm:col-span-1">
            <span class="text-xs font-bold text-rose-500 uppercase tracking-wider block">Terlambat</span>
            <p class="text-3xl font-extrabold text-rose-600 mt-1">{{ number_format($stats['terlambat']) }}</p>
            <span class="text-[10px] text-rose-400">> 14 hari tanpa update</span>
        </div>

    </div>

    <!-- 26. DAFTAR PEKERJAAN OPD TABLE -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-xl font-extrabold text-navy-900 flex items-center">
                    <i class="fa-solid fa-helmet-safety text-amber-500 mr-2.5"></i>
                    Daftar Pekerjaan & Tugas Lapangan
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Penugasan perbaikan jalan umum dari Admin Pengawas.</p>
            </div>
            <a href="{{ route('opd.tasks.index') }}" class="text-xs font-bold text-amber-600 hover:underline">
                Lihat Semua Tugas &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Nama Ruas Jalan</th>
                        <th class="py-3.5 px-4">Prioritas</th>
                        <th class="py-3.5 px-4">Progres Pengerjaan</th>
                        <th class="py-3.5 px-4">Status Terkini</th>
                        <th class="py-3.5 px-4 text-right">Tombol Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4">
                                <span class="font-bold text-navy-900 block text-sm">{{ $task->road_name }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">#{{ $task->ticket_number }} • {{ $task->kecamatan }}</span>
                            </td>
                            <td class="py-4 px-4">
                                @if($task->priorityResult)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $task->priorityResult->badge_class }} border">
                                        {{ $task->priorityResult->priority_level }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold text-[10px]">Normal</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $task->current_progress }}%"></div>
                                    </div>
                                    <span class="font-bold text-[11px]">{{ $task->current_progress }}%</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $task->status_badge_class }} border">
                                    {{ $task->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right space-x-2">
                                <a href="{{ route('opd.tasks.show', $task->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-navy-950 font-bold text-xs rounded-xl shadow transition inline-flex items-center space-x-1.5">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Update Progress</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">Belum ada tugas perbaikan yang diberikan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
