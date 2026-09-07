<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    protected static ?array $cachedSettings = null;

    public static function get(string $key, mixed $default = null): mixed
    {
        if (self::$cachedSettings === null) {
            self::$cachedSettings = \Illuminate\Support\Facades\Cache::remember('all_system_settings_cache', 3600, function () {
                try {
                    return self::all()->keyBy('key')->toArray();
                } catch (\Throwable $e) {
                    return [];
                }
            });
        }

        if (!isset(self::$cachedSettings[$key])) {
            return $default;
        }

        $setting = self::$cachedSettings[$key];
        $type = $setting['type'] ?? 'string';
        $val = $setting['value'] ?? null;

        return match ($type) {
            'boolean' => filter_var($val, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $val,
            'float' => (float) $val,
            'json' => json_decode($val, true) ?? $default,
            default => $val ?? $default,
        };
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): self
    {
        $strValue = is_array($value) ? json_encode($value) : (string) $value;
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $strValue, 'group' => $group, 'type' => $type]
        );

        \Illuminate\Support\Facades\Cache::forget('all_system_settings_cache');
        self::$cachedSettings = null;

        return $setting;
    }

    public static function appName(): string
    {
        return (string) self::get('app_name', 'JALAN KU');
    }

    public static function appSlogan(): string
    {
        return (string) self::get('app_slogan', 'Laporkan. Pantau. Perbaiki.');
    }

    public static function getLogo(): string
    {
        $logo = self::get('app_logo');
        if (!empty($logo)) {
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
                return $logo;
            }
            if (file_exists(public_path('storage/' . $logo))) {
                return asset('storage/' . $logo);
            }
            if (file_exists(public_path($logo))) {
                return asset($logo);
            }
        }

        return asset('images/logo.png');
    }
}
