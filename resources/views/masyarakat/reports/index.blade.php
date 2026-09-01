@extends('layouts.app')

@section('title', 'Laporan Pengaduan Saya - JALAN KU')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900">Daftar Pengaduan Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500">Semua laporan kerusakan jalan yang telah Anda ajukan.</p>
        </div>
        <a href="{{ route('masyarakat.reports.create') }}" class="inline-flex items-center space-x-2 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-navy-950 font-bold text-xs rounded-xl shadow-lg shadow-amber-500/25 transition">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Buat Laporan Baru</span>
        </a>
    </div>

    <!-- Reports Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8 space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Nomor Tiket</th>
                        <th class="py-3.5 px-4">Ruas Jalan / Lokasi</th>
                        <th class="py-3.5 px-4">Tanggal Lapor</th>
                        <th class="py-3.5 px-4">Status Terkini</th>
                        <th class="py-3.5 px-4">Progres Pengerjaan</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4 font-mono font-bold text-amber-600">
                                {{ $report->ticket_number }}
                            </td>
                            <td class="py-4 px-4 font-bold text-navy-900">
                                {{ $report->road_name }}
                                <p class="text-[11px] font-normal text-slate-500">{{ $report->kecamatan }} • {{ ucfirst($report->damage_type) }}</p>
                            </td>
                            <td class="py-4 px-4 text-slate-600">
                                {{ $report->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $report->status_badge_class }} border">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $report->current_progress }}%"></div>
                                    </div>
                                    <span class="font-bold text-[11px]">{{ $report->current_progress }}%</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <a href="{{ route('masyarakat.reports.show', $report->id) }}" class="px-3.5 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition">
                                    Pantau Timeline &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Belum ada riwayat pengaduan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    </div>

</div>
@endsection
