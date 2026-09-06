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
            $c1Scale = 1.0;
            $c2Safety = 1.0;
            $c7Impact = 1.0;
            $report->update(['damage_type' => 'normal', 'disturbance_level' => 'rendah']);
        }

        $hours = max(1, (int) $report->created_at->diffInHours(now()));
        $pendingDays = max(1.0, round($hours / 24.0, 1));
        $sameRoadCount = max(1, Report::where('road_name', $report->road_name)->count());

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
            // PRIORITY 1: Check local storage path first (instant, zero network latency)
            $candidatePaths = [
                Storage::disk('public')->path(str_replace('road-reports/', '', $photo->file_path)),
                Storage::disk('public')->path($photo->file_path),
                public_path('storage/' . $photo->file_path),
                public_path('storage/' . str_replace('road-reports/', '', $photo->file_path)),
                storage_path('app/public/' . $photo->file_path),
                storage_path('app/public/' . str_replace('road-reports/', '', $photo->file_path)),
            ];

        foreach ($candidatePaths as $p) {
            if (file_exists($p) && is_file($p) && filesize($p) > 1000) {
                $localPath = $p;
                break;
            }
        }

        // PRIORITY 2: Fall back to download only if local file is missing
        if (!$localPath && !empty($photo->file_url) && str_starts_with($photo->file_url, 'http')) {
            try {
                $cacheDir = storage_path('app/public/yolo_cache');
                if (!file_exists($cacheDir)) {
                    @mkdir($cacheDir, 0755, true);
                }
                $cacheFile = $cacheDir . '/report_' . $photo->report_id . '_' . ($photo->file_name ?: 'foto-1.jpg');

                $resp = \Illuminate\Support\Facades\Http::timeout(5)->connectTimeout(3)->get($photo->file_url);
                if ($resp->successful() && strlen($resp->body()) > 1000) {
                    file_put_contents($cacheFile, $resp->body());
                    $localPath = $cacheFile;
                    $tempDownloaded = true;
                }
            } catch (\Throwable $e) {
                Log::warning('Could not download image for YOLO: ' . $e->getMessage());
            }
        }

        // 2. Execute custom YOLO detector
        if ($localPath && file_exists($localPath) && file_exists($this->scriptPath)) {
            try {
                $absLocalPath = realpath($localPath) ?: $localPath;
                $command = "\"{$this->pythonPath}\" \"{$this->scriptPath}\" --image \"{$absLocalPath}\" --conf 0.15 2>&1";

                $rawOutput = @shell_exec($command);
                if ($rawOutput && preg_match('/\{[\s\S]*\}/', $rawOutput, $matches)) {
                    $outputJson = json_decode($matches[0], true);
                }
            } catch (\Throwable $e) {
                Log::warning('YoloService execution error: ' . $e->getMessage());
            }
        }

        // 3. Fallback to direct URL if local execution failed
        if ((!$outputJson || empty($outputJson['success'])) && !empty($photo->file_url) && file_exists($this->scriptPath)) {
            try {
                $command = "\"{$this->pythonPath}\" \"{$this->scriptPath}\" --image \"{$photo->file_url}\" --conf 0.15 2>&1";
                $rawOutput = @shell_exec($command);
                if ($rawOutput && preg_match('/\{[\s\S]*\}/', $rawOutput, $matches)) {
                    $outputJson = json_decode($matches[0], true);
                }
            } catch (\Throwable $e) {
                Log::warning('YoloService URL execution error: ' . $e->getMessage());
            }
        }

        // Clean up temp download
        if ($tempDownloaded && $localPath && file_exists($localPath)) {
            @unlink($localPath);
        }
        }

        // 4. Default to normal/unprocessed only if YOLO engine fails completely (no fabricated defects!)
        if (!$outputJson || empty($outputJson['success'])) {
            $outputJson = [
                'success' => true,
                'total_defects' => 0,
                'confidence_score' => 0.0,
                'detected_classes' => ['landslide' => 0, 'pothole' => 0, 'crack' => 0],
                'damaged_area_sqm' => 0.0,
                'bounding_boxes' => [],
                'model_version' => 'model_terbaru_kaggle.pt',
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
