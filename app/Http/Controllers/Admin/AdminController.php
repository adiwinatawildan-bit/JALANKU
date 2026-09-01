<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Opd;
use App\Models\PriorityResult;
use App\Models\ProgressPhoto;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\ReportStatusHistory;
use App\Models\RoadAssessment;
use App\Models\User;
use App\Services\StorageService;
use App\Services\TopsisService;
use App\Services\YoloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
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
        // 25. DASHBOARD ADMIN
        $stats = [
            'total_laporan' => Report::count(),
            'laporan_baru' => Report::where('status', Report::STATUS_DIAJUKAN)->count(),
            'diverifikasi' => Report::where('status', Report::STATUS_DIVERIFIKASI)->count(),
            'ditugaskan' => Report::where('status', Report::STATUS_DITUGASKAN)->count(),
            'survei' => Report::where('status', Report::STATUS_SURVEI)->count(),
            'sedang_diperbaiki' => Report::where('status', Report::STATUS_SEDANG_DIPERBAIKI)->count(),
            'selesai' => Report::where('status', Report::STATUS_SELESAI)->count(),
            'terlambat' => Report::whereIn('status', [Report::STATUS_DITUGASKAN, Report::STATUS_SURVEI, Report::STATUS_SEDANG_DIPERBAIKI])
                ->where('updated_at', '<', now()->subDays(14))
                ->count(),
        ];

        // Perlu Tindakan
        $actionRequired = [
            'belum_diverifikasi' => Report::with(['location', 'photos'])
                ->where('status', Report::STATUS_DIAJUKAN)
                ->latest()
                ->take(5)
                ->get(),
            'belum_ditugaskan' => Report::with(['location', 'priorityResult'])
                ->where('status', Report::STATUS_DIVERIFIKASI)
                ->whereNull('opd_id')
                ->latest()
                ->take(5)
                ->get(),
            'terlambat' => Report::with(['location', 'opd'])
                ->whereIn('status', [Report::STATUS_DITUGASKAN, Report::STATUS_SURVEI, Report::STATUS_SEDANG_DIPERBAIKI])
                ->where('updated_at', '<', now()->subDays(14))
                ->latest('updated_at')
                ->take(5)
                ->get(),
        ];

        // TOP 10 Prioritas (TOPSIS)
        $topPriorities = Report::with(['location', 'priorityResult', 'opd', 'photos'])
            ->whereNotIn('status', [Report::STATUS_SELESAI, Report::STATUS_DITOLAK, Report::STATUS_DUPLIKAT])
            ->whereHas('priorityResult')
            ->join('priority_results', 'reports.id', '=', 'priority_results.report_id')
            ->orderBy('priority_results.score', 'desc')
            ->select('reports.*')
            ->take(10)
            ->get();

        // Chart Data (SQLite & MySQL compatible)
        $monthExpr = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at) as month"
            : "DATE_FORMAT(created_at, '%Y-%m') as month";

        $monthlyData = Report::select(
                DB::raw($monthExpr),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(6)
            ->get();

        $damageTypeData = Report::select('damage_type', DB::raw('count(*) as count'))
            ->groupBy('damage_type')
            ->pluck('count', 'damage_type');

        $opdList = Opd::where('is_active', true)->get();

        return view('admin.dashboard', compact(
            'stats',
            'actionRequired',
            'topPriorities',
            'monthlyData',
            'damageTypeData',
            'opdList'
        ));
    }

    public function reports(Request $request)
    {
        $query = Report::with(['user', 'location', 'photos', 'opd', 'priorityResult', 'damageDetections']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        if ($request->filled('priority')) {
            $priority = $request->priority;
            $query->whereHas('priorityResult', fn($q) => $q->where('priority_level', $priority));
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('ticket_number', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%")
                    ->orWhere('road_name', 'like', "%{$q}%")
                    ->orWhere('kecamatan', 'like', "%{$q}%");
            });
        }

        $reports = $query->latest()->paginate(15)->withQueryString();
        $opds = Opd::where('is_active', true)->get();

        return view('admin.reports.index', compact('reports', 'opds'));
    }

    public function show($id)
    {
        $report = Report::with([
            'user',
            'location',
            'photos.damageDetections',
            'surveyPhotos',
            'progressUpdates.photos',
            'statusHistories.changer',
            'damageDetections',
            'assessment',
            'priorityResult',
            'opd',
            'duplicates'
        ])->findOrFail($id);

        $opds = Opd::where('is_active', true)->get();
        $otherReports = Report::where('id', '!=', $id)
            ->whereNotIn('status', [Report::STATUS_DITOLAK, Report::STATUS_DUPLIKAT])
            ->latest()
            ->take(50)
            ->get();

        return view('admin.reports.show', compact('report', 'opds', 'otherReports'));
    }

    public function verify($id)
    {
        $admin = Auth::user();
        $report = Report::findOrFail($id);

        $report->update([
            'status' => Report::STATUS_DIVERIFIKASI,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        ReportStatusHistory::create([
            'report_id' => $report->id,
            'from_status' => Report::STATUS_DIAJUKAN,
            'to_status' => Report::STATUS_DIVERIFIKASI,
            'notes' => 'Laporan telah diverifikasi oleh tim Admin Pengawas.',
            'changed_by' => $admin->id,
        ]);

        // Run YOLO detection & TOPSIS calculation
        $this->yoloService->analyzeReport($report, $admin->id);
        $this->topsisService->calculateAll();

        // Notify citizen
        Notification::create([
            'user_id' => $report->user_id,
            'type' => 'info',
            'title' => 'Laporan Telah Diverifikasi',
            'message' => "Laporan Anda (#{$report->ticket_number}) telah diverifikasi oleh admin dan diteruskan ke tahap penanganan prioritas.",
            'link_url' => route('masyarakat.reports.show', $report->id),
        ]);

        AuditLog::record(
            activity: 'Verifikasi Laporan #' . $report->ticket_number,
            targetType: 'Report',
            targetId: $report->id,
            description: "Admin {$admin->name} memverifikasi laporan di {$report->road_name}.",
            userId: $admin->id
        );

        return back()->with('success', "Laporan #{$report->ticket_number} berhasil diverifikasi.");
    }

    public function reject(Request $request, $id)
    {
        $admin = Auth::user();
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $oldStatus = $report->status;
        $report->update([
            'status' => Report::STATUS_DITOLAK,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        ReportStatusHistory::create([
            'report_id' => $report->id,
            'from_status' => $oldStatus,
            'to_status' => Report::STATUS_DITOLAK,
            'notes' => 'Alasan penolakan: ' . $validated['rejection_reason'],
            'changed_by' => $admin->id,
        ]);

        Notification::create([
            'user_id' => $report->user_id,
            'type' => 'alert',
            'title' => 'Laporan Tidak Dapat Diproses',
            'message' => "Laporan #{$report->ticket_number} ditolak. Alasan: {$validated['rejection_reason']}",
            'link_url' => route('masyarakat.reports.show', $report->id),
        ]);

        AuditLog::record(
            activity: 'Penolakan Laporan #' . $report->ticket_number,
            targetType: 'Report',
            targetId: $report->id,
            description: "Admin menolak laporan dengan alasan: {$validated['rejection_reason']}",
            userId: $admin->id
        );

        return back()->with('success', 'Laporan telah ditandai sebagai ditolak.');
    }

    public function markDuplicate(Request $request, $id)
    {
        $admin = Auth::user();
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'duplicate_of_id' => ['required', 'exists:reports,id', 'different:id'],
        ]);

        $targetReport = Report::findOrFail($validated['duplicate_of_id']);
        $oldStatus = $report->status;

        $report->update([
            'status' => Report::STATUS_DUPLIKAT,
            'duplicate_of_id' => $targetReport->id,
        ]);

        // Increment cluster count on parent report assessment
        if ($targetReport->assessment) {
            $targetReport->assessment->increment('c4_report_count');
        }

        ReportStatusHistory::create([
            'report_id' => $report->id,
            'from_status' => $oldStatus,
            'to_status' => Report::STATUS_DUPLIKAT,
            'notes' => "Ditandai sebagai duplikat dari laporan #{$targetReport->ticket_number}",
            'changed_by' => $admin->id,
        ]);

        $this->topsisService->calculateAll();

        AuditLog::record(
            activity: 'Penandaan Laporan Duplikat #' . $report->ticket_number,
            targetType: 'Report',
            targetId: $report->id,
            description: "Laporan digabungkan ke laporan utama #{$targetReport->ticket_number}.",
            userId: $admin->id
        );

        return back()->with('success', "Laporan berhasil ditandai sebagai duplikat dari #{$targetReport->ticket_number}.");
    }

    public function assignOpd(Request $request, $id)
    {
        $admin = Auth::user();
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'opd_id' => ['required', 'exists:opds,id'],
        ]);

        $opd = Opd::findOrFail($validated['opd_id']);
        $oldStatus = $report->status;

        $report->update([
            'opd_id' => $opd->id,
            'status' => Report::STATUS_DITUGASKAN,
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        ReportStatusHistory::create([
            'report_id' => $report->id,
            'from_status' => $oldStatus,
            'to_status' => Report::STATUS_DITUGASKAN,
            'notes' => "Laporan ditugaskan kepada OPD: {$opd->name}",
            'changed_by' => $admin->id,
        ]);

        // Notify OPD officers
        $officers = User::where('opd_id', $opd->id)->get();
        foreach ($officers as $officer) {
            Notification::create([
                'user_id' => $officer->id,
                'type' => 'warning',
                'title' => 'Tugas Perbaikan Baru',
                'message' => "Laporan baru #{$report->ticket_number} di {$report->road_name} telah ditugaskan ke OPD Anda.",
                'link_url' => route('opd.tasks.show', $report->id),
            ]);
        }

        // Notify citizen
        Notification::create([
            'user_id' => $report->user_id,
            'type' => 'info',
            'title' => 'Laporan Diteruskan ke OPD',
            'message' => "Laporan Anda (#{$report->ticket_number}) telah ditugaskan ke {$opd->name} untuk dilakukan survei dan penanganan.",
            'link_url' => route('masyarakat.reports.show', $report->id),
        ]);

        AuditLog::record(
            activity: 'Penugasan Laporan ke OPD',
            targetType: 'Report',
            targetId: $report->id,
            description: "Laporan #{$report->ticket_number} ditugaskan ke {$opd->name}.",
            userId: $admin->id
        );

        return back()->with('success', "Laporan berhasil ditugaskan ke {$opd->name}.");
    }

    public function runYoloAnalysis($id)
    {
        $admin = Auth::user();
        $report = Report::with('photos')->findOrFail($id);

        $result = $this->yoloService->analyzeReport($report, $admin->id);

        if ($result['success']) {
            return back()->with('success', "Analisis YOLO selesai. Ditemukan {$result['total_defects']} titik kerusakan (Confidence: {$result['confidence']}%).");
        }

        return back()->with('error', $result['message'] ?? 'Gagal menjalankan analisis foto YOLO.');
    }

    public function recalculateTopsis()
    {
        $results = $this->topsisService->calculateAll();
        return back()->with('success', "Kalkulasi prioritas TOPSIS berhasil diperbarui untuk {$results->count()} laporan.");
    }

    public function deleteReport($id)
    {
        $admin = Auth::user();
        $report = Report::with(['photos', 'progressUpdates.photos'])->findOrFail($id);
        $ticket = $report->ticket_number;

        // 1. Delete physical files from storage
        foreach ($report->photos as $photo) {
            if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
        }
        foreach ($report->progressUpdates as $update) {
            foreach ($update->photos as $pPhoto) {
                if ($pPhoto->file_path && Storage::disk('public')->exists($pPhoto->file_path)) {
                    Storage::disk('public')->delete($pPhoto->file_path);
                }
            }
        }

        // 2. Cascade delete database records cleanly
        $report->photos()->delete();
        foreach ($report->progressUpdates as $update) {
            $update->photos()->delete();
            $update->delete();
        }
        $report->statusHistories()->delete();
        $report->detections()->delete();
        $report->assessment()?->delete();
        $report->priorityResult()?->delete();

        // 3. Remove notification links for this report
        Notification::where('link_url', 'like', "%/{$id}%")->delete();

        $report->delete();

        // 4. Recalculate TOPSIS rankings
        $this->topsisService->calculateAll();

        AuditLog::record(
            activity: "Hapus Laporan #{$ticket}",
            targetType: 'Report',
            targetId: $id,
            description: "Admin {$admin->name} menghapus laporan #{$ticket} beserta seluruh file dan relasi datanya.",
            userId: $admin->id
        );

        return redirect()->route('admin.reports.index')->with('success', "Laporan #{$ticket} beserta seluruh data & file foto berhasil dihapus permanen dari database.");
    }

    public function deleteReportPhoto($photoId)
    {
        // 41, 42, 43, 44. ALUR HAPUS FOTO LAPORAN
        $admin = Auth::user();
        $photo = ReportPhoto::findOrFail($photoId);

        $deleted = $this->storageService->deleteReportPhoto($photo, $admin->id);

        if ($deleted) {
            return back()->with('success', 'Foto laporan berhasil dihapus dari penyimpanan dan database.');
        }

        return back()->with('error', 'Foto gagal dihapus dari Storage. Silakan coba lagi.');
    }

    public function deleteProgressPhoto($photoId)
    {
        // 45. HAPUS FOTO PROGRESS
        $admin = Auth::user();
        $photo = ProgressPhoto::findOrFail($photoId);

        $deleted = $this->storageService->deleteProgressPhoto($photo, $admin->id);

        if ($deleted) {
            return back()->with('success', 'Foto progres berhasil dihapus.');
        }

        return back()->with('error', 'Foto progres gagal dihapus dari Storage.');
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('activity', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('target_type', 'like', "%{$q}%");
            });
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        return view('admin.audit-logs', compact('logs'));
    }
}
