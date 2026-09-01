@extends('layouts.app')

@section('title', 'Laporan Publik Kerusakan Jalan - JALAN KU')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Header Section -->
    <div class="mb-10 text-center max-w-3xl mx-auto space-y-3">
        <span class="text-amber-600 font-bold text-xs uppercase tracking-widest">Portal Transparansi</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight">Laporan Kerusakan Jalan Publik</h1>
        <p class="text-slate-600 text-sm sm:text-base">Seluruh pengaduan yang diajukan masyarakat dapat dipantau secara terbuka oleh publik dari kondisi awal sampai tuntas.</p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm mb-10">
        <form method="GET" action="{{ route('public.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Search Query -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama jalan / tiket / deskripsi..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">Semua Status Laporan</option>
                    <option value="DIAJUKAN" {{ request('status') === 'DIAJUKAN' ? 'selected' : '' }}>Diajukan</option>
                    <option value="DIVERIFIKASI" {{ request('status') === 'DIVERIFIKASI' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="DITUGASKAN" {{ request('status') === 'DITUGASKAN' ? 'selected' : '' }}>Ditugaskan ke OPD</option>
                    <option value="SURVEI" {{ request('status') === 'SURVEI' ? 'selected' : '' }}>Survei Lapangan</option>
                    <option value="SEDANG DIPERBAIKI" {{ request('status') === 'SEDANG DIPERBAIKI' ? 'selected' : '' }}>Sedang Diperbaiki</option>
                    <option value="SELESAI" {{ request('status') === 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <!-- Kecamatan Filter -->
            <div>
                <select name="kecamatan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatanList as $kec)
                        <option value="{{ $kec }}" {{ request('kecamatan') === $kec ? 'selected' : '' }}>{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

                        <!-- Damage Type Filter -->
            <div>
                <select name="damage_type" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">Semua Jenis Kerusakan</option>
                    <option value="pothole" {{ request('damage_type') === 'pothole' ? 'selected' : '' }}>Lubang Jalan (Pothole)</option>
                    <option value="crack" {{ request('damage_type') === 'crack' ? 'selected' : '' }}>Retak Jalan (Crack)</option>
                    <option value="landslide" {{ request('damage_type') === 'landslide' ? 'selected' : '' }}>Longsor / Amblas (Landslide)</option>
                    <option value="lainnya" {{ request('damage_type') === 'lainnya' ? 'selected' : '' }}>Kerusakan Lainnya</option>
                </select>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
                    <i class="fa-solid fa-filter mr-1.5"></i> Terapkan Filter
                </button>
                <a href="{{ route('public.reports.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition" title="Reset Filter">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($reports as $report)
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl transition duration-200 flex flex-col justify-between">
                
                <div>
                    <!-- Photo Cover -->
                    <div class="relative h-56 bg-slate-900 overflow-hidden group">
                        @php
                            $cover = $report->photos->first()?->file_url ?? asset('images/road-placeholder.svg');
                        @endphp
                        <img src="{{ $cover }}" alt="{{ $report->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        
                        <!-- Top Badges -->
                        <div class="absolute top-3 left-3 flex flex-col space-y-1.5">
                            <span class="px-3 py-1 rounded-full text-xs font-bold shadow-md {{ $report->status_badge_class }} border">
                                {{ $report->status }}
                            </span>
                        </div>

                        <div class="absolute top-3 right-3">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-navy-900/80 text-amber-300 backdrop-blur-sm border border-slate-700">
                                {{ $report->damage_type_label }}
                            </span>
                        </div>

                        <!-- Progress Bar Overlay -->
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-navy-950/90 to-transparent p-4 pt-8 flex justify-between items-end text-white">
                            <div>
                                <span class="text-[11px] font-mono font-bold text-amber-400">{{ $report->ticket_number }}</span>
                                <p class="text-xs font-medium text-slate-300">{{ $report->created_at->format('d M Y') }}</p>
                            </div>
                            <span class="text-xs font-extrabold text-emerald-400">{{ $report->current_progress }}% Progres</span>
                        </div>
                    </div>

                    <!-- Body Content -->
                    <div class="p-6 space-y-3">
                        <div class="flex items-center text-xs text-slate-500 space-x-1.5">
                            <i class="fa-solid fa-location-dot text-amber-500"></i>
                            <span class="font-medium truncate">{{ $report->road_name }}, {{ $report->kecamatan }}</span>
                        </div>

                        <h3 class="text-base font-bold text-navy-900 leading-snug hover:text-amber-600 line-clamp-2">
                            <a href="{{ route('public.reports.show', $report->id) }}">{{ $report->title }}</a>
                        </h3>

                        <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                            {{ $report->description }}
                        </p>
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        <span class="font-bold text-slate-700">{{ $report->photos->count() }}</span> Foto
                        @if($report->progressUpdates->count() > 0)
                            • <span class="font-bold text-emerald-600">{{ $report->progressUpdates->count() }} Wk</span> Progres
                        @endif
                    </div>
                    <a href="{{ route('public.reports.show', $report->id) }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs transition">
                        <span>Lihat Timeline</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

            </div>
        @empty
            <div class="col-span-3 py-16 text-center bg-white rounded-3xl border border-slate-200 space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Tidak ada laporan yang sesuai dengan kriteria</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Coba sesuaikan kata kunci pencarian atau reset filter untuk melihat seluruh laporan publik.</p>
                <a href="{{ route('public.reports.index') }}" class="inline-block mt-2 px-5 py-2 rounded-xl bg-navy-900 text-white text-xs font-bold">
                    Tampilkan Semua Laporan
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-10">
        {{ $reports->links() }}
    </div>

</div>
@endsection
