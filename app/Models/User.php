<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'opd_id',
        'avatar_url',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProgressUpdate::class, 'uploaded_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // Role helpers
    public function hasRole(string|array $roleNames): bool
    {
        if (!$this->role) {
            return false;
        }

        if (is_array($roleNames)) {
            return in_array($this->role->name, $roleNames);
        }

        return $this->role->name === $roleNames;
    }

    public function isMasyarakat(): bool
    {
        return $this->hasRole('masyarakat');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isOpd(): bool
    {
        return $this->hasRole('opd');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isStaffOrAdmin(): bool
    {
        return in_array($this->role?->name, ['admin', 'super_admin', 'opd']);
    }

    public function getAvatar(): ?string
    {
        if (!empty($this->avatar_url)) {
            if (str_starts_with($this->avatar_url, 'http://') || str_starts_with($this->avatar_url, 'https://')) {
                return $this->avatar_url;
            }
            if (file_exists(public_path('storage/' . $this->avatar_url))) {
                return asset('storage/' . $this->avatar_url);
            }
            if (file_exists(public_path($this->avatar_url))) {
                return asset($this->avatar_url);
            }
            return asset('storage/' . $this->avatar_url);
        }
        return null;
    }

    public function hasAvatar(): bool
    {
        return !empty($this->avatar_url);
    }
}
