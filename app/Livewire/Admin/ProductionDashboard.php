<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\Material;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Dashboard Produksi')]
class ProductionDashboard extends Component
{
    public string $stage = 'all';
    public string $search = '';

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');

        if (!in_array($role, ['owner', 'super_admin', 'produksi', 'production'], true)) {
            $this->redirect(route('admin.dashboard'));
            return;
        }

        $s = (string) request('stage', '');
        if (in_array($s, ['paid', 'processing', 'qc', 'shipping'], true)) {
            $this->stage = $s;
        }
    }

    public function setStage(string $v): void
    {
        $this->stage = ($this->stage === $v) ? 'all' : $v;
    }

    public function clearFilters(): void
    {
        $this->stage = 'all';
        $this->search = '';
    }

    private function stages(): array
    {
        return [
            'paid'       => ['Siap Produksi', 'Menunggu ambil bahan & mulai cetak', 'signal', '#c8ec3f', 'printer'],
            'processing' => ['Sedang Dicetak', 'SPK aktif di mesin / pengerjaan', 'amber', '#fbbf24', 'bolt'],
            'qc'         => ['Quality Control', 'Menunggu pemeriksaan hasil cetak', 'violet', '#a78bfa', 'shield'],
            'shipping'   => ['Siap Kirim / Ambil', 'Lolos QC, menunggu diserahkan', 'sky', '#38bdf8', 'truck'],
        ];
    }

    public function render()
    {
        $meta = $this->stages();
        $role = (string) (auth()->user()->role ?? '');
        $isOwner = in_array($role, ['owner', 'super_admin'], true);

        $agg = Order::query()
            ->selectRaw("SUM(CASE WHEN status='paid' THEN 1 ELSE 0 END) as paid")
            ->selectRaw("SUM(CASE WHEN status='processing' THEN 1 ELSE 0 END) as processing")
            ->selectRaw("SUM(CASE WHEN status='qc' THEN 1 ELSE 0 END) as qc")
            ->selectRaw("SUM(CASE WHEN status='shipping' THEN 1 ELSE 0 END) as shipping")
            ->first();

        $counts = [
            'paid'       => (int) ($agg->paid ?? 0),
            'processing' => (int) ($agg->processing ?? 0),
            'qc'         => (int) ($agg->qc ?? 0),
            'shipping'   => (int) ($agg->shipping ?? 0),
        ];

        $doneToday = Order::where('status', 'completed')->whereDate('updated_at', today())->count();
        $pipeline  = array_sum($counts);

        $segments = [];
        foreach ($meta as $key => $m) {
            $segments[] = [
                'key'   => $key,
                'label' => $m[0],
                'count' => $counts[$key],
                'hue'   => $m[3],
                'share' => $pipeline > 0 ? round($counts[$key] / $pipeline * 100, 1) : 0,
            ];
        }

        $active = Order::with(['user', 'items.service'])
            ->whereIn('status', array_keys($meta))
            ->orderByRaw("CASE WHEN status='paid' THEN 0 WHEN status='processing' THEN 1 WHEN status='qc' THEN 2 ELSE 3 END")
            ->orderByRaw('deadline_date IS NULL, deadline_date ASC')
            ->latest()
            ->get();

        $rows = [];
        $attention = [];

        foreach ($active as $o) {
            $deadline = $o->deadline_date ? Carbon::parse($o->deadline_date)->startOfDay() : null;
            $days = $deadline ? (int) now()->startOfDay()->diffInDays($deadline, false) : null;
            $late = $days !== null && $days < 0;
            $soon = $days !== null && $days >= 0 && $days <= 2;

            $row = [
                'id'       => (int) $o->id,
                'invoice'  => (string) $o->invoice_number,
                'customer' => (string) ($o->user->name ?? 'Pelanggan'),
                'service'  => (string) ($o->items->first()?->service?->name ?? '—'),
                'items'    => $o->items->count(),
                'status'   => (string) $o->status,
                'stage'    => $meta[$o->status][0] ?? $o->status,
                'tone'     => $meta[$o->status][2] ?? 'signal',
                'created'  => $o->created_at?->format('d M Y'),
                'deadline' => $deadline?->format('d M Y'),
                'days'     => $days,
                'late'     => $late,
                'soon'     => $soon,
                'priority' => (string) ($o->priority ?? 'normal'),
            ];

            $rows[] = $row;
            if ($late || $soon) $attention[] = $row;
        }

        usort($attention, fn($a, $b) => [$a['days'] ?? 9999, $a['invoice']] <=> [$b['days'] ?? 9999, $b['invoice']]);
        $attention = array_slice($attention, 0, 5);

        $term = mb_strtolower(trim($this->search));
        $queue = array_values(array_filter($rows, function ($r) use ($term) {
            if ($this->stage !== 'all' && $r['status'] !== $this->stage) return false;
            if ($term === '') return true;

            return str_contains(mb_strtolower($r['invoice']), $term)
                || str_contains(mb_strtolower($r['customer']), $term)
                || str_contains(mb_strtolower($r['service']), $term);
        }));

        $queueTotal = count($queue);
        $queue = array_slice($queue, 0, 8);

        $criticalRaw = Material::where('is_active', true)
            ->whereRaw('(stock - stock_reserved) <= minimum_stock')
            ->orderByRaw('(stock - stock_reserved) ASC')
            ->limit(8)
            ->get();

        $prMap = collect();
        if ($criticalRaw->isNotEmpty()) {
            $prMap = PurchaseRequest::whereIn('material_id', $criticalRaw->pluck('id'))
                ->whereIn('status', ['pending', 'approved', 'ordered'])
                ->orderBy('id')
                ->get()
                ->keyBy('material_id');
        }

        $critical = [];
        $unrequested = 0;

        foreach ($criticalRaw as $m) {
            $net = round((float) $m->stock - (float) $m->stock_reserved, 2);
            $min = (float) ($m->minimum_stock ?? 0);
            $pr  = $prMap[$m->id] ?? null;
            $prState = $pr ? (string) $pr->status : null;
            if (!$pr) $unrequested++;

            $critical[] = [
                'id'        => (int) $m->id,
                'name'      => (string) $m->name,
                'unit'      => str_ireplace('m2', 'm²', (string) ($m->unit ?? 'm²')),
                'net'       => $net,
                'min'       => $min,
                'empty'     => $net <= 0,
                'coverage'  => $min > 0 ? max(0, min(100, (int) round(max(0, $net) / $min * 100))) : 0,
                'lead'      => max(0, (int) ($m->lead_time_days ?? 3)),
                'pr_state'  => $prState,
                'pr_number' => $pr?->pr_number,
            ];
        }

        $criticalTotal = Material::where('is_active', true)
            ->whereRaw('(stock - stock_reserved) <= minimum_stock')
            ->count();

        $pick = function (array $names) {
            foreach ($names as $n) {
                if (Route::has($n)) return $n;
            }
            return null;
        };

        $rBom    = $pick(['admin.boms', 'admin.service-boms', 'admin.bom']);
        $rOpname = $pick(['admin.stock-opname', 'admin.opname', 'admin.stock.opname']);

        $links = [];
        if (Route::has('admin.production')) $links[] = ['admin.production', 'Antrian Produksi', 'Ambil bahan & kerjakan SPK', 'printer'];
        if (Route::has('admin.production.requests')) $links[] = ['admin.production.requests', 'Ajukan Purchase Request', 'Bahan kurang & alert ROP', 'send'];
        if (Route::has('admin.goods-receipt')) $links[] = ['admin.goods-receipt', 'Penerimaan Barang', 'Catat bahan datang dari supplier', 'truck'];
        if ($rBom) $links[] = [$rBom, 'BOM Layanan', 'Resep bahan per layanan', 'flask'];
        if ($rOpname) $links[] = [$rOpname, 'Stock Opname', 'Cocokkan stok fisik & sistem', 'clipboard'];
        if ($isOwner && Route::has('admin.inventory')) $links[] = ['admin.inventory', 'Dashboard Inventory', 'Valuasi, batch, kadaluarsa', 'chart'];

        return view('livewire.admin.production-dashboard', [
            'counts'     => $counts,
            'segments'   => $segments,
            'stagesMeta' => $meta,
            'queue'      => $queue,
            'queueTotal' => $queueTotal,
            'attention'  => $attention,
            'critical'   => $critical,
            'links'      => $links,
            'stats'      => [
                'pipeline'    => $pipeline,
                'done_today'  => (int) $doneToday,
                'critical'    => $criticalTotal,
                'unrequested' => $unrequested,
                'late'        => count(array_filter($rows, fn($r) => $r['late'])),
                'soon'        => count(array_filter($rows, fn($r) => $r['soon'])),
            ],
            'hasFilter' => $this->stage !== 'all' || $this->search !== '',
            'today'     => now()->format('d M Y'),
        ]);
    }
}