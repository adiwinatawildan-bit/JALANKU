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
                Report::STATUS_DIAJUKAN,
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
            'damage_type' => ['required', 'string', 'in:pothole,crack,landslide,retak,amblas,bergelombang,drainase,lainnya'],
            'disturbance_level' => ['required', 'string', 'in:rendah,sedang,tinggi,sangat_parah'],
            'additional_info' => ['nullable', 'string'],
            'photos' => ['required', 'array', 'min:1', 'max:3'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'photos.max' => 'Maksimal 3 foto untuk setiap laporan.',
            'photos.*.max' => 'Ukuran foto tidak boleh melebihi 5 MB.',
            'photos.*.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $user = Auth::user();
            $ticketNumber = 'JLK-' . date('Ym') . '-' . strtoupper(Str::random(5));

            // Create Report
            $report = Report::create([
                'ticket_number' => $ticketNumber,
                'user_id' => $user->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'road_name' => $validated['road_name'],
                'kecamatan' => $validated['kecamatan'],
                'desa' => $validated['desa'],
                'damage_type' => $validated['damage_type'],
                'disturbance_level' => $validated['disturbance_level'],
                'additional_info' => $validated['additional_info'] ?? null,
                'status' => Report::STATUS_DIAJUKAN,
                'is_public' => true,
            ]);

            // Create Location
            Location::create([
                'report_id' => $report->id,
                'road_name' => $validated['road_name'],
                'address_detail' => $validated['address_detail'] ?? null,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'kecamatan' => $validated['kecamatan'],
                'desa' => $validated['desa'],
            ]);

            // Status History Initial
            ReportStatusHistory::create([
                'report_id' => $report->id,
                'from_status' => null,
                'to_status' => Report::STATUS_DIAJUKAN,
                'notes' => 'Laporan diajukan oleh masyarakat via portal web.',
                'changed_by' => $user->id,
            ]);

            // Upload up to 3 Photos to Supabase/Local Storage
            if ($request->hasFile('photos')) {
                $files = $request->file('photos');
                $index = 1;
                foreach (array_slice($files, 0, 3) as $file) {
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
                }
            }

            // Run YOLO AI Analysis & TOPSIS Calculation
            $this->yoloService->analyzeReport($report, $user->id);
            $this->topsisService->calculateAll();

            // Notify user
            Notification::create([
                'user_id' => $user->id,
                'type' => 'success',
                'title' => 'Laporan Berhasil Diajukan',
                'message' => "Laporan Anda (#{$ticketNumber}) di {$report->road_name} telah diterima dan sedang menunggu verifikasi admin.",
                'link_url' => route('masyarakat.reports.show', $report->id),
            ]);

            // Notify all admins
            $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'info',
                    'title' => 'Laporan Baru Masuk',
                    'message' => "Laporan baru #{$ticketNumber} di {$report->road_name} membutuhkan verifikasi.",
                    'link_url' => route('admin.reports.show', $report->id),
                ]);
            }

            AuditLog::record(
                activity: "Pengaduan Baru #{$ticketNumber}",
                targetType: 'Report',
                targetId: $report->id,
                description: "Masyarakat {$user->name} membuat laporan kerusakan jalan di {$report->road_name}.",
                userId: $user->id
            );

            return redirect()->route('masyarakat.reports.show', $report->id)
                ->with('success', "Laporan Anda dengan nomor tiket {$ticketNumber} berhasil dikirim dan sedang diproses!");
        });
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
            'priorityResult'
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
