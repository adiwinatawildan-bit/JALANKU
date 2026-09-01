@extends('layouts.admin')

@section('title', 'Daftar Tugas Perbaikan Jalan - JALAN KU')
@section('header_title', 'Tugas & Penanganan Jalan')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Bar -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('opd.tasks.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama jalan / tiket..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                    <option value="">Semua Status Pengerjaan</option>
                    <option value="DITUGASKAN" {{ request('status') === 'DITUGASKAN' ? 'selected' : '' }}>Ditugaskan</option>
                    <option value="SURVEI" {{ request('status') === 'SURVEI' ? 'selected' : '' }}>Survei</option>
                    <option value="SEDANG DIPERBAIKI" {{ request('status') === 'SEDANG DIPERBAIKI' ? 'selected' : '' }}>Sedang Diperbaiki</option>
                    <option value="SELESAI" {{ request('status') === 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
                    Filter Tugas
                </button>
                <a href="{{ route('opd.tasks.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tasks Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tasks as $task)
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl transition flex flex-col justify-between">
                <div>
                    <!-- Photo Header -->
                    <div class="relative h-44 bg-slate-900 overflow-hidden">
                        @php
                            $cover = $task->photos->first()?->file_url ?? asset('images/road-placeholder.svg');
                        @endphp
                        <img src="{{ $cover }}" class="w-full h-full object-cover">
                        <div class="absolute top-3 left-3">
                            <span class="px-3 py-1 rounded-full text-xs font-bold shadow {{ $task->status_badge_class }} border">
                                {{ $task->status }}
                            </span>
                        </div>
                        <div class="absolute bottom-0 inset-x-0 bg-navy-950/80 p-3 flex justify-between items-center text-xs text-white">
                            <span class="font-mono font-bold text-amber-400">#{{ $task->ticket_number }}</span>
                            <span class="font-extrabold text-emerald-400">{{ $task->current_progress }}% Progres</span>
                        </div>
                    </div>

                    <div class="p-5 space-y-2.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ $task->kecamatan }}</span>
                        <h3 class="text-base font-bold text-navy-900 leading-snug">{{ $task->road_name }}</h3>
                        <p class="text-xs text-slate-600 line-clamp-2">{{ $task->title }}</p>
                    </div>
                </div>

                <div class="p-5 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">
                        {{ $task->progressUpdates->count() }} Kali Update
                    </span>
                    <a href="{{ route('opd.tasks.show', $task->id) }}" class="px-4 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition">
                        Buka & Update &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-12 text-center bg-white rounded-3xl border border-slate-200 text-slate-400 text-xs">
                Tidak ada tugas perbaikan yang ditemukan.
            </div>
        @endforelse
    </div>

    <div>
        {{ $tasks->links() }}
    </div>

</div>
@endsection
