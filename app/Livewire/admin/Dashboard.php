<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Material;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        // CEK ROLE USER
        if ($this->isOwner()) {
            return view('livewire.admin.owner-dashboard', $this->ownerData());
        } 
        
        if ($this->isCS()) {
            return view('livewire.admin.cs-dashboard', $this->csData());
        }

        // Fallback untuk admin biasa atau role lain
        return view('livewire.admin.dashboard'); 
    }

    private function isOwner(): bool
    {
        // Sesuaikan dengan nama role di database Anda
        return in_array(auth()->user()->role, ['owner', 'super_admin']);
    }

    private function isCS(): bool
    {
        // Sesuaikan dengan nama role di database Anda  
        return in_array(auth()->user()->role, ['cs', 'admin']);
    }

    /* =========================================================
       DATA UNTUK OWNER
       ========================================================= */
    private function ownerData(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        $monthlyRevenue = (float) Payment::where('status', 'verified')->whereBetween('verified_at', [$start, $end])->sum('amount');
        $monthlyOutstanding = (float) Invoice::whereBetween('created_at', [$start, $end])->whereColumn('total_paid', '<', 'total_amount')->selectRaw('COALESCE(SUM(total_amount - total_paid), 0) AS outstanding')->value('outstanding');
        $newOrders = Order::whereBetween('created_at', [$start, $end])->count();
        $completedOrders = Order::where('status', 'completed')->whereBetween('updated_at', [$start, $end])->count();

        $pipeline = [
            'waiting_invoice' => Order::where('status', 'waiting_invoice')->count(),
            'unpaid'          => Order::where('status', 'unpaid')->count(),
            'paid'            => Order::where('status', 'paid')->count(),
            'processing'      => Order::where('status', 'processing')->count(),
            'qc'              => Order::where('status', 'qc')->count(),
            'shipping'        => Order::where('status', 'shipping')->count(),
        ];

        return [
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyOutstanding' => $monthlyOutstanding,
            'newOrders' => $newOrders,
            'completedOrders' => $completedOrders,
            'pipeline' => $pipeline,
            'chartData' => $this->revenueChartData(),
            'lowStockAlerts' => $this->lowStockAlerts(),
        ];
    }

    /* =========================================================
       DATA UNTUK CS
       ========================================================= */
    private function csData(): array
    {
        $waitingInvoice = Order::where('status', 'waiting_invoice')->latest()->take(5)->get();
        $unpaidOrders = Order::where('status', 'unpaid')->latest()->take(5)->get();
        $inProduction = Order::whereIn('status', ['paid', 'processing', 'qc'])->count();
        $startOfMonth = Carbon::now()->startOfMonth();
        $myCreatedOrders = Order::whereBetween('created_at', [$startOfMonth, Carbon::now()])->count();

        return [
            'waitingInvoice' => $waitingInvoice,
            'unpaidOrders' => $unpaidOrders,
            'inProduction' => $inProduction,
            'myCreatedOrders' => $myCreatedOrders,
        ];
    }

    private function revenueChartData(): array
    {
        $labels = []; $values = []; $counts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $stats = Payment::where('status', 'verified')->whereBetween('verified_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->selectRaw('SUM(amount) as total, COUNT(*) as count')->first();
            $labels[] = $month->format('M Y');
            $values[] = (float) ($stats->total ?? 0);
            $counts[] = (int) ($stats->count ?? 0);
        }
        return ['labels' => $labels, 'values' => $values, 'counts' => $counts];
    }

    private function lowStockAlerts()
    {
        return Material::where('is_active', true)->where('stock', '>', 0)
            ->whereRaw('stock - stock_reserved < (stock * 0.20)')
            ->selectRaw('*, (stock - stock_reserved) as available')
            ->orderByRaw('(stock - stock_reserved) / NULLIF(stock, 0) ASC')->limit(8)->get()
            ->map(fn ($m) => [
                'name' => $m->name, 
                'stock' => (float)$m->stock, 
                'available' => max(0, $m->stock - $m->stock_reserved),
                'percentage' => $m->stock > 0 ? round((max(0, $m->stock - $m->stock_reserved) / $m->stock) * 100, 1) : 0,
                'severity' => (max(0, $m->stock - $m->stock_reserved) / $m->stock * 100) < 10 ? 'critical' : (((max(0, $m->stock - $m->stock_reserved) / $m->stock * 100) < 30) ? 'warning' : 'low'),
            ]);
    }
}