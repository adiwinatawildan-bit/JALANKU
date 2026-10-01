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

        // 1. Cek dari relational collection jika sudah terload di memory
        if ($this->relationLoaded('progressUpdates') && $this->progressUpdates->isNotEmpty()) {
            $latest = $this->progressUpdates->sortByDesc('week_number')->sortByDesc('id')->first();
            if ($latest) {
                return (int) round($latest->progress_percentage);
            }
        }

        // 2. Query ke database untuk mendapatkan update progres terbaru yang diisi oleh OPD
        $latest = $this->progressUpdates()->latest('week_number')->latest('id')->first();
        if ($latest) {
            return (int) round($latest->progress_percentage);
        }

        // Jika belum ada update progres yang diisi OPD, tampilkan 0%
        return 0;
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
            'menunggu_analisis', 'pending_ai', 'proses_ai' => 'Menunggu Analisis AI',
            'normal', 'baik' => 'Normal / Baik',
            'bergelombang' => 'Jalan Bergelombang',
            'drainase' => 'Saluran Drainase',
            default => ucfirst($this->damage_type ?: 'Menunggu Analisis AI'),
        };
    }

    // Google Maps Query helper prioritizing accurate address filled by citizen
    public function getGoogleMapsQueryAttribute(): string
    {
        $address = $this->location?->address_detail;
        if (!empty($address)) {
            return $address;
        }

        $parts = array_filter([
            $this->road_name,
            $this->desa ? 'Desa ' . $this->desa : null,
            $this->kecamatan ? 'Kecamatan ' . $this->kecamatan : null,
            'Jawa Barat',
        ]);

        if (!empty($parts)) {
            return implode(', ', $parts);
        }

        if ($this->location?->latitude && $this->location?->longitude) {
            return $this->location->latitude . ',' . $this->location->longitude;
        }

        return $this->road_name ?? '';
    }
}
