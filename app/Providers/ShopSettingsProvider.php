<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\ShopSettings as Store;

class ShopSettingsProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            $s = Store::all();

            $brand = [];
            if (!empty($s['shop_name']))    $brand['app.brand.name'] = $s['shop_name'];
            if (!empty($s['shop_phone']))   $brand['app.brand.whatsapp'] = Store::wa($s['shop_phone']);
            if (!empty($s['shop_phone']))   $brand['app.brand.phone'] = $s['shop_phone'];
            if (!empty($s['shop_email']))   $brand['app.brand.email'] = $s['shop_email'];
            if (!empty($s['shop_address'])) $brand['app.brand.address'] = $s['shop_address'];
            if (!empty($s['shop_hours']))   $brand['app.brand.hours'] = $s['shop_hours'];

            $brand['app.shop'] = $s;

            config($brand);
        } catch (\Throwable $e) {
            // Tabel belum ada / cache belum siap — biarkan config bawaan berjalan.
        }
    }
}