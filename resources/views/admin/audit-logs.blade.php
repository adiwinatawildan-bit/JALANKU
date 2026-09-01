@extends('layouts.admin')

@section('title', 'Audit Log Aktivitas - JALAN KU')
@section('header_title', 'Audit Log Sistem & Transparansi Aktivitas')

@section('content')
<div class="space-y-6">
    
    <!-- Search / Filter -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.audit-logs') }}" class="flex items-center space-x-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari aktivitas, tipe target, atau deskripsi..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>
            <button type="submit" class="px-5 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
                Cari
            </button>
            <a href="{{ route('admin.audit-logs') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                <i class="fa-solid fa-rotate-right"></i>
            </a>
        </form>
    </div>

    <!-- 46. AUDIT LOG TABLE & CARDS -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-6">
        <!-- Desktop Table View -->
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-3">Waktu Kejadian</th>
                        <th class="py-3.5 px-3">Pengguna / Role</th>
                        <th class="py-3.5 px-3">Aktivitas</th>
                        <th class="py-3.5 px-3">Target Entitas</th>
                        <th class="py-3.5 px-3">Rincian Deskripsi</th>
                        <th class="py-3.5 px-3 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3 text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->translatedFormat('d M Y, H:i:s') }}
                            </td>
                            <td class="py-3.5 px-3">
                                @if($log->user)
                                    <span class="font-bold text-navy-900 block">{{ $log->user->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">{{ $log->user->role?->name }}</span>
                                @else
                                    <span class="text-slate-400 italic">Sistem / Publik</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-bold text-navy-900 bg-slate-100 px-2 py-0.5 rounded-md">
                                    {{ $log->activity }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 font-mono text-[11px]">
                                {{ $log->target_type }} #{{ $log->target_id }}
                            </td>
                            <td class="py-3.5 px-3 max-w-sm text-[11px] text-slate-600 leading-snug">
                                {{ $log->description }}
                            </td>
                            <td class="py-3.5 px-3 text-right font-mono text-[10px] text-slate-400">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">Belum ada riwayat audit log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3">
            @forelse($logs as $log)
                <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 space-y-2">
                    <div class="flex items-start justify-between">
                        <span class="font-bold text-navy-900 bg-slate-200 px-2 py-0.5 rounded-md text-[11px]">
                            {{ $log->activity }}
                        </span>
                        <span class="text-[10px] text-slate-400 shrink-0 ml-2">{{ $log->created_at->translatedFormat('d M, H:i') }}</span>
                    </div>
                    <div class="text-xs text-slate-600 leading-snug">
                        {{ Str::limit($log->description, 120) }}
                    </div>
                    <div class="flex items-center justify-between text-[10px]">
                        <span class="text-slate-500">
                            @if($log->user)
                                <i class="fa-solid fa-user mr-1 text-amber-400"></i>{{ $log->user->name }}
                                <span class="uppercase text-slate-400 font-bold">({{ $log->user->role?->name }})</span>
                            @else
                                <span class="text-slate-400 italic">Sistem</span>
                            @endif
                        </span>
                        <span class="font-mono text-slate-400">{{ $log->target_type }} #{{ $log->target_id }}</span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-8">Belum ada riwayat audit log.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
