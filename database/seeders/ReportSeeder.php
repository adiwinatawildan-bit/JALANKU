<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\DamageDetection;
use App\Models\Location;
use App\Models\Notification;
use App\Models\ProgressPhoto;
use App\Models\ProgressUpdate;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\ReportStatusHistory;
use App\Models\RoadAssessment;
use App\Services\TopsisService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create sample SVG/PNG placeholder files in public storage
        $this->ensureStorageFiles();

        // REPORT 1: Jalan Cikajang (Full Completed Timeline as specified in PDF points 29, 31, 38)
        $report1 = Report::create([
            'ticket_number' => 'JLK-202608-0001',
            'user_id' => 4, // Budi Santoso
            'opd_id' => 1, // Dinas Bina Marga
            'title' => 'Jalan Rusak Berlubang Parah di Ruas Utama Jalan Cikajang',
            'description' => 'Terdapat beberapa lubang besar dengan kedalaman 10-15 cm yang sangat membahayakan pengendara roda dua terutama saat malam hari dan hujan.',
            'road_name' => 'Jalan Cikajang',
            'kecamatan' => 'Kecamatan Cikajang',
            'desa' => 'Desa Cikajang',
            'damage_type' => 'pothole',
            'disturbance_level' => 'sangat_parah',
            'additional_info' => 'Dekat Puskesmas Cikajang dan sering terjadi kecelakaan motor.',
            'status' => Report::STATUS_SELESAI,
            'is_public' => true,
            'verified_by' => 2,
            'verified_at' => now()->subDays(30),
            'assigned_by' => 2,
            'assigned_at' => now()->subDays(29),
            'survey_notes' => 'Survei lapangan telah dilakukan. Ditemukan 7 titik lubang besar dan retak buaya sepanjang 45 meter.',
            'survey_at' => now()->subDays(28),
            'completed_at' => now()->subDays(2),
            'citizen_feedback' => 'Terima kasih, perbaikan jalan sangat cepat, mulus, dan rapi!',
            'citizen_rating' => 5,
            'created_at' => now()->subDays(32),
            'updated_at' => now()->subDays(2),
        ]);

        Location::create([
            'report_id' => $report1->id,
            'road_name' => 'Jalan Cikajang KM 4.5',
            'address_detail' => 'Depan Puskesmas Cikajang Makmur No. 40',
            'latitude' => -6.9350000,
            'longitude' => 107.6400000,
            'kecamatan' => 'Kecamatan Cikajang',
            'desa' => 'Desa Cikajang',
            'postal_code' => '44171',
        ]);

        // Status histories for Report 1
        $statuses = [
            ['from' => null, 'to' => Report::STATUS_DIAJUKAN, 'days' => 32, 'notes' => 'Laporan diajukan oleh masyarakat'],
            ['from' => Report::STATUS_DIAJUKAN, 'to' => Report::STATUS_DIVERIFIKASI, 'days' => 30, 'notes' => 'Laporan diverifikasi oleh Admin'],
            ['from' => Report::STATUS_DIVERIFIKASI, 'to' => Report::STATUS_DITUGASKAN, 'days' => 29, 'notes' => 'Laporan ditugaskan ke Dinas Bina Marga dan Penataan Ruang'],
            ['from' => Report::STATUS_DITUGASKAN, 'to' => Report::STATUS_SURVEI, 'days' => 28, 'notes' => 'Petugas melakukan survei teknis lapangan'],
            ['from' => Report::STATUS_SURVEI, 'to' => Report::STATUS_SEDANG_DIPERBAIKI, 'days' => 25, 'notes' => 'Pekerjaan fisik perbaikan dimulai'],
            ['from' => Report::STATUS_SEDANG_DIPERBAIKI, 'to' => Report::STATUS_SELESAI, 'days' => 2, 'notes' => 'Pekerjaan pengaspalan selesai 100% dan telah lulus uji kelayakan'],
        ];

        foreach ($statuses as $st) {
            ReportStatusHistory::create([
                'report_id' => $report1->id,
                'from_status' => $st['from'],
                'to_status' => $st['to'],
                'notes' => $st['notes'],
                'changed_by' => 2,
                'created_at' => now()->subDays($st['days']),
            ]);
        }

        // Initial photos for Report 1 (3 photos max)
        for ($i = 1; $i <= 3; $i++) {
            ReportPhoto::create([
                'report_id' => $report1->id,
                'file_name' => "foto-{$i}.jpg",
                'file_path' => "road-reports/reports/{$report1->id}/initial/foto-{$i}.jpg",
                'file_url' => asset("storage/road-reports/reports/{$report1->id}/initial/foto-{$i}.jpg"),
                'photo_type' => 'initial',
                'caption' => "Foto Kondisi Awal Ruas Kerusakan #{$i}",
                'uploaded_by' => 4,
                'created_at' => now()->subDays(32),
            ]);
        }

        // Damage detection YOLO for Report 1
        DamageDetection::create([
            'report_id' => $report1->id,
            'report_photo_id' => 1,
            'detected_classes' => ['pothole' => 7, 'crack' => 4, 'surface_damage' => 1],
            'total_defects' => 12,
            'confidence_score' => 89.20,
            'bounding_boxes' => [
                ['class' => 'pothole', 'confidence' => 92.4, 'box' => [120, 160, 320, 310]],
                ['class' => 'pothole', 'confidence' => 88.7, 'box' => [360, 190, 510, 330]],
                ['class' => 'crack', 'confidence' => 86.5, 'box' => [200, 340, 480, 410]],
            ],
            'damaged_area_sqm' => 4.25,
            'model_version' => 'YOLOv8-RoadDamage-v1.0',
        ]);

        // Road Assessment for Report 1
        RoadAssessment::create([
            'report_id' => $report1->id,
            'c1_damage_scale' => 5.0,
            'c2_user_safety' => 4.8,
            'c3_traffic_volume' => 4.5,
            'c4_report_count' => 8,
            'c5_road_function' => 4.0,
            'c6_facility_proximity' => 5.0,
            'c7_community_impact' => 4.5,
            'c8_pending_days' => 30,
            'evaluated_by' => 2,
        ]);

        // Progress Updates: Minggu 1 (20%), Minggu 2 (50%), Minggu 3 (80%), Minggu 4 (100% Selesai)
        $updates = [
            [
                'week' => 1,
                'pct' => 20.0,
                'days' => 25,
                'status' => 'SEDANG DIPERBAIKI',
                'desc' => 'Pembersihan dan persiapan lokasi, pengerukan aspal rusak, perataan pondasi agregat kelas A.',
            ],
            [
                'week' => 2,
                'pct' => 50.0,
                'days' => 18,
                'status' => 'SEDANG DIPERBAIKI',
                'desc' => 'Pemadatan base course sub-base, perbaikan drainase samping jalan, dan penyemprotan prime coat.',
            ],
            [
                'week' => 3,
                'pct' => 80.0,
                'days' => 10,
                'status' => 'SEDANG DIPERBAIKI',
                'desc' => 'Penghamparan aspal hotmix lapisan AC-BC dan pemadatan menggunakan tandem roller.',
            ],
            [
                'week' => 4,
                'pct' => 100.0,
                'days' => 2,
                'status' => 'SELESAI',
                'desc' => 'Pelapisan akhir wear-course AC-WC, pengecatan marka jalan putih-kuning, pembersihan akhir jalur.',
            ],
        ];

        foreach ($updates as $upd) {
            $prog = ProgressUpdate::create([
                'report_id' => $report1->id,
                'week_number' => $upd['week'],
                'date' => now()->subDays($upd['days'])->toDateString(),
                'status' => $upd['status'],
                'progress_percentage' => $upd['pct'],
                'description' => $upd['desc'],
                'uploaded_by' => 3, // OPD
                'created_at' => now()->subDays($upd['days']),
            ]);

            // Up to 3 photos per weekly progress
            for ($k = 1; $k <= 3; $k++) {
                ProgressPhoto::create([
                    'progress_update_id' => $prog->id,
                    'file_name' => "foto-{$k}.jpg",
                    'file_path' => "road-reports/reports/{$report1->id}/progress/week-{$upd['week']}/foto-{$k}.jpg",
                    'file_url' => asset("storage/road-reports/reports/{$report1->id}/progress/week-{$upd['week']}/foto-{$k}.jpg"),
                    'caption' => "Dokumentasi Pekerjaan Minggu {$upd['week']} - Bagian {$k}",
                    'uploaded_by' => 3,
                    'created_at' => now()->subDays($upd['days']),
                ]);
            }
        }


        // REPORT 2: Jalan Merdeka Sentral (SEDANG DIPERBAIKI - Minggu 2 50%)
        $report2 = Report::create([
            'ticket_number' => 'JLK-202608-0002',
            'user_id' => 5, // Siti
            'opd_id' => 1,
            'title' => 'Aspal Amblas dan Retak Parah Depan Pasar Induk Raya',
            'description' => 'Jalan amblas sedalam 20 cm di jalur padat angkutan logistik, rawan kendaraan muatan terguling.',
            'road_name' => 'Jalan Merdeka',
            'kecamatan' => 'Kecamatan Sentral',
            'desa' => 'Desa Sukamaju',
            'damage_type' => 'amblas',
            'disturbance_level' => 'sangat_parah',
            'additional_info' => 'Jalur utama menuju Pasar Induk Raya.',
            'status' => Report::STATUS_SEDANG_DIPERBAIKI,
            'is_public' => true,
            'verified_by' => 2,
            'verified_at' => now()->subDays(14),
            'assigned_by' => 2,
            'assigned_at' => now()->subDays(13),
            'survey_notes' => 'Struktur tanah labil karena rembesan drainase. Perlu perkuatan cerucuk dan rigid pavement.',
            'survey_at' => now()->subDays(12),
            'created_at' => now()->subDays(15),
        ]);

        Location::create([
            'report_id' => $report2->id,
            'road_name' => 'Jalan Merdeka Sentral No. 102',
            'address_detail' => 'Depan Pintu Barat Pasar Induk Raya',
            'latitude' => -6.9142000,
            'longitude' => 107.6120000,
            'kecamatan' => 'Kecamatan Sentral',
            'desa' => 'Desa Sukamaju',
            'postal_code' => '44151',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            ReportPhoto::create([
                'report_id' => $report2->id,
                'file_name' => "foto-{$i}.jpg",
                'file_path' => "road-reports/reports/{$report2->id}/initial/foto-{$i}.jpg",
                'file_url' => asset("storage/road-reports/reports/{$report2->id}/initial/foto-{$i}.jpg"),
                'photo_type' => 'initial',
                'caption' => "Foto Kerusakan Awal Jalan Merdeka #{$i}",
                'uploaded_by' => 5,
                'created_at' => now()->subDays(15),
            ]);
        }

        DamageDetection::create([
            'report_id' => $report2->id,
            'detected_classes' => ['pothole' => 5, 'crack' => 6],
            'total_defects' => 11,
            'confidence_score' => 91.50,
            'bounding_boxes' => [],
            'damaged_area_sqm' => 6.20,
            'model_version' => 'YOLOv8-RoadDamage-v1.0',
        ]);

        RoadAssessment::create([
            'report_id' => $report2->id,
            'c1_damage_scale' => 5.0,
            'c2_user_safety' => 4.9,
            'c3_traffic_volume' => 5.0,
            'c4_report_count' => 12,
            'c5_road_function' => 4.5,
            'c6_facility_proximity' => 5.0,
            'c7_community_impact' => 5.0,
            'c8_pending_days' => 15,
            'evaluated_by' => 2,
        ]);

        // Progress for Report 2: Week 1 (25%), Week 2 (50%)
        $prog2_1 = ProgressUpdate::create([
            'report_id' => $report2->id,
            'week_number' => 1,
            'date' => now()->subDays(8)->toDateString(),
            'status' => 'SEDANG DIPERBAIKI',
            'progress_percentage' => 25.0,
            'description' => 'Pemasangan dinding penahan tanah & perbaikan saluran gorong-gorong air.',
            'uploaded_by' => 3,
        ]);
        for ($k = 1; $k <= 2; $k++) {
            ProgressPhoto::create([
                'progress_update_id' => $prog2_1->id,
                'file_name' => "foto-{$k}.jpg",
                'file_path' => "road-reports/reports/{$report2->id}/progress/week-1/foto-{$k}.jpg",
                'file_url' => asset("storage/road-reports/reports/{$report2->id}/progress/week-1/foto-{$k}.jpg"),
                'caption' => "Progress Week 1 #{$k}",
                'uploaded_by' => 3,
            ]);
        }

        $prog2_2 = ProgressUpdate::create([
            'report_id' => $report2->id,
            'week_number' => 2,
            'date' => now()->subDays(1)->toDateString(),
            'status' => 'SEDANG DIPERBAIKI',
            'progress_percentage' => 50.0,
            'description' => 'Pengecoran beton lean concrete dasar dan pemasangan pembesian plat jalan.',
            'uploaded_by' => 3,
        ]);
        for ($k = 1; $k <= 3; $k++) {
            ProgressPhoto::create([
                'progress_update_id' => $prog2_2->id,
                'file_name' => "foto-{$k}.jpg",
                'file_path' => "road-reports/reports/{$report2->id}/progress/week-2/foto-{$k}.jpg",
                'file_url' => asset("storage/road-reports/reports/{$report2->id}/progress/week-2/foto-{$k}.jpg"),
                'caption' => "Progress Week 2 #{$k}",
                'uploaded_by' => 3,
            ]);
        }


        // REPORT 3: Jalan Pemuda Timur (DIVERIFIKASI - Menunggu Penugasan)
        $report3 = Report::create([
            'ticket_number' => 'JLK-202608-0003',
            'user_id' => 6, // Ahmad
            'opd_id' => null,
            'title' => 'Retak Memanjang & Lubang Jalan Dekat SMAN 1',
            'description' => 'Retakan jalan cukup lebar di tikungan dekat gerbang sekolah, sering membahayakan siswa bersepeda motor.',
            'road_name' => 'Jalan Pemuda Timur',
            'kecamatan' => 'Kecamatan Sentral',
            'desa' => 'Desa Mekarjaya',
            'damage_type' => 'retak',
            'disturbance_level' => 'tinggi',
            'additional_info' => 'Kawasan padat pelajar jam berangkat & pulang sekolah.',
            'status' => Report::STATUS_DIVERIFIKASI,
            'is_public' => true,
            'verified_by' => 2,
            'verified_at' => now()->subDays(3),
            'created_at' => now()->subDays(5),
        ]);

        Location::create([
            'report_id' => $report3->id,
            'road_name' => 'Jalan Pemuda Timur No. 88',
            'address_detail' => '50 meter sebelum SMAN 1 Unggulan',
            'latitude' => -6.9205000,
            'longitude' => 107.6250000,
            'kecamatan' => 'Kecamatan Sentral',
            'desa' => 'Desa Mekarjaya',
            'postal_code' => '44152',
        ]);

        for ($i = 1; $i <= 2; $i++) {
            ReportPhoto::create([
                'report_id' => $report3->id,
                'file_name' => "foto-{$i}.jpg",
                'file_path' => "road-reports/reports/{$report3->id}/initial/foto-{$i}.jpg",
                'file_url' => asset("storage/road-reports/reports/{$report3->id}/initial/foto-{$i}.jpg"),
                'photo_type' => 'initial',
                'caption' => "Foto Kondisi Awal Jalan Pemuda Timur #{$i}",
                'uploaded_by' => 6,
                'created_at' => now()->subDays(5),
            ]);
        }

        DamageDetection::create([
            'report_id' => $report3->id,
            'detected_classes' => ['crack' => 8, 'pothole' => 2],
            'total_defects' => 10,
            'confidence_score' => 88.40,
            'bounding_boxes' => [],
            'damaged_area_sqm' => 3.10,
            'model_version' => 'YOLOv8-RoadDamage-v1.0',
        ]);

        RoadAssessment::create([
            'report_id' => $report3->id,
            'c1_damage_scale' => 4.0,
            'c2_user_safety' => 4.6,
            'c3_traffic_volume' => 4.0,
            'c4_report_count' => 5,
            'c5_road_function' => 3.5,
            'c6_facility_proximity' => 5.0, // Sangat dekat SMAN 1
            'c7_community_impact' => 4.0,
            'c8_pending_days' => 5,
            'evaluated_by' => 2,
        ]);


        // REPORT 4: Jalan Raya Timur Terminal (DIAJUKAN - Baru Masuk)
        $report4 = Report::create([
            'ticket_number' => 'JLK-202608-0004',
            'user_id' => 4,
            'opd_id' => null,
            'title' => 'Jalan Bergelombang dan Drainase Tersumbat Arah Terminal',
            'description' => 'Air meluap mengikis aspal jalan sehingga bergelombang setinggi 10 cm.',
            'road_name' => 'Jalan Raya Timur',
            'kecamatan' => 'Kecamatan Timur',
            'desa' => 'Desa Harapan Baru',
            'damage_type' => 'bergelombang',
            'disturbance_level' => 'sedang',
            'additional_info' => 'Arah Terminal Angkutan Terpadu.',
            'status' => Report::STATUS_DIAJUKAN,
            'is_public' => true,
            'created_at' => now()->subHours(6),
        ]);

        Location::create([
            'report_id' => $report4->id,
            'road_name' => 'Jalan Raya Timur KM 2',
            'address_detail' => 'Kawasan Simpang Terminal Timur',
            'latitude' => -6.9280000,
            'longitude' => 107.6320000,
            'kecamatan' => 'Kecamatan Timur',
            'desa' => 'Desa Harapan Baru',
            'postal_code' => '44161',
        ]);

        ReportPhoto::create([
            'report_id' => $report4->id,
            'file_name' => 'foto-1.jpg',
            'file_path' => "road-reports/reports/{$report4->id}/initial/foto-1.jpg",
            'file_url' => asset("storage/road-reports/reports/{$report4->id}/initial/foto-1.jpg"),
            'photo_type' => 'initial',
            'caption' => 'Foto Kondisi Awal Jalan Raya Timur',
            'uploaded_by' => 4,
            'created_at' => now()->subHours(6),
        ]);

        // Run TOPSIS calculation on all reports
        app(TopsisService::class)->calculateAll();

        // Seed Sample Notifications
        Notification::create([
            'user_id' => 4,
            'type' => 'success',
            'title' => 'Perbaikan Jalan Selesai!',
            'message' => 'Laporan #JLK-202608-0001 di Jalan Cikajang telah selesai diperbaiki 100%. Silakan beri ulasan.',
            'link_url' => '/laporan-publik/' . $report1->id,
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => 2, // Admin
            'type' => 'warning',
            'title' => 'Laporan Baru Menunggu Verifikasi',
            'message' => 'Laporan #JLK-202608-0004 baru saja diajukan dan membutuhkan verifikasi.',
            'link_url' => '/admin/verifikasi',
            'is_read' => false,
        ]);

        AuditLog::record(
            activity: 'Inisialisasi Master Data & Seeding Awal Sistem',
            targetType: 'System',
            targetId: 'INIT',
            description: 'Seeding database pengaduan JALAN KU berhasil dilakukan dengan data simulasi realistis.',
            userId: 1
        );
    }

    /**
     * Create placeholder images in storage for instant UI beauty
     */
    protected function ensureStorageFiles(): void
    {
        $base = storage_path('app/public/road-reports');
        if (!File::exists($base)) {
            File::makeDirectory($base, 0777, true, true);
        }

        // SVG template for professional road imagery
        $createSvg = function($title, $sub, $accentColor = '#f59e0b') {
            return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="800" height="600">
  <defs>
    <linearGradient id="sky" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#1e293b" />
      <stop offset="100%" stop-color="#0f172a" />
    </linearGradient>
    <linearGradient id="road" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#334155" />
      <stop offset="100%" stop-color="#1e293b" />
    </linearGradient>
  </defs>
  <rect width="800" height="600" fill="url(#sky)" />
  <polygon points="100,600 700,600 480,220 320,220" fill="url(#road)" />
  <line x1="400" y1="220" x2="400" y2="600" stroke="{$accentColor}" stroke-width="8" stroke-dasharray="30,25" />
  <rect x="250" y="320" width="140" height="70" rx="12" fill="rgba(239, 68, 68, 0.4)" stroke="#ef4444" stroke-width="3" stroke-dasharray="6,4" />
  <text x="320" y="360" fill="#fecaca" font-family="sans-serif" font-size="14" font-weight="bold" text-anchor="middle">Pothole Defect #1</text>
  <rect x="420" y="400" width="160" height="80" rx="12" fill="rgba(249, 115, 22, 0.4)" stroke="#f97316" stroke-width="3" stroke-dasharray="6,4" />
  <text x="500" y="445" fill="#ffedd5" font-family="sans-serif" font-size="14" font-weight="bold" text-anchor="middle">Crack Defect #2</text>
  <rect x="40" y="40" width="720" height="100" rx="16" fill="rgba(15, 23, 42, 0.85)" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" />
  <text x="70" y="80" fill="#f8fafc" font-family="sans-serif" font-size="22" font-weight="bold">{$title}</text>
  <text x="70" y="112" fill="#94a3b8" font-family="sans-serif" font-size="15">{$sub} • JALAN KU Infrastructure Monitoring</text>
</svg>
SVG;
        };

        // Create sample placeholder files
        $dirs = [
            'reports/1/initial', 'reports/1/progress/week-1', 'reports/1/progress/week-2', 'reports/1/progress/week-3', 'reports/1/progress/week-4',
            'reports/2/initial', 'reports/2/progress/week-1', 'reports/2/progress/week-2',
            'reports/3/initial', 'reports/4/initial'
        ];

        foreach ($dirs as $d) {
            $dirPath = storage_path("app/public/road-reports/{$d}");
            if (!File::exists($dirPath)) {
                File::makeDirectory($dirPath, 0777, true, true);
            }
            for ($k = 1; $k <= 3; $k++) {
                $filePath = "{$dirPath}/foto-{$k}.jpg";
                if (!File::exists($filePath)) {
                    $svg = $createSvg("Dokumentasi Kerusakan & Perbaikan", "Lokasi: {$d} Foto {$k}");
                    File::put($filePath, $svg);
                }
            }
        }
    }
}
