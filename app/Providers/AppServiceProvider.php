<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer untuk badge notifikasi delay (khusus Super Admin)
        View::composer('components.layouts.admin', function ($view) {
            $count = 0;
            try {
                if (auth()->check() && method_exists(auth()->user(), 'isSuperAdmin') && auth()->user()->isSuperAdmin()) {
                    $orders = \App\Models\Order::whereNotIn('status', ['completed', 'cancelled', 'rejected'])
                        ->whereNotNull('deadline_date')
                        ->get();

                    $count = $orders->filter(function ($o) {
                        if (!method_exists($o, 'productionTrace')) return false;
                        $trace = $o->productionTrace();
                        return in_array($trace['state'] ?? '', ['delayed', 'at_risk'], true);
                    })->count();
                }
            } catch (\Throwable $e) {
                // Gagal hitung? Biarkan 0, jangan crash aplikasi
                \Log::warning('delayAlertCount Composer failed: ' . $e->getMessage());
                $count = 0;
            }

            $view->with('delayAlertCount', $count);
        });
    }
}