@extends('layouts.admin')

@section('title', 'Periksa Laporan #' . $report->ticket_number . ' - JALAN KU')
@section('header_title', 'Detail Pengawasan Laporan #' . $report->ticket_number)

@section('content')
<div class="space-y-8">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-slate-500 hover:text-amber-600">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Laporan</span>
        </a>

        <!-- Status Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            
            @if($report->status === \App\Models\Report::STATUS_DIAJUKAN)
                <!-- Verify Button -->
                <form method="POST" action="{{ route('admin.reports.verify', $report->id) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Verifikasi Laporan</span>
                    </button>
                </form>

                <!-- Reject Button (Modal Trigger) -->
                <button type="button" onclick="openModal('modal-reject')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-ban"></i>
                    <span>Tolak Laporan</span>
                </button>
            @endif

            <!-- Mark Duplicate Modal Trigger -->
            @if($report->status !== \App\Models\Report::STATUS_DUPLIKAT)
                <button type="button" onclick="openModal('modal-duplicate')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-copy"></i>
                    <span>Tandai Duplikat</span>
                </button>
            @endif

            <!-- Run YOLO AI Analysis -->
            <form method="POST" action="{{ route('admin.reports.yolo', $report->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-brain text-amber-400"></i>
                    <span>Jalankan YOLO AI</span>
                </button>
            </form>

            <!-- Delete Report Button -->
            <form method="POST" action="{{ route('admin.reports.delete', $report->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan #{{ $report->ticket_number }} secara permanen? Seluruh foto, progres, dan data terkait akan dihapus total dari database.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 font-bold text-xs rounded-xl border border-rose-200 shadow-sm transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Hapus Laporan</span>
                </button>
            </form>

        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Details & Photos -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Information Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="font-mono text-xs font-bold text-amber-600">#{{ $report->ticket_number }}</span>
                        <h2 class="text-2xl font-extrabold text-navy-900 mt-0.5">{{ $report->road_name }}</h2>
                        <p class="text-sm font-semibold text-slate-700 mt-1">{{ $report->title }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $report->status_badge_class }} border">
                        {{ $report->status }}
                    </span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    {{ $report->description }}
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-semibold">Pelapor</span>
                        <span class="font-bold text-navy-900">{{ $report->user->name }}</span>
                        <span class="text-[10px] text-slate-500 block">{{ $report->user->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Kecamatan / Desa</span>
                        <span class="font-bold text-navy-900">{{ $report->kecamatan }}</span>
                        <span class="text-[10px] text-slate-500 block">{{ $report->desa }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Jenis / Tingkat Cacat</span>
                        <span class="font-bold text-navy-900">{{ ucfirst($report->damage_type) }}</span>
                        <span class="text-[10px] text-slate-500 block">Tingkat: {{ ucfirst($report->disturbance_level) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Koordinat GPS</span>
                        <span class="font-mono text-[11px] font-bold text-navy-900 block">{{ $report->location?->latitude }}, {{ $report->location?->longitude }}</span>
                    </div>
                </div>
            </div>

            <!-- 21. HASIL ANALISIS AI YOLO -->
            <div class="bg-gradient-to-tr from-navy-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-navy-950 flex items-center justify-center font-extrabold text-lg">
                            <i class="fa-solid fa-brain"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Hasil Deteksi Cacat AI (YOLO Vision)</h3>
                            <p class="text-xs text-slate-400">Ultralytics YOLO & OpenCV Computer Vision Analysis</p>
                        </div>
                    </div>
                    @php
                        $latestDetection = $report->damageDetections->first();
                    @endphp
                    @if($latestDetection)
                        <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            Confidence: {{ $latestDetection->confidence_score }}%
                        </span>
                    @endif
                </div>

                @if($latestDetection)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Landslide (Longsor)</span>
                            <p class="text-2xl font-extrabold text-rose-400 mt-1">{{ $latestDetection->detected_classes['landslide'] ?? 0 }}</p>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Pothole (Lubang)</span>
                            <p class="text-2xl font-extrabold text-amber-400 mt-1">{{ $latestDetection->detected_classes['pothole'] ?? 0 }}</p>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Crack (Retakan)</span>
                            <p class="text-2xl font-extrabold text-sky-400 mt-1">{{ $latestDetection->detected_classes['crack'] ?? 0 }}</p>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Total Defect</span>
                            <p class="text-2xl font-extrabold text-emerald-400 mt-1">{{ $latestDetection->total_defects }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4 text-slate-400 text-xs">
                        Belum ada data deteksi YOLO. Klik tombol "Jalankan YOLO AI" di atas untuk menganalisis foto.
                    </div>
                @endif
            </div>

            <!-- 41. FOTO KONDISI AWAL & MODERASI HAPUS FOTO -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-navy-900 flex items-center">
                        <i class="fa-solid fa-images text-amber-500 mr-2"></i> Foto Kondisi Awal (Maksimal 3 Foto)
                    </h3>
                    <span class="text-xs text-slate-400">{{ $report->initialPhotos->count() }} Terunggah</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @forelse($report->initialPhotos as $photo)
                        <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-50 flex flex-col justify-between shadow-sm">
                            <a href="{{ $photo->file_url }}" class="glightbox block h-40 bg-slate-900 relative group" data-gallery="admin-photos">
                                <img src="{{ $photo->file_url }}" alt="{{ $photo->file_name }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                <div class="absolute inset-0 bg-navy-950/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                    <i class="fa-solid fa-magnifying-glass-plus text-xl text-amber-400"></i>
                                </div>
                            </a>
                            <div class="p-3 flex items-center justify-between bg-white border-t border-slate-100">
                                <span class="text-[11px] font-semibold text-slate-600 truncate">{{ $photo->file_name }}</span>
                                
                                <!-- 41 & 43. Tombol Hapus Foto dengan Konfirmasi Modal -->
                                <button type="button" onclick="confirmDeletePhoto('{{ route('admin.photos.delete', $photo->id) }}', '{{ $photo->file_name }}')" class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Hapus Foto
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-6 text-xs text-slate-400">Tidak ada foto kondisi awal.</div>
                    @endforelse
                </div>
            </div>

            <!-- Progres Mingguan OPD & Foto -->
            @if($report->progressUpdates->count() > 0)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <h3 class="text-base font-bold text-navy-900 flex items-center border-b border-slate-100 pb-4">
                        <i class="fa-solid fa-list-check text-emerald-500 mr-2"></i> Dokumentasi Mingguan OPD Pelaksana
                    </h3>

                    <div class="space-y-6">
                        @foreach($report->progressUpdates as $upd)
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-bold text-navy-900">Minggu {{ $upd->week_number }} (Progres: {{ $upd->progress_percentage }}%)</span>
                                    <span class="text-slate-400">{{ \Carbon\Carbon::parse($upd->date)->translatedFormat('d M Y') }}</span>
                                </div>
                                <p class="text-xs text-slate-600">{{ $upd->description }}</p>

                                <div class="grid grid-cols-3 gap-3 pt-2">
                                    @foreach($upd->photos as $pPhoto)
                                        <div class="rounded-xl overflow-hidden border border-slate-200 bg-white">
                                            <a href="{{ $pPhoto->file_url }}" class="glightbox block h-28 bg-slate-900" data-gallery="admin-progress">
                                                <img src="{{ $pPhoto->file_url }}" class="w-full h-full object-cover">
                                            </a>
                                            <div class="p-2 flex justify-between items-center text-[10px]">
                                                <span class="text-slate-500 truncate">{{ $pPhoto->file_name }}</span>
                                                <button type="button" onclick="confirmDeletePhoto('{{ route('admin.progress-photos.delete', $pPhoto->id) }}', '{{ $pPhoto->file_name }}')" class="text-rose-600 hover:text-rose-800 font-bold">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Column: TOPSIS SPK Decision Card & Assignment -->
        <div class="lg:col-span-4 space-y-8">
            
            <!-- 24. REKOMENDASI PRIORITAS TOPSIS -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Decision Support System</span>
                    <h3 class="text-base font-bold text-navy-900 mt-0.5">Rekomendasi Prioritas (TOPSIS)</h3>
                </div>

                @if($report->priorityResult)
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Skor Preferensi Prioritas</span>
                        <p class="text-3xl font-extrabold text-navy-900">{{ number_format($report->priorityResult->score, 4) }}</p>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $report->priorityResult->badge_class }} border mt-1">
                            {{ $report->priorityResult->priority_level }} (Peringkat #{{ $report->priorityResult->rank }})
                        </span>
                    </div>

                    <!-- Alasan Rekomendasi (Section 24) -->
                    <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-950 space-y-1">
                        <strong class="font-bold block text-amber-900 flex items-center">
                            <i class="fa-solid fa-lightbulb mr-1.5 text-amber-600"></i> Alasan Rekomendasi Sistem:
                        </strong>
                        <p class="leading-relaxed">{{ $report->priorityResult->reasoning }}</p>
                    </div>

                    <p class="text-[11px] text-slate-400 italic">
                        *TOPSIS hanya memberikan rekomendasi prioritas objektif. Keputusan akhir penugasan tetap berada pada admin pemerintah.
                    </p>
                @else
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada skor prioritas yang dikalkulasi.</p>
                @endif
            </div>

            <!-- PENUGASAN KE OPD (DIVERIFIKASI -> DITUGASKAN) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-navy-900 border-b border-slate-100 pb-3 flex items-center">
                    <i class="fa-solid fa-building-circle-check text-sky-500 mr-2"></i> Penugasan OPD Terkait
                </h3>

                <form method="POST" action="{{ route('admin.reports.assign', $report->id) }}" class="space-y-4">
                    @csrf

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih OPD Berwenang</label>
                        <select name="opd_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                            <option value="">-- Pilih Dinas / Instansi --</option>
                            @foreach($opds as $opd)
                                <option value="{{ $opd->id }}" {{ $report->opd_id == $opd->id ? 'selected' : '' }}>
                                    {{ $opd->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
                        <i class="fa-solid fa-paper-plane mr-1.5"></i> Tugaskan Sekarang
                    </button>
                </form>
            </div>

            <!-- Riwayat Status Perpindahan -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-navy-900 border-b border-slate-100 pb-3">Riwayat Status Laporan</h3>
                <div class="space-y-3 text-xs">
                    @foreach($report->statusHistories as $hist)
                        <div class="border-l-2 border-slate-300 pl-3 space-y-0.5">
                            <span class="font-bold text-navy-900 block">{{ $hist->to_status }}</span>
                            <p class="text-[11px] text-slate-500">{{ $hist->notes }}</p>
                            <span class="text-[10px] text-slate-400">{{ $hist->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>

<!-- 43. MODAL KONFIRMASI HAPUS FOTO -->
<div id="modal-delete-photo" class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-6 shadow-2xl border border-slate-200">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl mx-auto">
            <i class="fa-solid fa-trash-can"></i>
        </div>
        <div class="text-center space-y-2">
            <h3 class="text-lg font-bold text-navy-900">Hapus Foto?</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin menghapus foto ini? Foto akan dihapus secara permanen dari penyimpanan cloud storage dan database.
            </p>
            <p id="delete-target-filename" class="text-xs font-mono font-bold text-rose-600"></p>
        </div>
        <form id="delete-photo-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="closeModal('modal-delete-photo')" class="py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">
                    Batal
                </button>
                <button type="submit" class="py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow">
                    🗑 Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TOLAK LAPORAN -->
<div id="modal-reject" class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-5 shadow-2xl border border-slate-200">
        <h3 class="text-base font-bold text-navy-900 border-b border-slate-100 pb-3">Tolak Pengaduan Ini</h3>
        <form method="POST" action="{{ route('admin.reports.reject', $report->id) }}" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="rejection_reason" rows="3" required placeholder="Jelaskan alasan laporan tidak dapat diproses (misal: foto bukan jalan umum, lokasi tidak jelas, dsb)..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('modal-reject')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-bold text-xs rounded-xl shadow">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TANDAI DUPLIKAT -->
<div id="modal-duplicate" class="fixed inset-0 bg-navy-950/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-5 shadow-2xl border border-slate-200">
        <h3 class="text-base font-bold text-navy-900 border-b border-slate-100 pb-3">Tandai Sebagai Laporan Duplikat</h3>
        <form method="POST" action="{{ route('admin.reports.duplicate', $report->id) }}" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Pilih Laporan Utama</label>
                <select name="duplicate_of_id" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:outline-none">
                    <option value="">-- Pilih Laporan Utama --</option>
                    @foreach($otherReports as $other)
                        <option value="{{ $other->id }}">#{{ $other->ticket_number }} - {{ $other->road_name }} ({{ $other->kecamatan }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('modal-duplicate')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white font-bold text-xs rounded-xl shadow">Tandai Duplikat</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    // 41, 42, 43. Hapus Foto Confirmation Handler
    function confirmDeletePhoto(actionUrl, fileName) {
        document.getElementById('delete-photo-form').action = actionUrl;
        document.getElementById('delete-target-filename').innerText = fileName;
        openModal('modal-delete-photo');
    }
</script>
@endpush
