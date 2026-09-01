<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriorityResult extends Model
{
    protected $fillable = [
        'report_id',
        'score',
        'rank',
        'priority_level',
        'reasoning',
        'calculation_details',
        'calculated_at',
    ];

    protected $casts = [
        'score' => 'float',
        'rank' => 'integer',
        'calculation_details' => 'array',
        'calculated_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->priority_level) {
            'Sangat Prioritas' => 'bg-red-100 text-red-800 border-red-300 dark:bg-red-950 dark:text-red-300',
            'Prioritas Tinggi' => 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-950 dark:text-orange-300',
            'Sedang' => 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-950 dark:text-yellow-300',
            default => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950 dark:text-emerald-300',
        };
    }
}
