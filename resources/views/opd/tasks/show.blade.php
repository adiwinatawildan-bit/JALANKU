@extends('layouts.admin')

@section('title', 'Tugas Perbaikan: ' . $report->road_name . ' - JALAN KU')
@section('header_title', 'Eksekusi & Update Progres: ' . $report->road_name)

@section('content')
<div class="space-y-8">
    
    <!-- Top Nav -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('opd.tasks.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-slate-500 hover:text-amber-600">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Tugas</span>
        </a>

        <div class="flex items-center space-x-2">
            <span class="font-mono bg-navy-900 text-amber-400 font-bold px-3 py-1 rounded-xl text-xs">#{{ $report->ticket_number }}</span>
            <span class="px-3 py-1 rounded-xl font-bold text-xs {{ $report->status_badge_class }} border">{{ $report->status }}</span>
        </div>
    </div>

    <!-- Overview Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-xs font-bold text-amber-600 uppercase tracking-widest">{{ $report->kecamatan }}, {{ $report->desa }}</span>
                <h2 class="text-2xl font-extrabold text-navy-900 mt-0.5">{{ $report->road_name }}</h2>
                <p class="text-xs text-slate-600 mt-1">{{ $report->title }}</p>
            </div>
            
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center min-w-[170px]">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Capaian Progres</span>
                <p class="text-3xl font-extrabold text-navy-900 mt-1">{{ $report->current_progress }}%</p>
                <div class="w-full h-2 bg-slate-200 rounded-full mt-2 overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $report->current_progress }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block font-semibold">Jenis Cacat</span>
                <span class="font-bold text-navy-900">{{ ucfirst($report->damage_type) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">Tingkat Gangguan</span>
                <span class="font-bold text-navy-900">{{ ucfirst($report->disturbance_level) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">Koordinat GPS</span>
                <span class="font-mono font-bold text-navy-900">{{ $report->location?->latitude }}, {{ $report->location?->longitude }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">Prioritas TOPSIS</span>
                <span class="font-bold text-amber-600">{{ $report->priorityResult?->priority_level ?? 'Normal' }}</span>
            </div>
        </div>
    </div>

    <!-- 2 Column Section: Actions (Survey & Progress Update) vs History -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Forms for Survey & Weekly Progress -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- FORM 1: SURVEI TEKNIS LAPANGAN (Jika belum survei) -->
            @if(in_array($report->status, [\App\Models\Report::STATUS_DITUGASKAN, \App\Models\Report::STATUS_SURVEI]))
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-5">
                    <div class="flex items-center space-x-3 border-b border-slate-100 pb-3">
                        <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-900">Input Hasil Survei Lapangan</h3>
                            <p class="text-[11px] text-slate-500">Catat kondisi teknis di lokasi dan lampirkan maksimal 3 foto survei.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('opd.tasks.survey', $report->id) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan Hasil Survei Teknis <span class="text-rose-500">*</span></label>
                            <textarea name="survey_notes" rows="3" required placeholder="Jelaskan kebutuhan material, dimensi kerusakan, kondisi pondasi, dan rencana penanganan..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">{{ old('survey_notes', $report->survey_notes) }}</textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Foto Survei Lapangan (Maksimal 3 Foto)</label>
                            <input type="file" name="survey_photos[]" multiple accept="image/*" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                            <p class="text-[11px] text-slate-400">Pilih hingga 3 foto (Format JPG, JPEG, PNG, WEBP, maks 5MB per foto).</p>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl shadow transition">
                            <i class="fa-solid fa-check mr-1.5"></i> Simpan Hasil Survei
                        </button>
                    </form>
                </div>
            @endif

            <!-- FORM 2: 29 & 30. UPDATE PROGRES MINGGUAN -->
            @if($report->status !== \App\Models\Report::STATUS_SELESAI)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center space-x-3 border-b border-slate-100 pb-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-person-digging"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-900">Update Progres Mingguan</h3>
                            <p class="text-[11px] text-slate-500">Kirim dokumentasi berkala pengerjaan fisik (Maksimal 3 foto per minggu).</p>
                        </div>
                    </div>

                    <!-- 30. Alert Maksimal 3 Foto -->
                    <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-950 flex items-center space-x-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm"></i>
                        <span><strong>Ketentuan:</strong> Setiap 1 kali update progres, OPD maksimal mengirim <strong>3 FOTO</strong>.</span>
                    </div>

                    <form method="POST" action="{{ route('opd.tasks.progress', $report->id) }}" enctype="multipart/form-data" class="space-y-4" id="form-progress">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Minggu Ke -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Minggu Ke- <span class="text-rose-500">*</span></label>
                                <input type="number" name="week_number" value="{{ old('week_number', $nextWeekNumber) }}" min="1" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                            </div>

                            <!-- Tanggal Update -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tanggal <span class="text-rose-500">*</span></label>
                                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                            </div>

                            <!-- Persentase Progres -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Progres (%) <span class="text-rose-500">*</span></label>
                                <select name="progress_percentage" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                                    <option value="20" {{ old('progress_percentage') == '20' ? 'selected' : '' }}>20% - Pembersihan & Pondasi</option>
                                    <option value="50" {{ old('progress_percentage') == '50' ? 'selected' : '' }}>50% - Sub-base / Drainase</option>
                                    <option value="80" {{ old('progress_percentage') == '80' ? 'selected' : '' }}>80% - Lapisan Aspal / Hotmix</option>
                                    <option value="100" {{ old('progress_percentage') == '100' ? 'selected' : '' }}>100% - Selesai & Marka Jalan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Keterangan Pekerjaan -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Keterangan Pekerjaan Mingguan <span class="text-rose-500">*</span></label>
                            <textarea name="description" rows="3" required placeholder="Contoh: Pembersihan dan persiapan lokasi, perataan agregat sub-base..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">{{ old('description') }}</textarea>
                        </div>

                        <!-- 30. Foto Progres Mingguan (Maks 3) -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Upload Foto Progres (Maksimal 3 Foto) <span class="text-rose-500">*</span></label>
                            <input type="file" name="photos[]" id="progress-photo-input" multiple required accept="image/jpeg,image/png,image/webp,image/jpg" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                            <p class="text-[11px] text-slate-400">Pilih 1 s/d 3 foto (Format JPG, JPEG, PNG, WEBP, maks 5 MB per foto).</p>
                        </div>

                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-navy-950 font-extrabold text-xs rounded-xl shadow-lg transition">
                            <i class="fa-solid fa-cloud-arrow-up mr-1.5"></i> Simpan & Publikasikan Progres Mingguan
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-emerald-50 rounded-3xl p-6 border border-emerald-200 text-center space-y-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-3xl"></i>
                    <h3 class="text-base font-bold text-emerald-900">Perbaikan Selesai 100%</h3>
                    <p class="text-xs text-emerald-800">Laporan ini telah ditandai selesai dan ditutup.</p>
                </div>
            @endif

        </div>

        <!-- Right Column: Timeline & Existing Photos -->
        <div class="lg:col-span-5 space-y-8">
            
            <!-- Foto Kondisi Awal -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-navy-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                    Foto Kondisi Awal (Masyarakat)
                </h3>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($report->initialPhotos as $p)
                        <a href="{{ $p->file_url }}" class="glightbox block h-24 rounded-xl overflow-hidden bg-slate-900 border border-slate-200">
                            <img src="{{ $p->file_url }}" class="w-full h-full object-cover">
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Riwayat Progres Mingguan yang Sudah Dikirim -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
                <h3 class="text-xs font-bold text-navy-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                    Riwayat Progres Mingguan Tercatat
                </h3>

                <div class="space-y-4">
                    @forelse($report->progressUpdates as $upd)
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2 text-xs">
                            <div class="flex justify-between items-center font-bold">
                                <span class="text-navy-900">Minggu {{ $upd->week_number }} ({{ $upd->progress_percentage }}%)</span>
                                <span class="text-slate-400 text-[10px]">{{ \Carbon\Carbon::parse($upd->date)->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-slate-600 text-[11px] leading-relaxed">{{ $upd->description }}</p>

                            <!-- Progress photos with delete button (Section 45) -->
                            <div class="grid grid-cols-3 gap-2 pt-1">
                                @foreach($upd->photos as $pPhoto)
                                    <div class="relative group rounded-lg overflow-hidden border border-slate-200 h-20 bg-slate-900">
                                        <a href="{{ $pPhoto->file_url }}" class="glightbox block w-full h-full">
                                            <img src="{{ $pPhoto->file_url }}" class="w-full h-full object-cover">
                                        </a>
                                        <!-- 45. OPD Hapus Foto Progres -->
                                        <form method="POST" action="{{ route('opd.tasks.delete-photo', $pPhoto->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto progres ini?');" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] flex items-center justify-center shadow">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada update progres mingguan.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
