<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgressUpdate extends Model
{
    protected $fillable = [
        'report_id',
        'week_number',
        'date',
        'status',
        'progress_percentage',
        'description',
        'uploaded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'week_number' => 'integer',
        'progress_percentage' => 'float',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProgressPhoto::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
