<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportPhoto extends Model
{
    protected $fillable = [
        'report_id',
        'file_name',
        'file_path',
        'file_url',
        'photo_type', // 'initial', 'survey'
        'caption',
        'uploaded_by',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function damageDetections(): HasMany
    {
        return $this->hasMany(DamageDetection::class);
    }
}
