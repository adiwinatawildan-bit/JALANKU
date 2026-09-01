<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriorityWeight extends Model
{
    protected $fillable = [
        'priority_criterion_id',
        'weight_percentage',
        'updated_by',
    ];

    protected $casts = [
        'weight_percentage' => 'float',
    ];

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(PriorityCriterion::class, 'priority_criterion_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
