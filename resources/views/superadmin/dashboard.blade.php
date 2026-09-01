@extends('layouts.admin')

@section('title', 'Super Admin Control Center - JALAN KU')
@section('header_title', 'Super Admin Control Center')

@section('content')
<div class="space-y-8">
    
    <!-- 28. DASHBOARD SUPER ADMIN METRICS (6 Metric Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total User</span>
            <p class="text-3xl font-extrabold text-navy-900 mt-1">{{ number_format($stats['total_user']) }}</p>
            <span class="text-[10px] text-slate-400">Seluruh Akun</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Admin</span>
            <p class="text-3xl font-extrabold text-sky-600 mt-1">{{ number_format($stats['admin']) }}</p>
            <span class="text-[10px] text-slate-400">Pengelola</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">OPD</span>
            <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ number_format($stats['opd']) }}</p>
            <span class="text-[10px] text-slate-400">Instansi Terdaftar</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Petugas</span>
            <p class="text-3xl font-extrabold text-orange-600 mt-1">{{ number_format($stats['petugas']) }}</p>
            <span class="text-[10px] text-slate-400">Tim Lapangan</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Masyarakat</span>
            <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($stats['masyarakat']) }}</p>
            <span class="text-[10px] text-slate-400">Warga Pelapor</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">OPD Aktif</span>
            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['opd_aktif']) }}</p>
            <span class="text-[10px] text-slate-400">Status Aktif</span>
        </div>

    </div>

    <!-- Quick Management Shortcuts -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('superadmin.users.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-md hover:border-amber-500 transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-navy-900">User Management</p>
                <span class="text-[10px] text-slate-400">Kelola Akun & Hak Akses</span>
            </div>
        </a>

        <a href="{{ route('superadmin.criteria.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-md hover:border-amber-500 transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-navy-900">Kriteria & Bobot TOPSIS</p>
                <span class="text-[10px] text-slate-400">Atur 8 Bobot SPK</span>
            </div>
        </a>

        <a href="{{ route('superadmin.opds.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-md hover:border-amber-500 transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-navy-900">Master Data OPD</p>
                <span class="text-[10px] text-slate-400">Kelola Dinas Terkait</span>
            </div>
        </a>
    </div>

    <!-- 2 Column Section: Recent Users & Recent Audit Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Recent Users Table -->
        <div class="lg:col-span-6 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-navy-900 flex items-center">
                    <i class="fa-solid fa-user-clock text-amber-500 mr-2"></i> Pengguna Baru Terdaftar
                </h3>
                <a href="{{ route('superadmin.users.index') }}" class="text-xs font-bold text-amber-600 hover:underline">Kelola &rarr;</a>
            </div>

            <div class="space-y-3">
                @foreach($recentUsers as $u)
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-navy-900 block">{{ $u->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $u->email }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700 uppercase">
                            {{ $u->role->name }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Audit Logs -->
        <div class="lg:col-span-6 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-navy-900 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left text-sky-500 mr-2"></i> Aktivitas Sistem Terkini
                </h3>
                <a href="{{ route('superadmin.audit-logs.index') }}" class="text-xs font-bold text-amber-600 hover:underline">Semua Log &rarr;</a>
            </div>

            <div class="space-y-3">
                @foreach($recentAuditLogs as $log)
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 space-y-1 text-xs">
                        <div class="flex justify-between items-center font-bold">
                            <span class="text-navy-900">{{ $log->activity }}</span>
                            <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-snug">{{ $log->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection
