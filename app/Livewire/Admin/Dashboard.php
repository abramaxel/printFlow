<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Material;
use App\Models\OrderItem;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return match (true) {
            $this->isOwner() => view('livewire.admin.owner-dashboard', $this->ownerData()),
            $this->isCS()    => view('livewire.admin.cs-dashboard', $this->csData()),
            default          => view('livewire.admin.dashboard', [
                'pipeline'       => $this->orderPipeline(),
                'lowStockAlerts' => $this->criticalStockAlerts(),
            ]),
        };
    }

    private function isOwner(): bool
    {
        return in_array(auth()->user()->role, ['owner', 'super_admin']);
    }

    private function isCS(): bool
    {
        return in_array(auth()->user()->role, ['cs', 'admin']);
    }

    /* =========================================================
       DATA UNTUK OWNER
    ========================================================= */
    private function ownerData(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        $monthlyRevenue = (float) Payment::where('status', 'verified')
            ->whereBetween('verified_at', [$start, $end])->sum('amount');

        $monthlyOutstanding = (float) Invoice::whereBetween('created_at', [$start, $end])
            ->whereColumn('total_paid', '<', 'total_amount')
            ->selectRaw('COALESCE(SUM(total_amount - total_paid), 0) AS outstanding')
            ->value('outstanding');

        return [
            'monthlyRevenue'     => $monthlyRevenue,
            'monthlyOutstanding' => $monthlyOutstanding,
            'newOrders'          => Order::whereBetween('created_at', [$start, $end])->count(),
            'completedOrders'    => Order::where('status', 'completed')
                                    ->whereBetween('updated_at', [$start, $end])->count(),
            'pipeline'           => $this->orderPipeline(),
            'chartData'          => $this->revenueChartData(),
            'lowStockAlerts'     => $this->criticalStockAlerts(), // ← DIGANTI ke logika MRP
        ];
    }

    /* =========================================================
       DATA UNTUK CS
    ========================================================= */
    private function csData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            'waitingInvoice'  => Order::where('status', 'waiting_invoice')->latest()->take(5)->get(),
            'unpaidOrders'    => Order::where('status', 'unpaid')->latest()->take(5)->get(),
            'inProduction'    => Order::whereIn('status', ['paid', 'processing', 'qc'])->count(),
            'myCreatedOrders' => Order::whereBetween('created_at', [$startOfMonth, Carbon::now()])->count(),
        ];
    }

    /* =========================================================
       HELPER BERSAMA (DRY — dipakai Owner & Fallback)
    ========================================================= */
    private function orderPipeline(): array
    {
        return [
            'waiting_invoice' => Order::where('status', 'waiting_invoice')->count(),
            'unpaid'          => Order::where('status', 'unpaid')->count(),
            'paid'            => Order::where('status', 'paid')->count(),
            'processing'      => Order::where('status', 'processing')->count(),
            'qc'              => Order::where('status', 'qc')->count(),
            'shipping'        => Order::where('status', 'shipping')->count(),
        ];
    }

    private function revenueChartData(): array
    {
        $labels = []; $values = []; $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $stats = Payment::where('status', 'verified')
                ->whereBetween('verified_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->selectRaw('SUM(amount) as total, COUNT(*) as count')
                ->first();

            $labels[] = $month->format('M Y');
            $values[] = (float) ($stats->total ?? 0);
            $counts[] = (int) ($stats->count ?? 0);
        }

        return ['labels' => $labels, 'values' => $values, 'counts' => $counts];
    }

        /**
     * ALERT STOK MENIPIS — logika MRP (SINKRON dgn Materials & Purchase Request)
     * Kunci output disamakan dgn owner-dashboard.blade.php:
     *   status / net_available / minimum_stock / pipeline / unit / name / id
     * Efisiensi: 2 query, TANPA N+1, read-only (tidak menulis PR).
     */
        private function criticalStockAlerts(): array
    {
        $pipelineDemand = OrderItem::whereIn('order_id',
            Order::whereIn('status', ['paid', 'processing'])->pluck('id')
        )
        ->selectRaw('material_id, SUM(area_m2) AS total')
        ->groupBy('material_id')
        ->pluck('total', 'material_id');

        return Material::with('category')
            ->where('is_active', true)
            ->get()
            ->map(function ($m) use ($pipelineDemand) {
                $available = (float) $m->stock - (float) ($m->stock_reserved ?? 0);
                $net       = $available - (float) ($pipelineDemand[$m->id] ?? 0);
                $minimum   = (float) ($m->minimum_stock ?? 0);
                
                $status = match (true) {
                    $net <= 0        => 'out_of_stock',
                    $net <= $minimum => 'critical',
                    default          => 'safe',
                };

                return [
                    'id'            => $m->id,
                    'name'          => $m->name,
                    'unit'          => $m->unit ?? 'm²',
                    'stock'         => (float) $m->stock,
                    'reserved'      => (float) ($m->stock_reserved ?? 0),
                    'available'     => $available,
                    'pipeline'      => (float) ($pipelineDemand[$m->id] ?? 0),
                    'net_available' => $net,          // ← Key yang diharapkan Blade
                    'minimum_stock' => $minimum,      // ← Key yang diharapkan Blade
                    'status'        => $status,       // ← Key yang diharapkan Blade
                ];
            })
            ->filter(fn ($row) => $row['status'] !== 'safe')
            ->sortBy(fn ($row) => $row['net_available'])
            ->take(8)
            ->values()
            ->all();
    }
}