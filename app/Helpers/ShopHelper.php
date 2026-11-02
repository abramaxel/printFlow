<?php

if (!function_exists('setting')) {
    /**
     * Helper global untuk akses pengaturan toko.
     * Usage: setting('bank_account'), setting('shop_name', 'Default')
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\ShopSetting::get($key, $default);
    }
}