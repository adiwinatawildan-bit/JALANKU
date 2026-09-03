<?php

namespace App\Services;

use App\Models\PriorityCriterion;
use App\Models\PriorityResult;
use App\Models\Report;
use App\Models\RoadAssessment;
use Illuminate\Support\Collection;

class TopsisService
{
    /**
     * Calculate TOPSIS rankings for all active/verified reports or a specific collection of reports.
     *
     * @param Collection|null $reports
     * @return Collection
     */
    public function calculateAll(?Collection $reports = null): Collection
    {
        $criteria = PriorityCriterion::where('is_active', true)->orderBy('code')->get();
        if ($criteria->isEmpty()) {
            return collect();
        }

        // Get target reports (DIAJUKAN, DIVERIFIKASI, DITUGASKAN, SURVEI, MENUNGGU PERBAIKAN, SEDANG DIPERBAIKI)
        if ($reports === null) {
            $reports = Report::with(['assessment', 'location', 'photos'])
                ->whereNotIn('status', [Report::STATUS_DITOLAK, Report::STATUS_DUPLIKAT])
                ->get();
        }

        if ($reports->isEmpty()) {
            return collect();
        }

        // Ensure every report has a RoadAssessment
        foreach ($reports as $report) {
            $this->ensureAssessmentExists($report);
        }
        $reports->load('assessment');

        // Total criteria weights normalization to sum = 1.0
        $totalWeight = $criteria->sum('weight_percentage') ?: 100;
        $weights = [];
        $criteriaTypes = [];
        foreach ($criteria as $criterion) {
            $weights[$criterion->code] = (float) $criterion->weight_percentage / $totalWeight;
            $criteriaTypes[$criterion->code] = $criterion->type; // 'benefit' or 'cost'
        }

        // Standard criteria mapping to RoadAssessment columns
        $codeMap = [
            'C1' => 'c1_damage_scale',
            'C2' => 'c2_user_safety',
            'C3' => 'c3_traffic_volume',
            'C4' => 'c4_report_count',
            'C5' => 'c5_road_function',
            'C6' => 'c6_facility_proximity',
            'C7' => 'c7_community_impact',
            'C8' => 'c8_pending_days',
        ];

        // 1. Build Decision Matrix X
        $matrix = [];
        foreach ($reports as $report) {
            $assess = $report->assessment;
            $row = [];
            foreach ($criteria as $criterion) {
                $code = $criterion->code;
                $col = $codeMap[$code] ?? null;
                if ($col && isset($assess->$col)) {
                    $val = (float) $assess->$col;
                    if ($code === 'C8') {
                        $val = max(1.0, $val);
                    }
                } else {
                    $val = 3.0; // safe fallback default
                }
                $row[$code] = $val;
            }
            $matrix[$report->id] = $row;
        }

        // 2. Normalization Divisors
        $divisors = [];
        foreach ($criteria as $criterion) {
            $code = $criterion->code;
            $sumSq = 0;
            foreach ($matrix as $row) {
                $sumSq += pow($row[$code], 2);
            }
            $divisors[$code] = sqrt($sumSq) ?: 1.0;
        }

        // 3. Weighted Normalized Matrix Y
        $weightedMatrix = [];
        foreach ($matrix as $reportId => $row) {
            foreach ($criteria as $criterion) {
                $code = $criterion->code;
                $normalized = $row[$code] / $divisors[$code];
                $weightedMatrix[$reportId][$code] = $normalized * $weights[$code];
            }
        }

        // 4. Positive Ideal (A+) and Negative Ideal (A-)
        $idealPositive = [];
        $idealNegative = [];
        foreach ($criteria as $criterion) {
            $code = $criterion->code;
            $colValues = array_column($weightedMatrix, $code);
            $max = !empty($colValues) ? max($colValues) : 0;
            $min = !empty($colValues) ? min($colValues) : 0;

            if ($criteriaTypes[$code] === 'cost') {
                $idealPositive[$code] = $min;
                $idealNegative[$code] = $max;
            } else {
                $idealPositive[$code] = $max;
                $idealNegative[$code] = $min;
            }
        }

        // 5. Euclidean Distances (D+ and D-) & Preference Score (V)
        $scores = [];
        foreach ($weightedMatrix as $reportId => $row) {
            $dPlusSum = 0;
            $dMinSum = 0;
            foreach ($criteria as $criterion) {
                $code = $criterion->code;
                $dPlusSum += pow($row[$code] - $idealPositive[$code], 2);
                $dMinSum += pow($row[$code] - $idealNegative[$code], 2);
            }
            $dPlus = sqrt($dPlusSum);
            $dMin = sqrt($dMinSum);

            $denominator = $dPlus + $dMin;
            $v = $denominator > 0 ? ($dMin / $denominator) : 0.5;

            $scores[$reportId] = [
                'report_id' => $reportId,
                'score' => round($v, 4),
                'd_plus' => round($dPlus, 4),
                'd_min' => round($dMin, 4),
                'raw_values' => $matrix[$reportId],
                'weighted_values' => $row,
            ];
        }

        // 6. Rank reports descending by score
        uasort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        $rank = 1;
        $results = collect();
        $now = now();

        foreach ($scores as $reportId => $data) {
            $score = $data['score'];
            $level = match (true) {
                $score >= 0.75 => 'Sangat Prioritas',
                $score >= 0.55 => 'Prioritas Tinggi',
                $score >= 0.40 => 'Sedang',
                default => 'Rendah',
            };

            $report = $reports->firstWhere('id', $reportId);
            $reasoning = $this->generateReasoning($report, $data['raw_values'], $level, $score);

            $priorityResult = PriorityResult::updateOrCreate(
                ['report_id' => $reportId],
                [
                    'score' => $score,
                    'rank' => $rank,
                    'priority_level' => $level,
                    'reasoning' => $reasoning,
                    'calculation_details' => [
                        'd_plus' => $data['d_plus'],
                        'd_min' => $data['d_min'],
                        'raw_values' => $data['raw_values'],
                        'ideal_positive' => $idealPositive,
                        'ideal_negative' => $idealNegative,
                        'weights' => $weights,
                    ],
                    'calculated_at' => $now,
                ]
            );

            $results->push($priorityResult);
            $rank++;
        }

        return $results;
    }

