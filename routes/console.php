<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\MrpEngine;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// === MRP SCHEDULER ===

// Cek Re-Order Point & Kirim Notifikasi setiap hari jam 6 pagi
Schedule::call(function () {
    $mrp = app(MrpEngine::class);
    $results = $mrp->runFullMrp();
    
    $criticalCount = 0;
    
    // Loop hasil MRP, jika ada yang 'critical', kirim notifikasi
    foreach ($results as $res) {
        if (($res['urgency'] ?? '') === 'critical') {
            notify('owners', new \App\Notifications\StockCriticalNotification(
                materialName: $res['material_name'],
                currentStock: $res['net_available'],
                minimumStock: $res['minimum_stock'],
                materialId: $res['material_id']
            ));
            $criticalCount++;
        }
    }

    if ($criticalCount > 0) {
        \Log::info("MRP Scheduler: {$criticalCount} notifikasi stok kritis terkirim ke Owner.");
    }
})->dailyAt('06:00')->name('mrp-daily-check')->withoutOverlapping();

// Cek batch kadaluarsa setiap hari jam 7 pagi
Schedule::call(function () {
    $expiredBatches = \App\Models\MaterialBatch::where('status', 'active')
        ->where('expiry_date', '<', now())
        ->get();
    
    foreach ($expiredBatches as $batch) {
        $batch->update(['status' => 'expired']);
        \Log::warning("Batch expired: {$batch->batch_number} ({$batch->material->name})");
    }
})->dailyAt('07:00')->name('batch-expiry-check');