@extends('layouts.admin')

@section('title', 'Kelola Pengaduan Kerusakan Jalan - JALAN KU')
@section('header_title', 'Kelola Seluruh Pengaduan')

@section('content')
<div class="space-y-6">
    
    <!-- Filter & Search Bar -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tiket / jalan..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="DIAJUKAN" {{ request('status') === 'DIAJUKAN' ? 'selected' : '' }}>Diajukan (Baru)</option>
                    <option value="DIVERIFIKASI" {{ request('status') === 'DIVERIFIKASI' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="DITUGASKAN" {{ request('status') === 'DITUGASKAN' ? 'selected' : '' }}>Ditugaskan</option>
                    <option value="SURVEI" {{ request('status') === 'SURVEI' ? 'selected' : '' }}>Survei</option>
                    <option value="SEDANG DIPERBAIKI" {{ request('status') === 'SEDANG DIPERBAIKI' ? 'selected' : '' }}>Sedang Diperbaiki</option>
                    <option value="SELESAI" {{ request('status') === 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                    <option value="DITOLAK" {{ request('status') === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                    <option value="DUPLIKAT" {{ request('status') === 'DUPLIKAT' ? 'selected' : '' }}>Duplikat</option>
                </select>
            </div>

            <div>
                <select name="opd_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                    <option value="">Semua OPD</option>
                    @foreach($opds as $opd)
                        <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>{{ $opd->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="priority" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                    <option value="">Semua Prioritas</option>
                    <option value="Sangat Prioritas" {{ request('priority') === 'Sangat Prioritas' ? 'selected' : '' }}>Sangat Prioritas</option>
                    <option value="Prioritas Tinggi" {{ request('priority') === 'Prioritas Tinggi' ? 'selected' : '' }}>Prioritas Tinggi</option>
                    <option value="Sedang" {{ request('priority') === 'Sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="Rendah" {{ request('priority') === 'Rendah' ? 'selected' : '' }}>Rendah</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
                    Filter
                </button>
                <a href="{{ route('admin.reports.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Reports Table & Cards -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-6">
        <!-- Desktop Table View -->
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-3">Tiket & Waktu</th>
                        <th class="py-3.5 px-3">Ruas Jalan & Wilayah</th>
                        <th class="py-3.5 px-3">Status</th>
                        <th class="py-3.5 px-3">Prioritas TOPSIS</th>
                        <th class="py-3.5 px-3">OPD Ditugaskan</th>
                        <th class="py-3.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-3">
                                <span class="font-mono font-bold text-amber-600 block">{{ $report->ticket_number }}</span>
                                <span class="text-[10px] text-slate-400">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </td>
                            <td class="py-4 px-3">
                                <span class="font-bold text-navy-900 block">{{ $report->road_name }}</span>
                                <span class="text-[11px] text-slate-500">{{ $report->kecamatan }} • {{ ucfirst($report->damage_type) }}</span>
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $report->status_badge_class }} border">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                @if($report->priorityResult)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $report->priorityResult->badge_class }} border">
                                        {{ $report->priorityResult->priority_level }} ({{ $report->priorityResult->score }})
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum dihitung</span>
                                @endif
                            </td>
                            <td class="py-4 px-3 text-[11px]">
                                {{ $report->opd?->name ?? 'Belum Ditugaskan' }}
                            </td>
                            <td class="py-4 px-3 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="px-3 py-1.5 bg-navy-900 hover:bg-amber-600 text-white font-bold text-[11px] rounded-lg transition inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Detail</span>
                                </a>
                                <form method="POST" action="{{ route('admin.reports.delete', $report->id) }}" class="inline-block" onsubmit="return confirm('Hapus laporan #{{ $report->ticket_number }} beserta semua foto & datanya secara permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 font-bold text-[11px] rounded-lg border border-rose-200 transition inline-flex items-center space-x-1">
                                        <i class="fa-solid fa-trash-can"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">Tidak ada laporan yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3">
            @forelse($reports as $report)
                <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 space-y-2.5 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="font-mono font-bold text-amber-600 text-xs">{{ $report->ticket_number }}</span>
                            <h4 class="font-bold text-navy-900 text-sm leading-snug mt-0.5">{{ $report->road_name }}</h4>
                            <span class="text-[10px] text-slate-400">{{ $report->kecamatan }} • {{ $report->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $report->status_badge_class }} border">
                            {{ $report->status }}
                        </span>
                        @if($report->priorityResult)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $report->priorityResult->badge_class }} border">
                                {{ $report->priorityResult->priority_level }}
                            </span>
                        @endif
                        <span class="text-[10px] text-slate-500 ml-auto">{{ $report->opd?->name ?? 'Belum OPD' }}</span>
                    </div>
                    <div class="flex items-center space-x-2 pt-1">
                        <a href="{{ route('admin.reports.show', $report->id) }}" class="flex-1 text-center py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition">
                            Detail &rarr;
                        </a>
                        <form method="POST" action="{{ route('admin.reports.delete', $report->id) }}" class="inline-block" onsubmit="return confirm('Hapus laporan #{{ $report->ticket_number }} secara permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 font-bold text-xs rounded-xl border border-rose-200 transition">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-8">Tidak ada laporan yang ditemukan.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>

</div>
@endsection