    /**
     * Ensure a report has RoadAssessment values derived directly from YOLO AI photo detection:
     * 1. Landslide detected by AI -> Tier 1 (Scale 5.0)
     * 2. Pothole detected by AI -> Tier 2 (Scale 3.8 - 4.3 based on pothole count)
     * 3. Crack detected by AI -> Tier 3 (Scale 2.6 - 3.2 based on crack count)
     * 4. Normal / Minor / No defect -> Tier 4 (Scale 1.5)
     */
    public function ensureAssessmentExists(Report $report): RoadAssessment
    {
        $pendingDays = max(1, (int) $report->created_at->diffInDays(now()));
        $latestDetection = $report->damageDetections()->latest()->first();

        if ($latestDetection) {
            $classes = $latestDetection->detected_classes ?? [];
            $landslides = $classes['landslide'] ?? 0;
            $potholes = $classes['pothole'] ?? 0;
            $cracks = $classes['crack'] ?? 0;

            if ($landslides > 0) {
                $c1Scale = 5.0;
                $c2Safety = 5.0;
                $c7Impact = 5.0;
            } elseif ($potholes > 0) {
                $c1Scale = match (true) {
                    $potholes >= 6 => 4.3,
                    $potholes >= 3 => 4.0,
                    default => 3.8,
                };
                $c2Safety = 4.2;
                $c7Impact = 4.0;
            } elseif ($cracks > 0) {
                $c1Scale = match (true) {
                    $cracks >= 4 => 3.2,
                    default => 2.8,
                };
                $c2Safety = 2.8;
                $c7Impact = 2.8;
            } else {
                $c1Scale = 1.5;
                $c2Safety = 1.5;
                $c7Impact = 1.5;
            }
        } else {
            // Fallback before photo is analyzed
            $damageType = strtolower($report->damage_type ?? '');
            $disturbance = strtolower($report->disturbance_level ?? '');

            $c1Scale = match (true) {
                str_contains($damageType, 'landslide') || str_contains($damageType, 'longsor') => 5.0,
                str_contains($damageType, 'pothole') || str_contains($damageType, 'lubang') => 4.0,
                str_contains($damageType, 'crack') || str_contains($damageType, 'retak') => 2.8,
                default => 1.5,
            };

            $c2Safety = match (true) {
                str_contains($damageType, 'landslide') || str_contains($damageType, 'longsor') => 5.0,
                str_contains($damageType, 'pothole') || str_contains($damageType, 'lubang') => 4.2,
                str_contains($damageType, 'crack') || str_contains($damageType, 'retak') => 2.8,
                default => 1.5,
            };

            $c7Impact = match (true) {
                str_contains($damageType, 'landslide') || str_contains($damageType, 'longsor') => 5.0,
                str_contains($damageType, 'pothole') || str_contains($damageType, 'lubang') => 4.0,
                str_contains($damageType, 'crack') || str_contains($damageType, 'retak') => 2.8,
                default => 1.5,
            };
        }

        if ($report->assessment) {
            if (!$report->assessment->evaluated_by) {
                $report->assessment->update([
                    'c1_damage_scale' => $c1Scale,
                    'c2_user_safety' => $c2Safety,
                    'c7_community_impact' => $c7Impact,
                    'c8_pending_days' => $pendingDays,
                ]);
            }
            return $report->assessment;
        }

        return RoadAssessment::create([
            'report_id' => $report->id,
            'c1_damage_scale' => $c1Scale,
            'c2_user_safety' => $c2Safety,
            'c3_traffic_volume' => 3.5,
            'c4_report_count' => 1,
            'c5_road_function' => 3.0,
            'c6_facility_proximity' => 3.0,
            'c7_community_impact' => $c7Impact,
            'c8_pending_days' => $pendingDays,
        ]);
    }

