<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Models\MaterialBatch;
use App\Models\PurchaseRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
#[Title('Dashboard Inventory')]
class InventoryDashboard extends Component
{
    public function render()
    {
        // === KPI 1: TOTAL NILAI STOK ===
        $totalValue = Material::where('is_active', true)
            ->get()
            ->sum(fn($m) => (float)$m->stock * (float)$m->price);

        // === KPI 2: TOTAL JENIS MATERIAL AKTIF ===
        $totalMaterials = Material::where('is_active', true)->count();

        // === KPI 3: MATERIAL KRITIS ===
        $criticalMaterials = Material::where('is_active', true)
            ->get()
            ->filter(fn($m) => ($m->stock - $m->stock_reserved) <= ($m->minimum_stock ?? 0))
            ->count();

        // === KPI 4: PENDING PURCHASE REQUESTS ===
        $pendingPR = PurchaseRequest::where('status', 'pending')->count();

        // === TOP 5 FAST MOVING MATERIALS (30 hari terakhir) ===
        $fastMoving = MaterialMovement::where('movement_type', 'out')
            ->where('sub_type', 'production_issue')
            ->where('movement_date', '>=', now()->subDays(30))
            ->select('material_id', DB::raw('SUM(quantity) as total_usage'), DB::raw('SUM(total_cost) as total_cost'))
            ->groupBy('material_id')
            ->orderByDesc('total_usage')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $material = Material::find($item->material_id);
                return (object) [
                    'material' => $material,
                    'total_usage' => $item->total_usage,
                    'total_cost' => $item->total_cost,
                    'avg_daily' => $item->total_usage / 30,
                ];
            });

        // === DATA GRAFIK: MOVEMENT TREND 30 HARI ===
        $movementTrend = $this->getMovementTrend(30);

        // === ALERT: BATCH KADALUARSA (30 hari ke depan) ===
        $expiringBatches = MaterialBatch::where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('remaining_quantity', '>', 0)
            ->with('material')
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();

        // === ALERT: STOK KRITIS ===
        $criticalStock = Material::where('is_active', true)
            ->get()
            ->filter(fn($m) => ($m->stock - $m->stock_reserved) <= ($m->minimum_stock ?? 0) && ($m->minimum_stock ?? 0) > 0)
            ->sortBy(fn($m) => ($m->stock - $m->stock_reserved) - ($m->minimum_stock ?? 0))
            ->take(5);

        // === PENDING PURCHASE REQUESTS ===
        $purchaseRequests = PurchaseRequest::where('status', 'pending')
            ->with('material')
            ->orderByRaw("CASE WHEN urgency = 'critical' THEN 1 WHEN urgency = 'warning' THEN 2 ELSE 3 END")
            ->limit(5)
            ->get();

        return view('livewire.admin.inventory-dashboard', [
            'kpi' => [
                'total_value' => $totalValue,
                'total_materials' => $totalMaterials,
                'critical_materials' => $criticalMaterials,
                'pending_pr' => $pendingPR,
            ],
            'fastMoving' => $fastMoving,
            'movementTrend' => $movementTrend,
            'expiringBatches' => $expiringBatches,
            'criticalStock' => $criticalStock,
            'purchaseRequests' => $purchaseRequests,
        ]);
    }

    /**
     * Hitung tren pergerakan stok per hari (untuk Chart.js)
     */
    private function getMovementTrend(int $days): array
    {
        $trend = [
            'labels' => [],
            'in' => [],
            'out' => [],
        ];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $label = $date->format('d M');

            $inQty = MaterialMovement::where('movement_type', 'in')
                ->whereDate('movement_date', $dateStr)
                ->sum('quantity');

            $outQty = MaterialMovement::where('movement_type', 'out')
                ->whereDate('movement_date', $dateStr)
                ->sum('quantity');

            $trend['labels'][] = $label;
            $trend['in'][] = round($inQty, 2);
            $trend['out'][] = round($outQty, 2);
        }

        return $trend;
    }
}