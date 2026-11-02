<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ShopSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Ambil nilai setting dengan cache 1 jam.
     * Usage: setting('bank_account') atau setting('shop_name', 'Default')
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('shop_settings', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Update nilai setting + invalidate cache.
     */
    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('shop_settings');
    }

    /**
     * Ambil semua settings sebagai array.
     */
    public static function allSettings(): array
    {
        return Cache::remember('shop_settings', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }
}