    /**
     * Generate natural explanation for priority decision
     */
    protected function generateReasoning(Report $report, array $raw, string $level, float $score): string
    {
        $roadName = $report->road_name ?: 'Jalan ini';
        $reasons = [];

        if ($level === 'Sangat Prioritas' || $level === 'Prioritas Tinggi') {
            if (($raw['C1'] ?? 0) >= 4.0) {
                $reasons[] = 'tingkat kerusakan sangat parah/luas';
            }
            if (($raw['C2'] ?? 0) >= 4.0) {
                $reasons[] = 'berisiko tinggi terhadap keselamatan pengguna jalan';
            }
            if (($raw['C3'] ?? 0) >= 4.0) {
                $reasons[] = 'memiliki volume lalu lintas padat';
            }
            if (($raw['C4'] ?? 0) >= 2) {
                $reasons[] = "mendapat banyak akumulasi aduan masyarakat ({$raw['C4']} laporan serupa)";
            }
            if (($raw['C5'] ?? 0) >= 4.0) {
                $reasons[] = 'merupakan jalur fungsi utama/arteri';
            }
            if (($raw['C6'] ?? 0) >= 4.0) {
                $reasons[] = 'berada sangat dekat dengan fasilitas publik vital';
            }
            if (($raw['C7'] ?? 0) >= 4.0) {
                $reasons[] = 'berdampak signifikan terhadap aktivitas warga';
            }
            if (($raw['C8'] ?? 0) >= 7) {
                $reasons[] = "telah menunggu penanganan selama {$raw['C8']} hari";
            }

            if (empty($reasons)) {
                $reasons[] = 'memiliki skor urgensi kumulatif tertinggi dibanding usulan ruas jalan lainnya';
            }

            $reasonText = implode(', ', $reasons);
            return "{$roadName} ditetapkan dengan status [{$level}] (Skor TOPSIS: {$score}) karena {$reasonText}.";
        } elseif ($level === 'Sedang') {
            return "{$roadName} ditetapkan dengan status [Sedang] (Skor TOPSIS: {$score}) dan direkomendasikan masuk dalam jadwal pemeliharaan berkala.";
        } else {
            // Status Rendah
            return "{$roadName} ditetapkan dengan status [Rendah] (Skor TOPSIS: {$score}) karena tingkat urgensi penanganannya berada di bawah laporan ruas jalan lain yang lebih mendesak atau memiliki lebih banyak akumulasi aduan.";
        }
    }
}
