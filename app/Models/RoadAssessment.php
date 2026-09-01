<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoadAssessment extends Model
{
    protected $fillable = [
        'report_id',
        'c1_damage_scale',
        'c2_user_safety',
        'c3_traffic_volume',
        'c4_report_count',
        'c5_road_function',
        'c6_facility_proximity',
        'c7_community_impact',
        'c8_pending_days',
        'evaluated_by',
    ];

    protected $casts = [
        'c1_damage_scale' => 'float',
        'c2_user_safety' => 'float',
        'c3_traffic_volume' => 'float',
        'c4_report_count' => 'integer',
        'c5_road_function' => 'float',
        'c6_facility_proximity' => 'float',
        'c7_community_impact' => 'float',
        'c8_pending_days' => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
