<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Material;
use App\Models\Service;
use App\Models\PurchaseRequest;
use App\Models\PurchaseReceipt;
use App\Models\MaterialMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OwnerCommandCenter extends Component
{
    public string $period = 'month';      // month | 30d | 7d
    public string $trendMetric = 'revenue'; // revenue | purchases

    public function mount(): void
    {
        $role = auth()->user()->role ?? '';
        if (!in_array($role, ['owner', 'super_admin'], true)) abort(403);
    }

    public function setPeriod(string $p): void { $this->period = $p; }
    public function setTrendMetric(string $m): void { $this->trendMetric = $m; }

    public function render()
    {
        $now = now();
        $start = match ($this->period) {
            '7d'  => $now->copy()->subDays(7),
            '30d' => $now->copy()->subDays(30),
            default => $now->copy()->startOfMonth(),
        };
        $today = $now->toDateString();

        // Keuangan periode
        $revenue = (float) Payment::where('status', 'verified')->whereBetween('verified_at', [$start, $now])->sum('amount');
        $purchases = (float) PurchaseReceipt::whereBetween('receipt_date', [$start->toDateString(), $today])->sum('total_cost');
        $margin = $revenue - $purchases;

        // Piutang & overdue
        $open = Invoice::where('status', '!=', 'paid')->with('order')->get()
            ->filter(fn($i) => $i->order && $i->order->status !== 'cancelled');
        $receivable = (float) $open->sum(fn($i) => max(0, (float)$i->total_amount - (float)$i->total_paid));
        $overdue = $open->filter(fn($i) => $i->due_date && $i->due_date < $today);
        $overdueValue = (float) $overdue->sum(fn($i) => max(0, (float)$i->total_amount - (float)$i->total_paid));
        $overdueCount = $overdue->count();

        // Pesanan
        $orderRow = Order::selectRaw("SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_orders", [$start])
            ->selectRaw("SUM(CASE WHEN status = 'completed' AND updated_at >= ? THEN 1 ELSE 0 END) as done", [$start])
            ->selectRaw("SUM(CASE WHEN status = 'waiting_invoice' THEN 1 ELSE 0 END) as waiting_invoice")
            ->selectRaw("SUM(CASE WHEN status = 'unpaid' THEN 1 ELSE 0 END) as unpaid")
            ->selectRaw("SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid")
            ->selectRaw("SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing")
            ->selectRaw("SUM(CASE WHEN status = 'qc' THEN 1 ELSE 0 END) as qc")
            ->selectRaw("SUM(CASE WHEN status = 'shipping' THEN 1 ELSE 0 END) as shipping")
            ->first();
        $pipeline = [
            'waiting_invoice' => (int)($orderRow->waiting_invoice ?? 0),
            'unpaid' => (int)($orderRow->unpaid ?? 0),
            'paid' => (int)($orderRow->paid ?? 0),
            'processing' => (int)($orderRow->processing ?? 0),
            'qc' => (int)($orderRow->qc ?? 0),
            'shipping' => (int)($orderRow->shipping ?? 0),
        ];

        // Tren 6 bulan (grouped, 2 query)
        $sixStart = $now->copy()->startOfMonth()->subMonths(5);
        $revByMonth = Payment::where('status', 'verified')->where('verified_at', '>=', $sixStart)
            ->selectRaw("to_char(verified_at,'YYYY-MM') as ym, SUM(amount) as total")->groupBy('ym')->pluck('total', 'ym');
        $purByMonth = PurchaseReceipt::where('receipt_date', '>=', $sixStart->toDateString())
            ->selectRaw("to_char(receipt_date,'YYYY-MM') as ym, SUM(total_cost) as total")->groupBy('ym')->pluck('total', 'ym');
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->startOfMonth()->subMonths($i);
            $key = $m->format('Y-m');
            $trend[] = ['label' => $m->format('M Y'), 'revenue' => (float)($revByMonth[$key] ?? 0), 'purchases' => (float)($purByMonth[$key] ?? 0)];
        }

        // Stok (1 query, hitung di PHP)
        $materials = Material::where('is_active', true)->get(['id', 'name', 'unit', 'stock', 'stock_reserved', 'minimum_stock']);
        $net = fn($m) => (float)$m->stock - (float)$m->stock_reserved;
        $overReserved = $materials->filter(fn($m) => $net($m) < 0)->sortBy($net)->values();
        $ropUnset = $materials->filter(fn($m) => (float)$m->minimum_stock == 0)->count();
        $stockAlerts = $materials->filter(fn($m) => $net($m) <= (float)$m->minimum_stock)->sortBy($net)->take(6)->values();
        $stockCriticalCount = $materials->filter(fn($m) => $net($m) <= (float)$m->minimum_stock)->count();

        // Purchasing
        $prRow = PurchaseRequest::where('status', 'pending')
            ->selectRaw('COUNT(*) as c, COALESCE(SUM(estimated_cost),0) as v')
            ->selectRaw("SUM(CASE WHEN urgency = 'critical' THEN 1 ELSE 0 END) as crit")->first();
        $prPending = (int)($prRow->c ?? 0); $prValue = (float)($prRow->v ?? 0); $prCritical = (int)($prRow->crit ?? 0);
        $prAwaiting = PurchaseRequest::whereIn('status', ['approved', 'ordered'])->count();
        $recentPrs = PurchaseRequest::with('material')->where('status', 'pending')
            ->orderByRaw("CASE WHEN urgency='critical' THEN 0 ELSE 1 END")->latest()->limit(4)->get();
        $recRow = PurchaseReceipt::whereBetween('receipt_date', [$start->toDateString(), $today])
            ->selectRaw('COUNT(*) as c, COALESCE(SUM(total_cost),0) as v')->first();
        $receiptCount = (int)($recRow->c ?? 0);
        $recentReceipts = PurchaseReceipt::with('supplier')->latest()->limit(4)->get();

        // Kepatuhan
        $noBom = $this->servicesWithoutBom();
        $adjRow = MaterialMovement::where('movement_type', 'adjustment')->where('movement_date', '>=', $start)
            ->selectRaw('COUNT(*) as c, MAX(movement_date) as last')->first();
        $adjCount = (int)($adjRow->c ?? 0);
        $lastOpname = MaterialMovement::where('movement_type', 'adjustment')->max('movement_date');
        [$expCount, $expBatches] = $this->expiryAlerts();

        // CRM
        $topCustomers = User::where('role', 'customer')
            ->addSelect(['paid_period' => Payment::selectRaw('COALESCE(SUM(payments.amount),0)')
                ->where('payments.status', 'verified')->whereBetween('payments.verified_at', [$start, $now])
                ->whereIn('payments.invoice_id', Invoice::select('id')
                    ->whereIn('invoices.order_id', Order::select('id')->whereColumn('orders.user_id', 'users.id')))])
            ->orderByDesc('paid_period')->limit(4)->get();

        // Decision queue
        $decisions = $this->buildDecisions($prPending, $prCritical, $prValue, $overdueCount, $overdueValue,
            $stockCriticalCount, $overReserved->count(), $noBom, $pipeline['waiting_invoice'], $ropUnset);

        return view('livewire.admin.owner-command-center', [
            'revenue' => $revenue, 'purchases' => $purchases, 'margin' => $margin,
            'receivable' => $receivable, 'overdueValue' => $overdueValue, 'overdueCount' => $overdueCount,
            'newOrders' => (int)($orderRow->new_orders ?? 0), 'doneOrders' => (int)($orderRow->done ?? 0),
            'pipeline' => $pipeline, 'trend' => $trend,
            'stockAlerts' => $stockAlerts, 'stockCriticalCount' => $stockCriticalCount,
            'overReserved' => $overReserved, 'ropUnset' => $ropUnset,
            'prPending' => $prPending, 'prValue' => $prValue, 'prCritical' => $prCritical, 'prAwaiting' => $prAwaiting,
            'recentPrs' => $recentPrs, 'receiptCount' => $receiptCount, 'recentReceipts' => $recentReceipts,
            'noBom' => $noBom, 'adjCount' => $adjCount, 'lastOpname' => $lastOpname,
            'expCount' => $expCount, 'expBatches' => $expBatches,
            'topCustomers' => $topCustomers, 'decisions' => $decisions,
        ]);
    }

    private function buildDecisions($prPending, $prCritical, $prValue, $overdueCount, $overdueValue, $stockCritical, $overRes, $noBom, $waitingInv, $ropUnset): array
    {
        $d = [];
        if ($prPending > 0) $d[] = ['sev' => $prCritical > 0 ? 0 : 1, 'icon' => 'approve', 'title' => 'Approve Purchase Request', 'detail' => $prCritical . ' kritis • est Rp ' . number_format($prValue, 0, ',', '.'), 'count' => $prPending, 'route' => route('admin.purchase-requests'), 'cta' => 'Approve'];
        if ($overdueCount > 0) $d[] = ['sev' => 0, 'icon' => 'clock', 'title' => 'Piutang Jatuh Tempo', 'detail' => 'Rp ' . number_format($overdueValue, 0, ',', '.') . ' perlu follow-up', 'count' => $overdueCount, 'route' => route('admin.orders') . '?status=unpaid', 'cta' => 'Follow-up'];
        if ($stockCritical > 0) $d[] = ['sev' => 0, 'icon' => 'box', 'title' => 'Stok Kritis / Habis', 'detail' => 'Bahan di bawah ROP', 'count' => $stockCritical, 'route' => route('admin.purchase-requests'), 'cta' => 'Buat PR'];
        if ($overRes > 0) $d[] = ['sev' => 1, 'icon' => 'alert', 'title' => 'Reservasi Melebihi Stok', 'detail' => 'Stok bersih negatif', 'count' => $overRes, 'route' => route('admin.materials'), 'cta' => 'Perbaiki'];
        if ($waitingInv > 0) $d[] = ['sev' => 1, 'icon' => 'invoice', 'title' => 'Menunggu Invoice', 'detail' => 'Pesanan belum ditagih', 'count' => $waitingInv, 'route' => route('admin.orders') . '?status=waiting_invoice', 'cta' => 'Terbitkan'];
        if ($noBom > 0) $d[] = ['sev' => 1, 'icon' => 'bom', 'title' => 'Layanan Tanpa BOM', 'detail' => 'MRP belum bisa hitung', 'count' => $noBom, 'route' => route('admin.service-boms'), 'cta' => 'Set BOM'];
        if ($ropUnset > 0) $d[] = ['sev' => 2, 'icon' => 'rop', 'title' => 'ROP Belum Diset', 'detail' => 'Minimum stok = 0', 'count' => $ropUnset, 'route' => route('admin.materials'), 'cta' => 'Set ROP'];
        usort($d, fn($a, $b) => $a['sev'] <=> $b['sev']);
        return array_slice($d, 0, 6);
    }

    private function servicesWithoutBom(): int
    {
        if (!Schema::hasTable('service_boms') || !Schema::hasColumn('service_boms', 'service_id')) return 0;
        return Service::where('is_active', true)->whereNotIn('id', DB::table('service_boms')->select('service_id'))->count();
    }

    private function expiryAlerts(): array
    {
        if (!Schema::hasTable('material_batches')) return [0, collect()];
        $cols = Schema::getColumnListing('material_batches');
        $expiryCol = collect(['expiry_date', 'expired_at', 'expires_at'])->first(fn($c) => in_array($c, $cols));
        if (!$expiryCol) return [0, collect()];
        $qtyCol = collect(['remaining_quantity', 'remaining', 'quantity', 'qty'])->first(fn($c) => in_array($c, $cols));
        $base = DB::table('material_batches as b')->join('materials as m', 'm.id', '=', 'b.material_id')
            ->whereNotNull("b.{$expiryCol}")->whereDate("b.{$expiryCol}", '<=', now()->addDays(30)->toDateString());
        if ($qtyCol) $base->where("b.{$qtyCol}", '>', 0);
        $count = (clone $base)->count();
        $rows = (clone $base)->orderBy("b.{$expiryCol}")->limit(4)
            ->get(['m.name', 'm.unit', DB::raw("b.{$expiryCol} as expiry_at"), DB::raw($qtyCol ? "b.{$qtyCol} as remaining" : "0 as remaining")])
            ->map(fn($r) => ['name' => $r->name, 'unit' => $r->unit, 'expiry' => $r->expiry_at, 'qty' => (float)$r->remaining]);
        return [$count, $rows];
    }
}