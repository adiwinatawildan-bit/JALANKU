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

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            'json' => json_decode($setting->value, true) ?? $default,
            default => $setting->value,
        };
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): self
    {
        $strValue = is_array($value) ? json_encode($value) : (string) $value;
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $strValue, 'group' => $group, 'type' => $type]
        );
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
            return asset('storage/' . $logo);
        }

        return asset('images/logo.png');
    }
}
