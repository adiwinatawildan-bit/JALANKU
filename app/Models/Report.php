<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'opd_id',
        'title',
        'description',
        'road_name',
        'kecamatan',
        'desa',
        'damage_type',
        'disturbance_level',
        'additional_info',
        'status',
        'rejection_reason',
        'duplicate_of_id',
        'cluster_id',
        'is_public',
        'verified_by',
        'verified_at',
        'assigned_by',
        'assigned_at',
        'survey_notes',
        'survey_at',
        'completed_at',
        'citizen_feedback',
        'citizen_rating',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'verified_at' => 'datetime',
        'assigned_at' => 'datetime',
        'survey_at' => 'datetime',
        'completed_at' => 'datetime',
        'citizen_rating' => 'integer',
    ];

    // Status Constants
    public const STATUS_DIAJUKAN = 'DIAJUKAN';
    public const STATUS_DIVERIFIKASI = 'DIVERIFIKASI';
    public const STATUS_DITUGASKAN = 'DITUGASKAN';
    public const STATUS_SURVEI = 'SURVEI';
    public const STATUS_MENUNGGU_PERBAIKAN = 'MENUNGGU PERBAIKAN';
    public const STATUS_SEDANG_DIPERBAIKI = 'SEDANG DIPERBAIKI';
    public const STATUS_SELESAI = 'SELESAI';
    public const STATUS_DITOLAK = 'DITOLAK';
    public const STATUS_DUPLIKAT = 'DUPLIKAT';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ReportPhoto::class);
    }

    public function initialPhotos(): HasMany
    {
        return $this->hasMany(ReportPhoto::class)->where('photo_type', 'initial');
    }

    public function surveyPhotos(): HasMany
    {
        return $this->hasMany(ReportPhoto::class)->where('photo_type', 'survey');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProgressUpdate::class)->orderBy('week_number', 'asc');
    }

    public function latestProgress(): HasOne
    {
        return $this->hasOne(ProgressUpdate::class)->latestOfMany();
    }

    public function damageDetections(): HasMany
    {
        return $this->hasMany(DamageDetection::class);
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(RoadAssessment::class);
    }

    public function priorityResult(): HasOne
    {
        return $this->hasOne(PriorityResult::class);
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'duplicate_of_id');
    }

    public function duplicates(): HasMany
    {
        return $this->hasMany(Report::class, 'duplicate_of_id');
    }

    // Helper: current progress percentage
    public function getCurrentProgressAttribute(): int
    {
        if ($this->status === self::STATUS_SELESAI) {
            return 100;
        }

        $latest = $this->progressUpdates()->latest('week_number')->first();
        if ($latest) {
            return (int) $latest->progress_percentage;
        }

        return match ($this->status) {
            self::STATUS_SEDANG_DIPERBAIKI => 20,
            self::STATUS_MENUNGGU_PERBAIKAN => 10,
            self::STATUS_SURVEI => 5,
            default => 0,
        };
    }

    // Status color badge helper
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SELESAI => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950 dark:text-emerald-300',
            self::STATUS_SEDANG_DIPERBAIKI => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950 dark:text-amber-300',
            self::STATUS_SURVEI, self::STATUS_MENUNGGU_PERBAIKAN => 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-950 dark:text-yellow-300',
            self::STATUS_DIVERIFIKASI, self::STATUS_DITUGASKAN => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950 dark:text-blue-300',
            self::STATUS_DITOLAK => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950 dark:text-rose-300',
            self::STATUS_DUPLIKAT => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-950 dark:text-purple-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300',
        };
    }

    // Marker color for Leaflet
    public function getMarkerColorAttribute(): string
    {
        $priority = $this->priorityResult?->priority_level;
        if ($this->status === self::STATUS_SELESAI) return '#10b981'; // Green
        if ($priority === 'Sangat Prioritas') return '#ef4444'; // Red
        if ($priority === 'Prioritas Tinggi') return '#f97316'; // Orange
        if ($priority === 'Sedang') return '#eab308'; // Yellow
        return '#3b82f6'; // Blue / info
    }

    // Damage type label in Indonesian
    public function getDamageTypeLabelAttribute(): string
    {
        return match (strtolower($this->damage_type ?? '')) {
            'pothole' => 'Lubang (Pothole)',
            'crack', 'retak' => 'Retak (Crack)',
            'landslide', 'amblas' => 'Longsor (Landslide)',
            'bergelombang' => 'Jalan Bergelombang',
            'drainase' => 'Saluran Drainase',
            default => ucfirst($this->damage_type ?? 'Lainnya'),
        };
    }

}
