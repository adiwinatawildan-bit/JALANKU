<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Opd;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function index()
    {
        // 13. STATISTIK LANDING PAGE (Real-time from MySQL)
        $stats = [
            'total_pengaduan' => Report::count(),
            'sedang_diproses' => Report::whereIn('status', [
                Report::STATUS_DIAJUKAN,
                Report::STATUS_DIVERIFIKASI,
                Report::STATUS_DITUGASKAN,
                Report::STATUS_SURVEI,
                Report::STATUS_MENUNGGU_PERBAIKAN
            ])->count(),
            'sedang_diperbaiki' => Report::where('status', Report::STATUS_SEDANG_DIPERBAIKI)->count(),
            'selesai' => Report::where('status', Report::STATUS_SELESAI)->count(),
        ];

        // Recent reports for public feed
        $recentReports = Report::with(['location', 'photos', 'progressUpdates.photos', 'priorityResult', 'opd'])
            ->where('is_public', true)
            ->whereNotIn('status', [Report::STATUS_DITOLAK, Report::STATUS_DUPLIKAT])
            ->latest()
            ->take(6)
            ->get();

        // Active repair reports showcase
        $repairShowcase = Report::with(['location', 'photos', 'progressUpdates.photos', 'opd'])
            ->whereIn('status', [Report::STATUS_SEDANG_DIPERBAIKI, Report::STATUS_SELESAI])
            ->has('progressUpdates')
            ->latest('updated_at')
            ->take(3)
            ->get();

        // Reports for Live Feed interactive dropdown widget (Survei, Perbaikan, Selesai)
        $liveFeedReports = Report::with(['location', 'photos', 'initialPhotos', 'surveyPhotos', 'progressUpdates.photos', 'opd'])
            ->where('is_public', true)
            ->whereNotIn('status', [Report::STATUS_DITOLAK, Report::STATUS_DUPLIKAT])
            ->orderByRaw("CASE 
                WHEN status = '" . Report::STATUS_SEDANG_DIPERBAIKI . "' THEN 1 
                WHEN status = '" . Report::STATUS_SURVEI . "' THEN 2 
                WHEN status = '" . Report::STATUS_DITUGASKAN . "' THEN 3 
                WHEN status = '" . Report::STATUS_MENUNGGU_PERBAIKAN . "' THEN 4 
                WHEN status = '" . Report::STATUS_SELESAI . "' THEN 5 
                ELSE 6 END")
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('public.index', compact('stats', 'recentReports', 'repairShowcase', 'liveFeedReports'));
    }

    public function peta(Request $request)
    {
        $kecamatanList = Report::distinct()->whereNotNull('kecamatan')->pluck('kecamatan');
        $damageTypes = Report::distinct()->whereNotNull('damage_type')->pluck('damage_type');

        return view('public.peta', compact('kecamatanList', 'damageTypes'));
    }

    public function laporanPublik(Request $request)
    {
        $query = Report::with(['location', 'photos', 'progressUpdates.photos', 'priorityResult', 'opd'])
            ->where('is_public', true)
            ->whereNotIn('status', [Report::STATUS_DITOLAK, Report::STATUS_DUPLIKAT]);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('road_name', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        if ($request->filled('damage_type')) {
            $query->where('damage_type', $request->damage_type);
        }

        $reports = $query->latest()->paginate(9)->withQueryString();
        $kecamatanList = Report::distinct()->whereNotNull('kecamatan')->pluck('kecamatan');

        return view('public.laporan-publik', compact('reports', 'kecamatanList'));
    }

    public function detailLaporanPublik($id)
    {
        // 31 & 32. HALAMAN DETAIL LAPORAN PUBLIK & TRANSPARANSI
        $report = Report::with([
            'location',
            'initialPhotos',
            'surveyPhotos',
            'progressUpdates.photos',
            'statusHistories.changer',
            'opd',
            'priorityResult',
        ])->where('is_public', true)->findOrFail($id);

        return view('public.detail-laporan', compact('report'));
    }

    public function statistik()
    {
        $totalReports = Report::count();
        $statusCounts = Report::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $damageTypeCounts = Report::select('damage_type', DB::raw('count(*) as count'))
            ->groupBy('damage_type')
            ->pluck('count', 'damage_type')
            ->toArray();

        $monthlyTrends = Report::select(
                DB::raw(DB::getDriverName() === "sqlite" ? "strftime('%Y-%m', created_at) as month" : "DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(12)
            ->get();

        $opdPerformance = Opd::withCount([
            'reports as total_tasks',
            'reports as finished_tasks' => function ($q) {
                $q->where('status', Report::STATUS_SELESAI);
            },
            'reports as active_tasks' => function ($q) {
                $q->where('status', Report::STATUS_SEDANG_DIPERBAIKI);
            }
        ])->get();

        return view('public.statistik', compact(
            'totalReports',
            'statusCounts',
            'damageTypeCounts',
            'monthlyTrends',
            'opdPerformance'
        ));
    }

    public function caraKerja()
    {
        return view('public.cara-kerja');
    }

    public function tentang()
    {
        return view('public.tentang');
    }

    /**
     * API endpoint returning GeoJSON/JSON for Leaflet Map
     */
    public function apiGeoReports(Request $request)
    {
        $query = Report::with(['location', 'photos', 'priorityResult', 'opd'])
            ->where('is_public', true)
            ->whereNotIn('status', [Report::STATUS_DITOLAK, Report::STATUS_DUPLIKAT])
            ->whereHas('location');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }
        if ($request->filled('damage_type')) {
            $query->where('damage_type', $request->damage_type);
        }

        $reports = $query->get()->map(function ($r) {
            $firstPhoto = $r->photos->first()?->file_url ?? asset('images/road-placeholder.svg');
            return [
                'id' => $r->id,
                'ticket_number' => $r->ticket_number,
                'title' => $r->title,
                'road_name' => $r->road_name,
                'kecamatan' => $r->kecamatan,
                'desa' => $r->desa,
                'status' => $r->status,
                'damage_type' => $r->damage_type_label,
                'disturbance_level' => ucfirst($r->disturbance_level),
                'progress' => $r->current_progress,
                'priority_level' => $r->priorityResult?->priority_level ?? 'Normal',
                'marker_color' => $r->marker_color,
                'latitude' => (float) $r->location->latitude,
                'longitude' => (float) $r->location->longitude,
                'photo_url' => $firstPhoto,
                'opd_name' => $r->opd?->name ?? 'Belum Ditugaskan',
                'detail_url' => route('public.reports.show', $r->id),
            ];
        });

        $facilities = Facility::all()->map(function ($f) {
            return [
                'id' => $f->id,
                'name' => $f->name,
                'type' => $f->type,
                'latitude' => (float) $f->latitude,
                'longitude' => (float) $f->longitude,
            ];
        });

        return response()->json([
            'reports' => $reports,
            'facilities' => $facilities,
        ]);
    }
}

