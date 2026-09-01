<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ProgressPhoto;
use App\Models\ProgressUpdate;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\ReportStatusHistory;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpdController extends Controller
{
    protected StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $opdId = $user->opd_id;

        // If user is super_admin or admin without specific opd, allow viewing first OPD or all assigned
        $query = Report::query();
        if ($opdId) {
            $query->where('opd_id', $opdId);
        } else {
            $query->whereNotNull('opd_id');
        }

        $allReports = (clone $query)->with(['location', 'priorityResult', 'progressUpdates'])->latest()->get();

        // 26. DASHBOARD OPD METRICS
        $stats = [
            'ditugaskan' => $allReports->where('status', Report::STATUS_DITUGASKAN)->count(),
            'survei' => $allReports->where('status', Report::STATUS_SURVEI)->count(),
            'diperbaiki' => $allReports->where('status', Report::STATUS_SEDANG_DIPERBAIKI)->count(),
            'selesai' => $allReports->where('status', Report::STATUS_SELESAI)->count(),
            'terlambat' => $allReports->whereIn('status', [Report::STATUS_DITUGASKAN, Report::STATUS_SURVEI, Report::STATUS_SEDANG_DIPERBAIKI])
                ->where('updated_at', '<', now()->subDays(14))
                ->count(),
        ];

        $tasks = $allReports->whereNotIn('status', [Report::STATUS_DITOLAK, Report::STATUS_DUPLIKAT])->take(15);

        return view('opd.dashboard', compact('stats', 'tasks', 'user'));
    }

    public function tasks(Request $request)
    {
        $user = Auth::user();
        $query = Report::with(['location', 'photos', 'priorityResult', 'progressUpdates.photos'])
            ->whereNotIn('status', [Report::STATUS_DITOLAK, Report::STATUS_DUPLIKAT]);

        if ($user->opd_id) {
            $query->where('opd_id', $user->opd_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('ticket_number', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%")
                    ->orWhere('road_name', 'like', "%{$q}%");
            });
        }

        $tasks = $query->latest()->paginate(12)->withQueryString();

        return view('opd.tasks.index', compact('tasks'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $query = Report::with([
            'location',
            'initialPhotos',
            'surveyPhotos',
            'progressUpdates.photos',
            'statusHistories.changer',
            'priorityResult',
            'opd'
        ]);

        if ($user->opd_id && !$user->isAdmin() && !$user->isSuperAdmin()) {
            $query->where('opd_id', $user->opd_id);
        }

        $report = $query->findOrFail($id);
        $nextWeekNumber = ($report->progressUpdates()->max('week_number') ?? 0) + 1;

        return view('opd.tasks.show', compact('report', 'nextWeekNumber'));
    }

    public function startSurvey(Request $request, $id)
    {
        $user = Auth::user();
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'survey_notes' => ['required', 'string'],
            'survey_photos' => ['nullable', 'array', 'max:3'],
            'survey_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'survey_photos.max' => 'Maksimal 3 foto untuk dokumentasi survei.',
        ]);

        return DB::transaction(function () use ($report, $validated, $request, $user) {
            $oldStatus = $report->status;
            $report->update([
                'status' => Report::STATUS_SURVEI,
                'survey_notes' => $validated['survey_notes'],
                'survey_at' => now(),
            ]);

            ReportStatusHistory::create([
                'report_id' => $report->id,
                'from_status' => $oldStatus,
                'to_status' => Report::STATUS_SURVEI,
                'notes' => 'Survei lapangan dilaksanakan: ' . $validated['survey_notes'],
                'changed_by' => $user->id,
            ]);

            if ($request->hasFile('survey_photos')) {
                $files = $request->file('survey_photos');
                $index = 1;
                foreach (array_slice($files, 0, 3) as $file) {
                    $stored = $this->storageService->uploadSurveyPhoto($file, $report->id, $index);
                    ReportPhoto::create([
                        'report_id' => $report->id,
                        'file_name' => $stored['file_name'],
                        'file_path' => $stored['file_path'],
                        'file_url' => $stored['file_url'],
                        'photo_type' => 'survey',
                        'caption' => "Foto Hasil Survei Lapangan #{$index}",
                        'uploaded_by' => $user->id,
                    ]);
                    $index++;
                }
            }

            // Notify citizen
            Notification::create([
                'user_id' => $report->user_id,
                'type' => 'info',
                'title' => 'Survei Lapangan Selesai Dilakukan',
                'message' => "Petugas OPD telah menyelesaikan survei teknis di lokasi {$report->road_name}.",
                'link_url' => route('masyarakat.reports.show', $report->id),
            ]);

            AuditLog::record(
                activity: 'Survei Lapangan Laporan #' . $report->ticket_number,
                targetType: 'Report',
                targetId: $report->id,
                description: "Petugas {$user->name} mencatat hasil survei teknis.",
                userId: $user->id
            );

            return back()->with('success', 'Hasil survei teknis berhasil disimpan.');
        });
    }

    public function storeProgress(Request $request, $id)
    {
        // 29, 30, 33, 36. FOTO DAN PROGRES MINGGUAN (Maksimal 3 Foto per minggu)
        $user = Auth::user();
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'week_number' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
            'progress_percentage' => ['required', 'numeric', 'between:1,100'],
            'description' => ['required', 'string'],
            'photos' => ['required', 'array', 'min:1', 'max:3'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'photos.max' => 'Maksimal 3 foto untuk setiap update progres mingguan.',
            'photos.*.max' => 'Ukuran setiap foto tidak boleh lebih dari 5 MB.',
        ]);

        return DB::transaction(function () use ($report, $validated, $request, $user) {
            $isComplete = (float) $validated['progress_percentage'] >= 100.0;
            $newStatus = $isComplete ? Report::STATUS_SELESAI : Report::STATUS_SEDANG_DIPERBAIKI;
            $oldStatus = $report->status;

            // Create progress update
            $progressUpdate = ProgressUpdate::create([
                'report_id' => $report->id,
                'week_number' => $validated['week_number'],
                'date' => $validated['date'],
                'status' => $newStatus,
                'progress_percentage' => $validated['progress_percentage'],
                'description' => $validated['description'],
                'uploaded_by' => $user->id,
            ]);

            // Upload up to 3 photos
            if ($request->hasFile('photos')) {
                $files = $request->file('photos');
                $index = 1;
                foreach (array_slice($files, 0, 3) as $file) {
                    $stored = $this->storageService->uploadProgressPhoto(
                        $file,
                        $report->id,
                        (int) $validated['week_number'],
                        $index
                    );

                    ProgressPhoto::create([
                        'progress_update_id' => $progressUpdate->id,
                        'file_name' => $stored['file_name'],
                        'file_path' => $stored['file_path'],
                        'file_url' => $stored['file_url'],
                        'caption' => "Dokumentasi Minggu {$validated['week_number']} - Foto {$index}",
                        'uploaded_by' => $user->id,
                    ]);
                    $index++;
                }
            }

            // Update Report Status
            $report->update([
                'status' => $newStatus,
                'completed_at' => $isComplete ? now() : $report->completed_at,
            ]);

            ReportStatusHistory::create([
                'report_id' => $report->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'notes' => "Update Progress Minggu {$validated['week_number']} ({$validated['progress_percentage']}%): {$validated['description']}",
                'changed_by' => $user->id,
            ]);

            // Notify citizen
            Notification::create([
                'user_id' => $report->user_id,
                'type' => $isComplete ? 'success' : 'info',
                'title' => $isComplete ? 'Perbaikan Jalan Telah Selesai!' : "Progres Minggu {$validated['week_number']} ({$validated['progress_percentage']}%)",
                'message' => "Update pekerjaan perbaikan di {$report->road_name}: {$validated['description']}",
                'link_url' => route('masyarakat.reports.show', $report->id),
            ]);

            AuditLog::record(
                activity: "Update Progress Minggu {$validated['week_number']}",
                targetType: 'ProgressUpdate',
                targetId: $progressUpdate->id,
                description: "Petugas {$user->name} memperbarui progres laporan #{$report->ticket_number} menjadi {$validated['progress_percentage']}%.",
                userId: $user->id
            );

            $msg = $isComplete
                ? "Perbaikan laporan #{$report->ticket_number} telah mencapai 100% dan berstatus SELESAI!"
                : "Update progres Minggu {$validated['week_number']} ({$validated['progress_percentage']}%) berhasil disimpan.";

            return back()->with('success', $msg);
        });
    }

    public function deleteProgressPhoto($photoId)
    {
        $user = Auth::user();
        $photo = ProgressPhoto::findOrFail($photoId);

        $deleted = $this->storageService->deleteProgressPhoto($photo, $user->id);

        if ($deleted) {
            return back()->with('success', 'Foto dokumentasi progres berhasil dihapus.');
        }

        return back()->with('error', 'Gagal menghapus foto dari storage.');
    }
}
