@extends('layouts.admin')

@section('title', 'Master Audit Logs - JALAN KU')
@section('header_title', 'Master Audit Trail & Rekam Jejak Sistem')

@section('content')
<div class="space-y-6">
    
    <!-- Filter -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('superadmin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari aktivitas / deskripsi..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div>
                <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                    <option value="">Semua Pengguna</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role->name }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
                    Filter Logs
                </button>
                <a href="{{ route('superadmin.audit-logs.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-3">Waktu</th>
                        <th class="py-3.5 px-3">Pengguna</th>
                        <th class="py-3.5 px-3">Aktivitas</th>
                        <th class="py-3.5 px-3">Target</th>
                        <th class="py-3.5 px-3">Rincian Deskripsi</th>
                        <th class="py-3.5 px-3 text-right">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3 text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3.5 px-3">
                                @if($log->user)
                                    <span class="font-bold text-navy-900 block">{{ $log->user->name }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold">{{ $log->user->role?->name }}</span>
                                @else
                                    <span class="text-slate-400 italic">Sistem</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-bold text-navy-900 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $log->activity }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 font-mono text-[11px]">
                                {{ $log->target_type }} #{{ $log->target_id }}
                            </td>
                            <td class="py-3.5 px-3 text-slate-600 leading-snug max-w-sm">
                                {{ $log->description }}
                            </td>
                            <td class="py-3.5 px-3 text-right font-mono text-[10px] text-slate-400">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
