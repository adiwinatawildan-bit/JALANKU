<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $fillable = [
        'report_id',
        'road_name',
        'address_detail',
        'latitude',
        'longitude',
        'kecamatan',
        'desa',
        'postal_code',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
