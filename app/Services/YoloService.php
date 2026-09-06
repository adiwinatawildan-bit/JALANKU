<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\DamageDetection;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\RoadAssessment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class YoloService
{
    protected bool $enabled;
    protected string $pythonPath;
    protected string $scriptPath;

    public function __construct()
    {
        $this->enabled = (bool) (config('services.yolo.enabled') ?? env('YOLO_ENABLED', true));
        $customPython = config('services.yolo.python_path') ?: env('PYTHON_PATH');
        if ($customPython) {
            $this->pythonPath = $customPython;
        } elseif (PHP_OS_FAMILY === 'Windows') {
            $winPython = 'C:\\Users\\Adiwinata\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';
            $this->pythonPath = file_exists($winPython) ? $winPython : 'python';
        } else {
            $this->pythonPath = file_exists('/usr/bin/python3') ? '/usr/bin/python3' : 'python3';
        }

        $this->scriptPath = base_path('ai_engine/yolo_detector.py');
    }

    /**
     * Run YOLO analysis on a report's photos
     */
    public function analyzeReport(Report $report, ?int $userId = null): array
    {
        $photos = $report->initialPhotos;
        if ($photos->isEmpty()) {
            $photos = $report->photos;
        }

        if ($photos->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada foto yang dapat dianalisis.',
            ];
        }

        $allDetections = [];
        $totalPotholes = 0;
        $totalCracks = 0;
        $totalLandslides = 0;
        $totalDefects = 0;
        $confidenceSum = 0;
        $totalArea = 0.0;

        foreach ($photos as $photo) {
            $result = $this->analyzePhoto($photo, $report);
            if (!empty($result['success']) && isset($result['detection'])) {
                $allDetections[] = $result['detection'];
                $totalPotholes += $result['detection']->detected_classes['pothole'] ?? 0;
                $totalCracks += $result['detection']->detected_classes['crack'] ?? 0;
                $totalLandslides += $result['detection']->detected_classes['landslide'] ?? 0;
                $totalDefects += $result['detection']->total_defects;
                $confidenceSum += $result['detection']->confidence_score;
                $totalArea += $result['detection']->damaged_area_sqm ?? 0.0;
            }
        }

        $count = count($allDetections);
        if ($count === 0) {
            return [
                'success' => false,
                'message' => 'Model YOLO belum berhasil mendeteksi foto atau sedang diproses. Laporan tetap dalam status Menunggu Analisis AI.',
            ];
        }

        $avgConf = $count > 0 ? round($confidenceSum / $count, 1) : 0.0;
        $totalLandslides = (int) $totalLandslides;
        $totalPotholes = (int) $totalPotholes;
        $totalCracks = (int) $totalCracks;

        if ($totalLandslides > 0) {
            $c1Scale = $totalLandslides >= 2 ? 5.0 : 4.6;
            $c2Safety = $totalLandslides >= 2 ? 5.0 : 4.6;
            $c7Impact = 5.0;
            $report->update(['damage_type' => 'landslide', 'disturbance_level' => ($totalLandslides >= 2 ? 'sangat_parah' : 'tinggi')]);
        } elseif ($totalPotholes > 0) {
            $c1Scale = match (true) {
                $totalPotholes >= 6 => 4.4,
                $totalPotholes >= 3 => 4.1,
                $totalPotholes == 2 => 3.8,
                default => 3.5,
            };
            $c2Safety = match (true) {
                $totalPotholes >= 3 => 4.2,
                default => 3.7,
            };
            $c7Impact = 4.0;
            $report->update(['damage_type' => 'pothole', 'disturbance_level' => ($totalPotholes >= 3 ? 'tinggi' : 'sedang')]);
        } elseif ($totalCracks > 0) {
            $c1Scale = match (true) {
                $totalCracks >= 4 => 3.2,
                $totalCracks >= 2 => 2.8,
                default => 2.4,
            };
            $c2Safety = match (true) {
                $totalCracks >= 3 => 2.8,
                default => 2.4,
            };
            $c7Impact = 2.5;
            $report->update(['damage_type' => 'crack', 'disturbance_level' => 'sedang']);
        } else {
            // Model genuinely detected 0 defects
            $c1Scale = 1.0;
            $c2Safety = 1.0;
            $c7Impact = 1.0;
            $report->update(['damage_type' => 'normal', 'disturbance_level' => 'rendah']);
        }

        $hours = max(1, (int) $report->created_at->diffInHours(now()));
        $pendingDays = max(1.0, round($hours / 24.0, 1));
        
        $targetRoad = strtolower(trim(preg_replace('/\s+/', ' ', (string) $report->road_name)));
        $allRoads = Report::pluck('road_name')->toArray();
        $sameRoadCount = 0;
        foreach ($allRoads as $road) {
            $clean = strtolower(trim(preg_replace('/\s+/', ' ', (string) $road)));
            if ($clean === $targetRoad) {
                $sameRoadCount++;
            } else {
                similar_text($clean, $targetRoad, $percent);
                if ($percent >= 85) {
                    $sameRoadCount++;
                }
            }
        }
        $sameRoadCount = max(1, $sameRoadCount);

        RoadAssessment::updateOrCreate(
            ['report_id' => $report->id],
            [
                'c1_damage_scale' => $c1Scale,
                'c2_user_safety' => $c2Safety,
                'c4_report_count' => min(10.0, (float) $sameRoadCount),
                'c7_community_impact' => $c7Impact,
                'c8_pending_days' => $pendingDays,
            ]
        );

        // Recalculate TOPSIS priorities
        try {
            app(TopsisService::class)->calculateAll();
        } catch (\Throwable $e) {
            Log::warning('Topsis recalculation notice: ' . $e->getMessage());
        }

        AuditLog::record(
            activity: 'Analisis AI YOLO pada laporan #' . $report->ticket_number,
            targetType: 'Report',
            targetId: $report->id,
            description: "Hasil YOLO model_terbaru_kaggle.pt: {$totalPotholes} pothole, {$totalCracks} crack, {$totalLandslides} landslide, total {$totalDefects} cacat. Confidence: {$avgConf}%.",
            userId: $userId
        );

        return [
            'success' => true,
            'report_id' => $report->id,
            'total_defects' => $totalDefects,
            'potholes' => $totalPotholes,
            'cracks' => $totalCracks,
            'landslides' => $totalLandslides,
            'confidence' => $avgConf,
            'damaged_area_sqm' => round($totalArea, 2),
            'detections' => $allDetections,
        ];
    }

    /**
     * Run analysis on a single ReportPhoto using model_terbaru_kaggle.pt
     */
    public function analyzePhoto(ReportPhoto $photo, ?Report $report = null): array
    {
        $outputJson = null;
        $report = $report ?? $photo->report ?? Report::find($photo->report_id);

        $localPath = null;
        $tempDownloaded = false;

        if ($this->enabled) {
            $imageTarget = null;
            $tempDownloaded = false;

            $candidatePaths = [
                Storage::disk('public')->path(str_replace('road-reports/', '', $photo->file_path)),
                Storage::disk('public')->path($photo->file_path),
                public_path('storage/' . $photo->file_path),
                public_path('storage/' . str_replace('road-reports/', '', $photo->file_path)),
                storage_path('app/public/' . $photo->file_path),
                storage_path('app/public/' . str_replace('road-reports/', '', $photo->file_path)),
            ];

            // PRIORITY 1: If a freshly uploaded local copy exists (within last 10 minutes), use it directly (instant zero network latency)
            foreach ($candidatePaths as $p) {
                if (file_exists($p) && is_file($p) && filesize($p) > 1000 && (time() - filemtime($p)) < 600) {
                    $imageTarget = $p;
                    break;
                }
            }

            // PRIORITY 2: Otherwise, fetch canonical photo from Supabase storage and keep in cache
            if (!$imageTarget && !empty($photo->file_url) && str_starts_with($photo->file_url, 'http')) {
                try {
                    $cacheDir = storage_path('app/public/yolo_cache');
                    if (!file_exists($cacheDir)) {
                        @mkdir($cacheDir, 0755, true);
                    }
                    $cacheFile = $cacheDir . '/report_' . $photo->report_id . '_' . $photo->id . '_' . ($photo->file_name ?: 'foto-1.jpg');

                    if (file_exists($cacheFile) && filesize($cacheFile) > 1000) {
                        $imageTarget = $cacheFile;
                    } else {
                        $ch = curl_init($photo->file_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
                        $data = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);

                        if ($httpCode === 200 && strlen($data) > 500) {
                            file_put_contents($cacheFile, $data);
                            $imageTarget = $cacheFile;
                        } else {
                            $imageTarget = $photo->file_url;
                        }
                    }
                } catch (\Throwable $e) {
                    $imageTarget = $photo->file_url;
                }
            }

            // PRIORITY 3: Fallback to existing local files
            if (!$imageTarget) {
                foreach ($candidatePaths as $p) {
                    if (file_exists($p) && is_file($p) && filesize($p) > 1000) {
                        $imageTarget = $p;
                        break;
                    }
                }
            }

            // Execute custom YOLO detector with generous timeout (60s)
            if ($imageTarget && file_exists($this->scriptPath)) {
                try {
                    $process = Process::timeout(60)->run([
                        $this->pythonPath,
                        $this->scriptPath,
                        '--image',
                        (string) $imageTarget,
                        '--conf',
                        '0.15'
                    ]);
                    $rawOutput = $process->output();
                    if ($rawOutput && preg_match('/\{[\s\S]*\}/', $rawOutput, $matches)) {
                        $outputJson = json_decode($matches[0], true);
                    }
                } catch (\Throwable $e) {
                    Log::warning('YoloService execution timeout/notice: ' . $e->getMessage());
                }
            }
        }

        // 4. If YOLO engine failed completely, do NOT fabricate 0-defect detection!
        if (!$outputJson || empty($outputJson['success'])) {
            return [
                'success' => false,
                'message' => 'YOLO engine belum berhasil menganalisis foto: ' . ($outputJson['error'] ?? 'timeout atau respon kosong'),
            ];
        }

        $detection = DamageDetection::updateOrCreate(
            [
                'report_id' => $photo->report_id,
                'report_photo_id' => $photo->id,
            ],
            [
                'detected_classes' => $outputJson['detected_classes'] ?? ['pothole' => 0, 'crack' => 0, 'landslide' => 0],
                'total_defects' => $outputJson['total_defects'] ?? 0,
                'confidence_score' => $outputJson['confidence_score'] ?? 0.0,
                'bounding_boxes' => $outputJson['bounding_boxes'] ?? [],
                'damaged_area_sqm' => $outputJson['damaged_area_sqm'] ?? 0.0,
                'model_version' => $outputJson['model_version'] ?? 'model_terbaru_kaggle.pt',
            ]
        );

        return [
            'success' => true,
            'detection' => $detection,
        ];
    }
}
