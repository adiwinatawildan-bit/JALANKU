@extends('layouts.app')

@section('title', 'Dashboard Masyarakat - JALAN KU')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-navy-950 text-white rounded-3xl p-8 border border-slate-800 shadow-xl gap-6">
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 uppercase tracking-wider">Dashboard Masyarakat</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p class="text-xs sm:text-sm text-slate-300">Pantau status laporan pengaduan kerusakan jalan Anda secara transparan dari minggu ke minggu.</p>
        </div>
        <a href="{{ route('masyarakat.reports.create') }}" class="inline-flex items-center justify-center space-x-2 px-6 py-3.5 bg-amber-500 hover:bg-amber-600 text-navy-950 font-bold text-sm rounded-xl shadow-lg shadow-amber-500/25 transition">
            <i class="fa-solid fa-plus-circle text-base"></i>
            <span>Buat Laporan Baru</span>
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase">Total Laporan Saya</span>
            <p class="text-3xl font-extrabold text-navy-900 mt-2">{{ $stats['total_laporan'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase">Sedang Diproses</span>
            <p class="text-3xl font-extrabold text-amber-500 mt-2">{{ $stats['diproses'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase">Sedang Diperbaiki</span>
            <p class="text-3xl font-extrabold text-orange-500 mt-2">{{ $stats['diperbaiki'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase">Selesai 100%</span>
            <p class="text-3xl font-extrabold text-emerald-500 mt-2">{{ $stats['selesai'] }}</p>
        </div>
    </div>

    <!-- Main Section: My Reports & Notifications -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- My Recent Reports Table -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-bold text-navy-900 flex items-center">
                    <i class="fa-solid fa-list-check text-amber-500 mr-2.5"></i> Riwayat Pengaduan Saya
                </h2>
                <a href="{{ route('masyarakat.reports.index') }}" class="text-xs font-bold text-amber-600 hover:underline">Lihat Semua &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Tiket / Tanggal</th>
                            <th class="py-3 px-3">Ruas Jalan</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Progres</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($myReports as $report)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3">
                                    <span class="font-mono font-bold text-amber-600">{{ $report->ticket_number }}</span>
                                    <p class="text-[10px] text-slate-400">{{ $report->created_at->format('d/m/Y') }}</p>
                                </td>
                                <td class="py-3.5 px-3 font-bold text-navy-900">
                                    {{ $report->road_name }}
                                    <p class="text-[11px] font-normal text-slate-500 truncate max-w-xs">{{ $report->title }}</p>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $report->status_badge_class }} border">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-16 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $report->current_progress }}%"></div>
                                        </div>
                                        <span class="font-bold text-[11px]">{{ $report->current_progress }}%</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <a href="{{ route('masyarakat.reports.show', $report->id) }}" class="px-3 py-1.5 bg-navy-900 hover:bg-amber-600 text-white font-bold text-[11px] rounded-lg transition">
                                        Pantau
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                                    Anda belum memiliki pengaduan jalan. Klik "Buat Laporan Baru" untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notifications Sidebar -->
        <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
            <h2 class="text-lg font-bold text-navy-900 flex items-center border-b border-slate-100 pb-4">
                <i class="fa-solid fa-bell text-amber-500 mr-2.5"></i> Notifikasi Sistem
            </h2>

            <div class="space-y-3">
                @forelse($notifications as $notif)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 hover:bg-slate-100 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-navy-900">{{ $notif->title }}</span>
                            <span class="text-[10px] text-slate-400">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $notif->message }}</p>
                        @if($notif->link_url)
                            <a href="{{ $notif->link_url }}" class="inline-block text-[11px] font-bold text-amber-600 hover:underline pt-1">Buka Laporan &rarr;</a>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada notifikasi.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
