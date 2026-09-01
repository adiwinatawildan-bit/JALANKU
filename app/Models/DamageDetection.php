<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamageDetection extends Model
{
    protected $fillable = [
        'report_id',
        'report_photo_id',
        'detected_classes',
        'total_defects',
        'confidence_score',
        'bounding_boxes',
        'damaged_area_sqm',
        'annotated_image_path',
        'annotated_image_url',
        'model_version',
    ];

    protected $casts = [
        'detected_classes' => 'array',
        'bounding_boxes' => 'array',
        'confidence_score' => 'float',
        'damaged_area_sqm' => 'float',
        'total_defects' => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(ReportPhoto::class, 'report_photo_id');
    }
}
