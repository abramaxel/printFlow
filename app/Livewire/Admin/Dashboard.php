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
    public string $csQuery = '';

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');

        if (!in_array($role, ['owner', 'super_admin', 'admin', 'cs', 'produksi', 'production'], true)) {
            $this->redirect(route('customer.dashboard'));
            return;
        }
    }

    public function clearCsQuery(): void
    {
        $this->csQuery = '';
    }

    public function render()
    {
        return match (true) {
            $this->isProduction() => view('livewire.admin.production-dashboard-shell'),
            $this->isOwner()      => view('livewire.admin.owner-dashboard', $this->ownerData()),
            $this->isCS()         => view('livewire.admin.cs-dashboard', $this->csData()),
            default               => view('livewire.admin.dashboard', [
                'pipeline'       => $this->orderPipeline(),
                'lowStockAlerts' => $this->criticalStockAlerts(),
            ]),
        };
    }

    private function isOwner(): bool
    {
        return in_array((string) (auth()->user()->role ?? ''), ['owner', 'super_admin'], true);
    }

    private function isCS(): bool
    {
        return in_array((string) (auth()->user()->role ?? ''), ['cs', 'admin'], true);
    }

    private function isProduction(): bool
    {
        return in_array((string) (auth()->user()->role ?? ''), ['produksi', 'production'], true);
    }

    /* =========================================================
       DATA UNTUK OWNER
       ========================================================= */
    private function ownerData(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        $monthlyRevenue = (float) Payment::where('status', 'verified')
            ->whereBetween('verified_at', [$start, $end])
            ->sum('amount');

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
            'lowStockAlerts'     => $this->criticalStockAlerts(),
        ];
    }

    /* =========================================================
       DATA UNTUK CS / ADMIN STAFF
       ========================================================= */
    private function waOf($user): string
    {
        $d = preg_replace('/\D+/', '', (string) ($user->phone ?? '')) ?? '';
        if ($d === '') return '';
        if (str_starts_with($d, '0')) $d = '62' . substr($d, 1);
        elseif (str_starts_with($d, '8')) $d = '62' . $d;
        return $d;
    }

    private function dueOf($order): ?Carbon
    {
        $inv = $order->invoice;
        $raw = $inv?->due_date ?? $order->deadline_date;
        if (!$raw && $order->created_at) $raw = $order->created_at->copy()->addDays(7);
        return $raw ? Carbon::parse($raw)->startOfDay() : null;
    }

    private function csData(): array
    {
        $now          = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $brand        = (string) config('app.brand.name', config('app.name', 'FC Murni Jaya'));
        $term         = mb_strtolower(trim($this->csQuery));

        $match = function (array $r) use ($term) {
            if ($term === '') return true;
            return str_contains(mb_strtolower($r['invoice']), $term)
                || str_contains(mb_strtolower($r['customer']), $term)
                || str_contains(mb_strtolower($r['service'] ?? ''), $term);
        };

        /* ---- Menunggu invoice ---- */
        $waitingRaw = Order::with(['user', 'items.service'])
            ->where('status', 'waiting_invoice')->latest()->get();

        $waiting = [];
        $waitingValue = 0.0;
        foreach ($waitingRaw as $o) {
            $est = (float) $o->items->sum('final_subtotal');
            $waitingValue += $est;
            $waiting[] = [
                'id'       => (int) $o->id,
                'invoice'  => (string) $o->invoice_number,
                'customer' => (string) ($o->user->name ?? 'Pelanggan'),
                'wa'       => $this->waOf($o->user),
                'service'  => (string) ($o->items->first()?->service?->name ?? '—'),
                'items'    => $o->items->count(),
                'estimate' => round($est, 2),
                'created'  => $o->created_at?->format('d M Y, H:i'),
                'age_h'    => $o->created_at ? (int) $o->created_at->diffInHours($now) : 0,
            ];
        }
        $waitingCount = count($waiting);
        $waiting = array_values(array_filter($waiting, $match));
        usort($waiting, fn($a, $b) => $b['age_h'] <=> $a['age_h']);
        $waitingShown = array_slice($waiting, 0, 6);

        /* ---- Belum dibayar ---- */
        $unpaidRaw = Order::with(['user', 'invoice', 'items.service'])
            ->where('status', 'unpaid')->latest()->get();

        $unpaid = [];
        $receivable = 0.0;
        $overdueCount = 0;
        $overdueValue = 0.0;
        $aging = ['belum' => 0, 'd1_3' => 0, 'd4_7' => 0, 'd8' => 0];
        $agingVal = ['belum' => 0.0, 'd1_3' => 0.0, 'd4_7' => 0.0, 'd8' => 0.0];

        foreach ($unpaidRaw as $o) {
            $inv  = $o->invoice;
            $total = (float) ($inv->total_amount ?? 0);
            $paid  = (float) ($inv->total_paid ?? 0);
            $out   = max(0, round($total - $paid, 2));
            if ($out <= 0) continue;

            $due  = $this->dueOf($o);
            $over = $due ? (int) floor(max(0, $now->timestamp - $due->copy()->endOfDay()->timestamp) / 86400) : 0;
            $left = $due ? (int) ceil(max(0, $due->copy()->endOfDay()->timestamp - $now->timestamp) / 86400) : null;
            $key  = $over <= 0 ? 'belum' : ($over <= 3 ? 'd1_3' : ($over <= 7 ? 'd4_7' : 'd8'));

            $receivable += $out;
            if ($over > 0) { $overdueCount++; $overdueValue += $out; }
            $aging[$key]++;
            $agingVal[$key] += $out;

            $wa = $this->waOf($o->user);
            $payUrl = !empty($inv?->payment_token) ? route('payment.show', $inv->payment_token) : null;
            $msg = 'Halo ' . ($o->user->name ?? 'Bapak/Ibu') . ', kami ' . $brand . '. Invoice ' . $o->invoice_number
                . ' masih memiliki sisa tagihan Rp ' . number_format($out, 0, ',', '.')
                . ($due ? ' dengan jatuh tempo ' . $due->format('d M Y') : '')
                . '. Mohon diselesaikan agar pesanan dapat segera diproses.'
                . ($payUrl ? ' Link pembayaran: ' . $payUrl : '') . ' Terima kasih.';

            $unpaid[] = [
                'id'        => (int) $o->id,
                'invoice'   => (string) $o->invoice_number,
                'customer'  => (string) ($o->user->name ?? 'Pelanggan'),
                'service'   => (string) ($o->items->first()?->service?->name ?? '—'),
                'wa'        => $wa,
                'wa_msg'    => $msg,
                'pay_url'   => $payUrl,
                'total'     => $total,
                'paid'      => $paid,
                'outstanding' => $out,
                'due'       => $due?->format('d M Y'),
                'over'      => $over,
                'left'      => $left,
                'aging'     => $key,
            ];
        }
        $unpaidCount = count($unpaid);
        $unpaid = array_values(array_filter($unpaid, $match));
        usort($unpaid, fn($a, $b) => [$b['over'], $b['outstanding']] <=> [$a['over'], $a['outstanding']]);
        $unpaidShown = array_slice($unpaid, 0, 6);

        /* ---- Siap diserahkan ---- */
        $readyRaw = Order::with(['user', 'invoice'])
            ->where('status', 'shipping')->latest()->get();

        $ready = [];
        foreach ($readyRaw as $o) {
            $pickup = (string) ($o->shipping_method ?? 'pickup') === 'pickup';
            $wa = $this->waOf($o->user);
            $msg = 'Halo ' . ($o->user->name ?? 'Bapak/Ibu') . ', kabar baik dari ' . $brand . '. Pesanan ' . $o->invoice_number
                . ($pickup ? ' sudah selesai dan siap diambil di toko kami pada jam operasional.' : ' sudah siap dikirim ke alamat Anda.')
                . ' Terima kasih telah mempercayakan cetakan Anda kepada kami.';

            $ready[] = [
                'id'       => (int) $o->id,
                'invoice'  => (string) $o->invoice_number,
                'customer' => (string) ($o->user->name ?? 'Pelanggan'),
                'service'  => '',
                'wa'       => $wa,
                'wa_msg'   => $msg,
                'pickup'   => $pickup,
                'total'    => (float) ($o->invoice->total_amount ?? 0),
                'since'    => $o->updated_at?->format('d M Y'),
            ];
        }
        $readyCount = count($ready);
        $ready = array_values(array_filter($ready, $match));
        $readyShown = array_slice($ready, 0, 6);

        /* ---- Produksi & aktivitas ---- */
        $prod = Order::query()
            ->selectRaw("SUM(CASE WHEN status='paid' THEN 1 ELSE 0 END) as paid")
            ->selectRaw("SUM(CASE WHEN status='processing' THEN 1 ELSE 0 END) as processing")
            ->selectRaw("SUM(CASE WHEN status='qc' THEN 1 ELSE 0 END) as qc")
            ->first();

        $prodCounts = [
            'paid'       => (int) ($prod->paid ?? 0),
            'processing' => (int) ($prod->processing ?? 0),
            'qc'         => (int) ($prod->qc ?? 0),
        ];

        $maxAging = max(1, max($aging));
        $agingBars = [];
        foreach ([
            'belum' => ['Belum jatuh tempo', '#c8ec3f'],
            'd1_3'  => ['Lewat 1–3 hari', '#fbbf24'],
            'd4_7'  => ['Lewat 4–7 hari', '#fb923c'],
            'd8'    => ['Lewat > 7 hari', '#ef4444'],
        ] as $k => $meta) {
            $agingBars[] = [
                'key'   => $k,
                'label' => $meta[0],
                'hue'   => $meta[1],
                'count' => $aging[$k],
                'value' => round($agingVal[$k], 2),
                'share' => $unpaidCount > 0 ? round($aging[$k] / $unpaidCount * 100, 1) : 0,
                'bar'   => max(3, (int) round($aging[$k] / $maxAging * 100)),
            ];
        }

        return [
            'waitingInvoice'  => $waitingShown,
            'unpaidOrders'    => $unpaidShown,
            'readyOrders'     => $readyShown,
            'agingBars'       => $agingBars,
            'production'      => $prodCounts,
            'inProduction'    => array_sum($prodCounts),
            'myCreatedOrders' => Order::whereBetween('created_at', [$startOfMonth, $now])->count(),
            'stats' => [
                'waiting'        => $waitingCount,
                'waiting_value'  => round($waitingValue, 2),
                'unpaid'         => $unpaidCount,
                'receivable'     => round($receivable, 2),
                'overdue_count'  => $overdueCount,
                'overdue_value'  => round($overdueValue, 2),
                'ready'          => $readyCount,
                'today_orders'   => Order::whereDate('created_at', today())->count(),
                'today_invoices' => Invoice::whereDate('created_at', today())->count(),
                'shown_waiting'  => count($waitingShown),
                'shown_unpaid'   => count($unpaidShown),
                'shown_ready'    => count($readyShown),
            ],
            'csHasFilter' => $term !== '',
            'csToday'     => $now->format('d M Y'),
        ];
    }

    /* =========================================================
       HELPER BERSAMA
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
        $labels = [];
        $values = [];
        $counts = [];

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
                    'net_available' => $net,
                    'minimum_stock' => $minimum,
                    'status'        => $status,
                ];
            })
            ->filter(fn ($row) => $row['status'] !== 'safe')
            ->sortBy(fn ($row) => $row['net_available'])
            ->take(8)
            ->values()
            ->all();
    }
}