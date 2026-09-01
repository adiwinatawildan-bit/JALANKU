<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriorityCriterion extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'weight_percentage',
        'description',
        'is_active',
    ];

    protected $casts = [
        'weight_percentage' => 'float',
        'is_active' => 'boolean',
    ];

    public function weightHistories(): HasMany
    {
        return $this->hasMany(PriorityWeight::class);
    }
}
