<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\MrpEngine;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// === MRP SCHEDULER ===

// Cek Re-Order Point setiap hari jam 6 pagi
Schedule::call(function () {
    $mrp = app(MrpEngine::class);
    $results = $mrp->runFullMrp();
    
    $critical = $results->where('urgency', 'critical')->count();
    
    if ($critical > 0) {
        \Log::info("MRP Daily Check: {$critical} material butuh pembelian segera.");
        
        // Opsional: Kirim notifikasi ke owner
        notify([
            'type' => 'inventory',
            'title' => '⚠️ Peringatan Stok Kritis',
            'message' => "{$critical} material di bawah batas minimum. Cek dashboard MRP.",
            'url' => route('admin.materials'), // sesuaikan dengan route Anda
        ]);
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
        
        // Notifikasi ke owner
        notify([
            'type' => 'inventory',
            'title' => '🗑️ Batch Kadaluarsa',
            'message' => "Batch {$batch->batch_number} ({$batch->material->name}) sudah kadaluarsa.",
            'url' => route('admin.materials'),
        ]);
    }
})->dailyAt('07:00')->name('batch-expiry-check');