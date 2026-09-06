<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\ReportStatusHistory;
use App\Models\User;
use App\Services\StorageService;
use App\Services\TopsisService;
use App\Services\YoloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    protected StorageService $storageService;
    protected YoloService $yoloService;
    protected TopsisService $topsisService;

    public function __construct(
        StorageService $storageService,
        YoloService $yoloService,
        TopsisService $topsisService
    ) {
        $this->storageService = $storageService;
        $this->yoloService = $yoloService;
        $this->topsisService = $topsisService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $myReports = Report::with(['location', 'photos', 'progressUpdates'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'total_laporan' => $myReports->count(),
            'diproses' => $myReports->whereIn('status', [
                Report::STATUS_DIVERIFIKASI,
                Report::STATUS_DITUGASKAN,
                Report::STATUS_SURVEI,
                Report::STATUS_MENUNGGU_PERBAIKAN,
            ])->count(),
            'diperbaiki' => $myReports->where('status', Report::STATUS_SEDANG_DIPERBAIKI)->count(),
            'selesai' => $myReports->where('status', Report::STATUS_SELESAI)->count(),
        ];

        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('masyarakat.dashboard', compact('stats', 'myReports', 'notifications'));
    }

    public function index()
    {
        $user = Auth::user();
        $reports = Report::with(['location', 'photos', 'progressUpdates.photos', 'priorityResult'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('masyarakat.reports.index', compact('reports'));
    }

    public function create()
    {
        $kecamatanList = [
            'Kecamatan Sentral',
            'Kecamatan Timur',
            'Kecamatan Barat',
            'Kecamatan Utara',
            'Kecamatan Selatan',
            'Kecamatan Cikajang',
            'Kecamatan Sukamaju',
        ];

        return view('masyarakat.reports.create', compact('kecamatanList'));
    }

    public function store(Request $request)
    {
        @set_time_limit(120);

        // 16 & 39. VALIDASI LARAVEL (Maksimal 3 Foto, format JPG, JPEG, PNG, WEBP, maks 5MB)
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'road_name' => ['required', 'string', 'max:255'],
            'address_detail' => ['nullable', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'desa' => ['required', 'string', 'max:100'],
            'damage_type' => ['nullable', 'string'],
            'disturbance_level' => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
            'photos' => ['required', 'array', 'min:1', 'max:3'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'photos.max' => 'Maksimal 3 foto untuk setiap laporan.',
            'photos.*.max' => 'Ukuran foto tidak boleh melebihi 5 MB.',
            'photos.*.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
        ]);

        $user = Auth::user();

        // Anti-duplicate protection: check if same user submitted same title or road within last 60 seconds
        $recentReport = Report::where('user_id', $user->id)
            ->where(function ($q) use ($validated) {
                $q->where('title', $validated['title'])
                  ->orWhere('road_name', $validated['road_name']);
            })
            ->where('created_at', '>=', now()->subSeconds(60))
            ->first();

        if ($recentReport) {
            return redirect()->route('masyarakat.reports.show', $recentReport->id)
                ->with('success', "Laporan Anda dengan nomor tiket {$recentReport->ticket_number} telah berhasil dikirim!");
        }

        $ticketNumber = 'JLK-' . date('Ym') . '-' . strtoupper(Str::random(5));

        // Auto-detect duplicate report if within ~35 meters (or ~50m on same road)
        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];

        $candidateLocations = Location::whereBetween('latitude', [$lat - 0.0006, $lat + 0.0006])
            ->whereBetween('longitude', [$lng - 0.0006, $lng + 0.0006])
            ->whereHas('report', function ($q) {
                $q->whereNotIn('status', [Report::STATUS_DITOLAK, Report::STATUS_DUPLIKAT, Report::STATUS_SELESAI]);
            })
            ->with(['report.duplicateOf'])
            ->get();

        $parentReport = null;
        $minDistance = null;

        foreach ($candidateLocations as $cand) {
            if (!$cand->report) continue;

            $dLat = deg2rad($cand->latitude - $lat);
            $dLon = deg2rad($cand->longitude - $lng);
            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($lat)) * cos(deg2rad($cand->latitude)) *
                 sin($dLon / 2) * sin($dLon / 2);
            $distance = 6371000 * 2 * atan2(sqrt($a), sqrt(1 - $a));

            $isSameRoad = strtolower(trim($cand->report->road_name)) === strtolower(trim($validated['road_name']));

            if ($distance <= 35.0 || ($distance <= 50.0 && $isSameRoad)) {
                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                    $parentReport = $cand->report->duplicate_of_id ? $cand->report->duplicateOf : $cand->report;
                }
            }
        }

        // 1. Create Report in DB quickly
        $report = DB::transaction(function () use ($validated, $user, $ticketNumber, $parentReport, $minDistance) {
            $clusterId = null;
            if ($parentReport) {
                $clusterId = $parentReport->cluster_id ?: ('CLS-' . $parentReport->id);
                if (!$parentReport->cluster_id) {
                    $parentReport->update(['cluster_id' => $clusterId]);
                }
            }

            $report = Report::create([
                'ticket_number' => $ticketNumber,
                'user_id' => $user->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'road_name' => $validated['road_name'],
                'kecamatan' => $validated['kecamatan'],
                'desa' => $validated['desa'],
                'damage_type' => $validated['damage_type'] ?? 'menunggu_analisis',
                'disturbance_level' => $validated['disturbance_level'] ?? 'sedang',
                'additional_info' => $validated['additional_info'] ?? null,
                'status' => $parentReport ? Report::STATUS_DUPLIKAT : Report::STATUS_DIAJUKAN,
                'duplicate_of_id' => $parentReport?->id,
                'cluster_id' => $clusterId,
                'is_public' => true,
            ]);

            Location::create([
                'report_id' => $report->id,
                'road_name' => $validated['road_name'],
                'address_detail' => $validated['address_detail'] ?? null,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'kecamatan' => $validated['kecamatan'],
                'desa' => $validated['desa'],
            ]);

            $notes = $parentReport
                ? "Sistem Otomatis: Terdeteksi duplikat dari laporan #{$parentReport->ticket_number} (Jarak GPS ~" . round($minDistance ?? 0) . "m). Otomatis digabung untuk mempercepat prioritas perbaikan."
                : 'Laporan diajukan oleh masyarakat via portal web.';

            ReportStatusHistory::create([
                'report_id' => $report->id,
                'from_status' => null,
                'to_status' => $report->status,
                'notes' => $notes,
                'changed_by' => $user->id,
            ]);

            if ($parentReport && $parentReport->assessment) {
                $parentReport->assessment->increment('c4_report_count');
            }

            return $report;
        });

        // 2. Upload up to 3 Photos to Supabase/Local Storage safely
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            $index = 1;
            foreach (array_slice($files, 0, 3) as $file) {
                try {
                    $stored = $this->storageService->uploadInitialPhoto($file, $report->id, $index);
                    ReportPhoto::create([
                        'report_id' => $report->id,
                        'file_name' => $stored['file_name'],
                        'file_path' => $stored['file_path'],
                        'file_url' => $stored['file_url'],
                        'photo_type' => 'initial',
                        'caption' => "Foto Kondisi Awal #{$index}",
                        'uploaded_by' => $user->id,
                    ]);
                    $index++;
                } catch (\Throwable $e) {
                    Log::error("Photo upload error on report #{$report->id}: " . $e->getMessage());
                }
            }
        }

        // 3. Run YOLO AI Analysis & TOPSIS safely without breaking report flow
        try {
            $yoloResult = $this->yoloService->analyzeReport($report, $user->id);
            if (!empty($yoloResult['success'])) {
                if (($yoloResult['landslides'] ?? 0) > 0) {
                    $report->update(['damage_type' => 'landslide', 'disturbance_level' => 'sangat_parah']);
                } elseif (($yoloResult['potholes'] ?? 0) > 0) {
                    $report->update(['damage_type' => 'pothole', 'disturbance_level' => ($yoloResult['potholes'] >= 3 ? 'tinggi' : 'sedang')]);
                } elseif (($yoloResult['cracks'] ?? 0) > 0) {
                    $report->update(['damage_type' => 'crack', 'disturbance_level' => 'sedang']);
                } else {
                    $report->update(['damage_type' => 'normal', 'disturbance_level' => 'rendah']);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('YOLO analysis notice on report store: ' . $e->getMessage());
        }

        try {
            $this->topsisService->calculateAll();
        } catch (\Throwable $e) {
            Log::warning('TOPSIS calculation notice on report store: ' . $e->getMessage());
        }

        // 4. Notify user & admin
        try {
            $userMsg = $parentReport
                ? "Laporan Anda (#{$ticketNumber}) di {$report->road_name} telah diterima dan otomatis digabungkan dengan laporan serupa (#{$parentReport->ticket_number}) untuk memperkuat urgensi perbaikan jalan."
                : "Laporan Anda (#{$ticketNumber}) di {$report->road_name} telah diterima dan sedang menunggu verifikasi admin.";

            Notification::create([
                'user_id' => $user->id,
                'type' => 'success',
                'title' => $parentReport ? 'Laporan Berhasil Digabungkan' : 'Laporan Berhasil Diajukan',
                'message' => $userMsg,
                'link_url' => route('masyarakat.reports.show', $report->id),
            ]);

            $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
            $adminMsg = $parentReport
                ? "Laporan baru #{$ticketNumber} otomatis digabungkan sebagai duplikat dari #{$parentReport->ticket_number} (lokasi GPS berdekatan)."
                : "Laporan baru #{$ticketNumber} di {$report->road_name} membutuhkan verifikasi.";

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'info',
                    'title' => $parentReport ? 'Laporan Duplikat Otomatis' : 'Laporan Baru Masuk',
                    'message' => $adminMsg,
                    'link_url' => route('admin.reports.show', $report->id),
                ]);
            }

            AuditLog::record(
                activity: $parentReport ? "Pengaduan Duplikat Otomatis #{$ticketNumber}" : "Pengaduan Baru #{$ticketNumber}",
                targetType: 'Report',
                targetId: $report->id,
                description: "Masyarakat {$user->name} membuat laporan kerusakan jalan di {$report->road_name}." . ($parentReport ? " (Tergabung ke #{$parentReport->ticket_number})" : ""),
                userId: $user->id
            );
        } catch (\Throwable $e) {
            Log::warning('Notification/Audit notice: ' . $e->getMessage());
        }

        $successMsg = $parentReport
            ? "Laporan Anda dengan nomor tiket {$ticketNumber} berhasil dikirim! Karena berada di titik yang sama dengan laporan #{$parentReport->ticket_number}, laporan Anda otomatis digabungkan untuk mempercepat prioritas perbaikan jalan."
            : "Laporan Anda dengan nomor tiket {$ticketNumber} berhasil dikirim dan sedang diproses!";

        return redirect()->route('masyarakat.reports.show', $report->id)
            ->with('success', $successMsg);
    }

    public function show($id)
    {
        $user = Auth::user();
        $report = Report::with([
            'location',
            'initialPhotos',
            'surveyPhotos',
            'progressUpdates.photos',
            'statusHistories.changer',
            'damageDetections',
            'opd',
            'priorityResult',
            'duplicateOf',
            'duplicates'
        ])->where('user_id', $user->id)->findOrFail($id);

        return view('masyarakat.reports.show', compact('report'));
    }

    public function submitFeedback(Request $request, $id)
    {
        $user = Auth::user();
        $report = Report::where('user_id', $user->id)->findOrFail($id);

        if ($report->status !== Report::STATUS_SELESAI) {
            return back()->with('error', 'Feedback hanya dapat diberikan setelah perbaikan selesai 100%.');
        }

        $validated = $request->validate([
            'citizen_rating' => ['required', 'integer', 'between:1,5'],
            'citizen_feedback' => ['required', 'string', 'max:1000'],
        ]);

        $report->update([
            'citizen_rating' => $validated['citizen_rating'],
            'citizen_feedback' => $validated['citizen_feedback'],
        ]);

        AuditLog::record(
            activity: 'Submit Feedback Masyarakat',
            targetType: 'Report',
            targetId: $report->id,
            description: "Pengguna {$user->name} memberikan rating {$validated['citizen_rating']} bintang untuk laporan #{$report->ticket_number}.",
            userId: $user->id
        );

        return back()->with('success', 'Terima kasih atas feedback dan penilaian Anda!');
    }
}
