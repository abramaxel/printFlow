<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\Material;

class ProductionDashboard extends Component
{
    public function render()
    {
        // 1 query agregat: jumlah pesanan per tahap produksi + selesai hari ini
        $row = Order::query()
            ->selectRaw("SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as ready")
            ->selectRaw("SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing")
            ->selectRaw("SUM(CASE WHEN status = 'qc' THEN 1 ELSE 0 END) as qc")
            ->selectRaw("SUM(CASE WHEN status = 'shipping' THEN 1 ELSE 0 END) as shipping")
            ->selectRaw("SUM(CASE WHEN status = 'completed' AND DATE(updated_at) = ? THEN 1 ELSE 0 END) as done_today", [now()->toDateString()])
            ->first();

        $stages = [
            'ready'      => (int) ($row->ready ?? 0),
            'processing' => (int) ($row->processing ?? 0),
            'qc'         => (int) ($row->qc ?? 0),
            'shipping'   => (int) ($row->shipping ?? 0),
            'done_today' => (int) ($row->done_today ?? 0),
        ];

        // Bahan kritis: stok bersih (stock - reserved) <= minimum_stock
        $criticalQuery = Material::where('is_active', true)
            ->whereRaw('(stock - stock_reserved) <= minimum_stock');

        $criticalCount = (clone $criticalQuery)->count();
        $criticalMaterials = (clone $criticalQuery)
            ->orderByRaw('(stock - stock_reserved) ASC')
            ->limit(5)
            ->get(['id', 'name', 'unit', 'stock', 'stock_reserved', 'minimum_stock']);

        // Antrian aktif terbaru (siap produksi s/d dikirim)
        $activity = Order::with(['user', 'items.service'])
            ->whereIn('status', ['paid', 'processing', 'qc', 'shipping'])
            ->latest()
            ->limit(8)
            ->get();

        return view('livewire.admin.production-dashboard', [
            'stages'            => $stages,
            'criticalCount'     => $criticalCount,
            'criticalMaterials' => $criticalMaterials,
            'activity'          => $activity,
        ]);
    }
}