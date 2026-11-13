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

class OwnerOpsPanel extends Component
{
    public function mount(): void
    {
        $role = auth()->user()->role ?? '';
        if (!in_array($role, ['owner', 'super_admin'], true)) {
            abort(403, 'Panel ini hanya untuk Owner / Super Admin.');
        }
    }

    public function render()
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $today = $now->toDateString();

        // --- Purchasing: PR & penerimaan ---
        $prPending = PurchaseRequest::where('status', 'pending')->count();
        $prPendingValue = (float) PurchaseRequest::where('status', 'pending')->sum('estimated_cost');
        $prAwaitingReceipt = PurchaseRequest::whereIn('status', ['approved', 'ordered'])->count();
        $recentPrs = PurchaseRequest::with('material')
            ->where('status', 'pending')
            ->orderByRaw("CASE WHEN urgency = 'critical' THEN 0 ELSE 1 END")
            ->latest()->limit(5)->get();

        $receiptsMonth = PurchaseReceipt::whereBetween('receipt_date', [$monthStart->toDateString(), $today])->get();
        $purchaseMonth = (float) $receiptsMonth->sum('total_cost');
        $receiptCount = $receiptsMonth->count();
        $recentReceipts = PurchaseReceipt::with('supplier')->latest()->limit(5)->get();

        // --- Piutang jatuh tempo (exclude order batal) ---
        $overdue = Invoice::where('status', '!=', 'paid')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->with('order')
            ->get()
            ->filter(fn($i) => $i->order && $i->order->status !== 'cancelled');
        $overdueValue = (float) $overdue->sum(fn($i) => max(0, (float) $i->total_amount - (float) $i->total_paid));
        $overdueCount = $overdue->count();

        // --- FIFO / kadaluarsa batch ---
        [$expCount, $expQty, $expBatches] = $this->expiryAlerts();

        // --- Integritas & kepatuhan ---
        $overReserved = Material::where('is_active', true)
            ->whereRaw('(stock - stock_reserved) < 0')
            ->orderByRaw('(stock - stock_reserved) ASC')
            ->limit(3)->get();
        $ropUnset = Material::where('is_active', true)->where('minimum_stock', 0)->count();
        $noBom = $this->servicesWithoutBom();
        $adjMonth = MaterialMovement::where('movement_type', 'adjustment')
            ->whereBetween('movement_date', [$monthStart, $now])->count();
        $lastOpname = MaterialMovement::where('movement_type', 'adjustment')->max('movement_date');

        // --- CRM: top pelanggan bulan ini ---
        $topCustomers = User::where('role', 'customer')
            ->addSelect([
                'paid_month' => Payment::selectRaw('COALESCE(SUM(payments.amount),0)')
                    ->where('payments.status', 'verified')
                    ->whereBetween('payments.verified_at', [$monthStart, $now])
                    ->whereIn('payments.invoice_id', Invoice::select('id')
                        ->whereIn('invoices.order_id', Order::select('id')->whereColumn('orders.user_id', 'users.id'))),
            ])
            ->orderByDesc('paid_month')
            ->limit(3)->get();

        return view('livewire.admin.owner-ops-panel', [
            'prPending' => $prPending,
            'prPendingValue' => $prPendingValue,
            'prAwaitingReceipt' => $prAwaitingReceipt,
            'recentPrs' => $recentPrs,
            'purchaseMonth' => $purchaseMonth,
            'receiptCount' => $receiptCount,
            'recentReceipts' => $recentReceipts,
            'overdueValue' => $overdueValue,
            'overdueCount' => $overdueCount,
            'expCount' => $expCount,
            'expQty' => $expQty,
            'expBatches' => $expBatches,
            'overReserved' => $overReserved,
            'ropUnset' => $ropUnset,
            'noBom' => $noBom,
            'adjMonth' => $adjMonth,
            'lastOpname' => $lastOpname,
            'topCustomers' => $topCustomers,
        ]);
    }

    /**
     * Alert batch kadaluarsa <= 30 hari. Defensif: cek tabel/kolom dulu.
     */
    private function expiryAlerts(): array
    {
        if (!Schema::hasTable('material_batches')) {
            return [0, 0.0, collect()];
        }
        $cols = Schema::getColumnListing('material_batches');
        $expiryCol = collect(['expiry_date', 'expired_at', 'expires_at'])->first(fn($c) => in_array($c, $cols));
        if (!$expiryCol) {
            return [0, 0.0, collect()];
        }
        $qtyCol = collect(['remaining_quantity', 'remaining', 'quantity', 'qty'])->first(fn($c) => in_array($c, $cols));

        $base = DB::table('material_batches as b')
            ->join('materials as m', 'm.id', '=', 'b.material_id')
            ->whereNotNull("b.{$expiryCol}")
            ->whereDate("b.{$expiryCol}", '<=', now()->addDays(30)->toDateString());
        if ($qtyCol) {
            $base->where("b.{$qtyCol}", '>', 0);
        }

        $count = (clone $base)->count();
        $qty = $qtyCol ? (float) (clone $base)->sum("b.{$qtyCol}") : 0.0;

        $rows = (clone $base)
            ->orderBy("b.{$expiryCol}")
            ->limit(5)
            ->get(['m.name', 'm.unit', DB::raw("b.{$expiryCol} as expiry_at"), DB::raw($qtyCol ? "b.{$qtyCol} as remaining" : "0 as remaining")])
            ->map(fn($r) => [
                'name' => $r->name,
                'unit' => $r->unit,
                'expiry' => $r->expiry_at,
                'qty' => (float) $r->remaining,
            ]);

        return [$count, $qty, $rows];
    }

    /**
     * Layanan aktif yang belum punya BOM (MRP tidak bisa meledakkan kebutuhan).
     */
    private function servicesWithoutBom(): int
    {
        if (!Schema::hasTable('service_boms') || !Schema::hasColumn('service_boms', 'service_id')) {
            return 0;
        }
        return Service::where('is_active', true)
            ->whereNotIn('id', DB::table('service_boms')->select('service_id'))
            ->count();
    }
}