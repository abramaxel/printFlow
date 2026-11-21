<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use App\Models\Category;
use App\Models\Order;
use App\Models\PurchaseRequest;
use App\Models\PurchaseReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Dashboard Inventory')]
class InventoryDashboard extends Component
{
    public int $range = 30;
    public string $search = '';
    public string $categoryFilter = 'all';
    public string $statusFilter = 'all';
    public string $sort = 'urgency';
    public bool $showAll = false;

    private array $tables = [];

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin'], true)) {
            abort(403, 'Dashboard Inventory hanya dapat diakses owner.');
        }
        $r = (int) request('range', 30);
        if (in_array($r, [30, 60, 90], true)) $this->range = $r;
    }

    private function hasTable(string $t): bool
    {
        if (empty($this->tables)) {
            try {
                $list = Schema::getTableListing();
                $this->tables = array_map(fn($x) => str_contains($x, '.') ? substr($x, strrpos($x, '.') + 1) : $x, $list);
            } catch (\Throwable $e) {
                $this->tables = [];
            }
        }
        if (!empty($this->tables)) return in_array($t, $this->tables, true);
        return Schema::hasTable($t);
    }

    private function priceOf($m): float
    {
        foreach (['price_per_m2', 'price', 'unit_price'] as $k) {
            $v = (float) ($m->{$k} ?? 0);
            if ($v > 0) return $v;
        }
        return 0.0;
    }

    public function setRange(int $days): void
    {
        if (in_array($days, [30, 60, 90], true)) $this->range = $days;
    }

    public function setStatusFilter(string $v): void { $this->statusFilter = $v; $this->showAll = false; }
    public function toggleAll(): void { $this->showAll = !$this->showAll; }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = 'all';
        $this->statusFilter = 'all';
        $this->sort = 'urgency';
        $this->showAll = false;
    }

    public function render()
    {
        $since = now()->subDays($this->range - 1)->startOfDay();
        $sinceDate = $since->toDateString();

        $categories = Category::orderBy('name')->get(['id', 'name'])->keyBy('id');
        $materials = Material::orderBy('name')->get();

        $daily = [];
        for ($i = 0; $i < $this->range; $i++) {
            $d = $since->copy()->addDays($i)->toDateString();
            $daily[$d] = ['in' => 0.0, 'out' => 0.0, 'in_val' => 0.0, 'out_val' => 0.0];
        }
        $outByMaterial = [];
        $inQty = 0.0; $outQty = 0.0; $inVal = 0.0; $outVal = 0.0;
        $lastOut = [];

        if ($this->hasTable('material_movements')) {
            $rows = DB::table('material_movements')
                ->where('movement_date', '>=', $sinceDate)
                ->get(['material_id', 'movement_type', 'quantity', 'total_cost', 'movement_date']);

            foreach ($rows as $r) {
                $day = substr((string) $r->movement_date, 0, 10);
                $isOut = str_contains(strtolower((string) $r->movement_type), 'out');
                $q = (float) $r->quantity;
                $v = (float) $r->total_cost;
                if (isset($daily[$day])) {
                    $daily[$day][$isOut ? 'out' : 'in'] += $q;
                    $daily[$day][$isOut ? 'out_val' : 'in_val'] += $v;
                }
                if ($isOut) {
                    $outQty += $q; $outVal += $v;
                    $mid = (int) $r->material_id;
                    if (!isset($outByMaterial[$mid])) $outByMaterial[$mid] = ['qty' => 0.0, 'val' => 0.0];
                    $outByMaterial[$mid]['qty'] += $q;
                    $outByMaterial[$mid]['val'] += $v;
                } else {
                    $inQty += $q; $inVal += $v;
                }
            }

            $lastOut = DB::table('material_movements')
                ->where('movement_type', 'out')
                ->selectRaw('material_id, MAX(movement_date) as last_out')
                ->groupBy('material_id')
                ->pluck('last_out', 'material_id')
                ->all();
        }

        $demand = [];
        foreach (Order::with('items')->whereIn('status', ['paid', 'processing', 'qc'])->get() as $o) {
            foreach ($o->items as $it) {
                $mid = (int) $it->material_id;
                if ($mid <= 0) continue;
                $demand[$mid] = ($demand[$mid] ?? 0) + (float) ($it->area_m2 ?? 0);
            }
        }

        $pendingPrs = PurchaseRequest::with('material')->where('status', 'pending')
            ->orderByRaw("CASE WHEN urgency='critical' THEN 0 WHEN urgency='warning' THEN 1 ELSE 2 END")
            ->latest()->get();
        $approvedPrs = PurchaseRequest::with('material')->whereIn('status', ['approved', 'ordered'])->get();

        $expiring = [];
        $expireValue = 0.0;
        if ($this->hasTable('material_batches')) {
            $rows = DB::table('material_batches')
                ->where('status', 'active')
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<=', now()->addDays(30)->toDateString())
                ->orderBy('expiry_date')->limit(12)->get();
            foreach ($rows as $b) {
                $val = round((float) $b->remaining_quantity * (float) $b->unit_cost, 2);
                $expireValue += $val;
                $expiring[] = [
                    'batch' => (string) $b->batch_number,
                    'material_id' => (int) $b->material_id,
                    'qty' => (float) $b->remaining_quantity,
                    'value' => $val,
                    'expiry' => Carbon::parse($b->expiry_date)->format('d M Y'),
                    'days' => (int) now()->startOfDay()->diffInDays(Carbon::parse($b->expiry_date)->startOfDay(), false),
                    'supplier' => (string) ($b->supplier ?? ''),
                ];
            }
        }

        $monthCount = 0; $monthValue = 0.0; $topSuppliers = [];
        if ($this->hasTable('purchase_receipts')) {
            $agg = PurchaseReceipt::whereYear('receipt_date', now()->year)->whereMonth('receipt_date', now()->month)
                ->selectRaw('COUNT(*) as c, COALESCE(SUM(total_cost),0) as v')->first();
            $monthCount = (int) ($agg->c ?? 0);
            $monthValue = (float) ($agg->v ?? 0);

            if ($this->hasTable('suppliers')) {
                $topSuppliers = DB::table('purchase_receipts')
                    ->join('suppliers', 'suppliers.id', '=', 'purchase_receipts.supplier_id')
                    ->whereYear('purchase_receipts.receipt_date', now()->year)
                    ->whereMonth('purchase_receipts.receipt_date', now()->month)
                    ->selectRaw('suppliers.name, COUNT(*) as c, SUM(purchase_receipts.total_cost) as v')
                    ->groupBy('suppliers.name')->orderByDesc('v')->limit(4)->get()
                    ->map(fn($s) => ['name' => (string) $s->name, 'count' => (int) $s->c, 'value' => (float) $s->v])->all();
            }
        }

        $rows = [];
        $byCategory = [];
        $totalValue = 0.0; $netValue = 0.0; $reservedValue = 0.0;
        $critical = 0; $empty = 0; $low = 0; $over = 0; $noRop = 0; $noPrice = 0; $sku = 0;
        $restockCost = 0.0;

        foreach ($materials as $m) {
            $active = (bool) ($m->is_active ?? true);
            $stock = (float) $m->stock;
            $reserved = (float) $m->stock_reserved;
            $net = round($stock - $reserved, 4);
            $min = (float) ($m->minimum_stock ?? 0);
            $max = (float) ($m->maximum_stock ?? 0);
            $price = $this->priceOf($m);
            $value = round($stock * $price, 2);
            $catName = (string) ($categories[$m->category_id]->name ?? 'Tanpa divisi');

            if ($active) {
                $sku++;
                $totalValue += $value;
                $netValue += round($net * $price, 2);
                $reservedValue += round($reserved * $price, 2);
                if ($price <= 0 && $stock > 0) $noPrice++;
                if ($min <= 0) $noRop++;
                if (!isset($byCategory[$catName])) $byCategory[$catName] = 0.0;
                $byCategory[$catName] += $value;
            }

            if ($net <= 0) { $status = 'habis'; if ($active) $empty++; }
            elseif ($min > 0 && $net <= $min) { $status = 'kritis'; }
            elseif ($min > 0 && $net <= $min * 1.5) { $status = 'rendah'; if ($active) $low++; }
            elseif ($max > 0 && $net > $max) { $status = 'over'; if ($active) $over++; }
            else { $status = 'aman'; }
            if ($active && in_array($status, ['habis', 'kritis'], true)) $critical++;

            $target = $max > 0 ? $max : max($min * 2, $min + 1);
            $suggest = max(0, round($target - $net, 4));
            if ($suggest <= 0 && $min > 0) $suggest = $min;
            $suggestCost = round($suggest * $price, 2);
            if ($active && in_array($status, ['habis', 'kritis'], true)) $restockCost += $suggestCost;

            $coverage = $min > 0 ? max(0, min(100, (int) round(max(0, $net) / $min * 100))) : ($net > 0 ? 100 : 0);
            $lo = $lastOut[$m->id] ?? null;
            $idleDays = $lo ? (int) Carbon::parse($lo)->startOfDay()->diffInDays(now()->startOfDay()) : null;

            $rows[] = [
                'id' => (int) $m->id,
                'name' => (string) $m->name,
                'category' => $catName,
                'category_id' => $m->category_id,
                'unit' => (string) ($m->unit ?? 'm²'),
                'active' => $active,
                'stock' => $stock,
                'reserved' => $reserved,
                'net' => $net,
                'min' => $min,
                'max' => $max,
                'price' => $price,
                'value' => $value,
                'status' => $status,
                'coverage' => $coverage,
                'lead' => max(0, (int) ($m->lead_time_days ?? 3)),
                'suggest' => $suggest,
                'suggest_cost' => $suggestCost,
                'no_rop' => $min <= 0,
                'no_price' => $price <= 0,
                'demand' => round($demand[$m->id] ?? 0, 4),
                'used_qty' => round($outByMaterial[$m->id]['qty'] ?? 0, 4),
                'used_val' => round($outByMaterial[$m->id]['val'] ?? 0, 2),
                'idle_days' => $idleDays,
                'batch' => (bool) ($m->is_batch_tracked ?? false),
            ];
        }

        $pendingByMaterial = [];
        $pendingValue = 0.0;
        $pendingList = [];
        foreach ($pendingPrs as $pr) {
            $m = $pr->material;
            $price = $m ? $this->priceOf($m) : 0.0;
            $est = (float) $pr->estimated_cost > 0 ? (float) $pr->estimated_cost : round((float) $pr->requested_quantity * $price, 2);
            $pendingValue += $est;
            $pendingByMaterial[(int) $pr->material_id] = $pr->pr_number;
            $pendingList[] = [
                'id' => $pr->id,
                'number' => (string) $pr->pr_number,
                'material' => $m->name ?? 'Bahan dihapus',
                'qty' => (float) $pr->requested_quantity,
                'unit' => (string) ($pr->unit ?: ($m->unit ?? 'm²')),
                'urgency' => (string) $pr->urgency,
                'estimate' => $est,
                'needed' => $pr->needed_by_date?->format('d M Y'),
                'age' => $pr->created_at ? (int) $pr->created_at->diffInDays(now()) : 0,
            ];
        }
        $incomingValue = 0.0;
        foreach ($approvedPrs as $pr) {
            $m = $pr->material;
            $price = $m ? $this->priceOf($m) : 0.0;
            $incomingValue += (float) $pr->estimated_cost > 0 ? (float) $pr->estimated_cost : round((float) $pr->requested_quantity * $price, 2);
        }

        $restock = [];
        foreach ($rows as $r) {
            if (!$r['active'] || !in_array($r['status'], ['habis', 'kritis'], true)) continue;
            $r['pr_number'] = $pendingByMaterial[$r['id']] ?? null;
            $restock[] = $r;
        }
        usort($restock, fn($a, $b) => [$a['net'] <= 0 ? 0 : 1, $a['coverage'], -$b['suggest_cost']] <=> [$b['net'] <= 0 ? 0 : 1, $b['coverage'], -$a['suggest_cost']]);

        $movers = [];
        $moverSource = empty($outByMaterial) ? 'demand' : 'movement';
        foreach ($rows as $r) {
            $qty = $moverSource === 'movement' ? $r['used_qty'] : $r['demand'];
            if ($qty <= 0) continue;
            $movers[] = ['name' => $r['name'], 'unit' => $r['unit'], 'qty' => $qty, 'value' => $moverSource === 'movement' ? $r['used_val'] : round($qty * $r['price'], 2), 'net' => $r['net'], 'status' => $r['status']];
        }
        usort($movers, fn($a, $b) => $b['qty'] <=> $a['qty']);
        $movers = array_slice($movers, 0, 6);
        $moverMax = $movers ? max(array_column($movers, 'qty')) : 0;
        foreach ($movers as $i => $mv) $movers[$i]['share'] = $moverMax > 0 ? (int) round($mv['qty'] / $moverMax * 100) : 0;

        $idle = [];
        $idleValue = 0.0;
        foreach ($rows as $r) {
            if (!$r['active'] || $r['stock'] <= 0) continue;
            $isIdle = $r['idle_days'] === null ? empty($outByMaterial) : $r['idle_days'] >= $this->range;
            if (!$isIdle) continue;
            if ($r['demand'] > 0) continue;
            $idleValue += $r['value'];
            $idle[] = $r;
        }
        usort($idle, fn($a, $b) => $b['value'] <=> $a['value']);
        $idleTotal = count($idle);
        $idle = array_slice($idle, 0, 6);

        arsort($byCategory);
        $hues = ['#c8ec3f', '#38bdf8', '#fbbf24', '#f472b6', '#34d399', '#a78bfa', '#fb7185', '#22d3ee'];
        $composition = [];
        $ci = 0;
        $catMax = $byCategory ? max($byCategory) : 0;
        foreach ($byCategory as $name => $val) {
            $composition[] = [
                'name' => $name,
                'value' => round($val, 2),
                'share' => $totalValue > 0 ? round($val / $totalValue * 100, 1) : 0,
                'bar' => $catMax > 0 ? max(3, (int) round($val / $catMax * 100)) : 3,
                'hue' => $hues[$ci % count($hues)],
            ];
            $ci++;
            if ($ci >= 8) break;
        }

        $peak = 0.0;
        foreach ($daily as $d) $peak = max($peak, $d['in'], $d['out']);
        $series = [];
        foreach ($daily as $day => $d) {
            $series[] = [
                'day' => Carbon::parse($day)->format('d M'),
                'in' => round($d['in'], 2),
                'out' => round($d['out'], 2),
                'in_pct' => $peak > 0 ? max($d['in'] > 0 ? 3 : 0, (int) round($d['in'] / $peak * 100)) : 0,
                'out_pct' => $peak > 0 ? max($d['out'] > 0 ? 3 : 0, (int) round($d['out'] / $peak * 100)) : 0,
            ];
        }

        $term = mb_strtolower(trim($this->search));
        $catalog = array_values(array_filter($rows, function ($r) use ($term) {
            if ($this->statusFilter === 'critical' && !in_array($r['status'], ['habis', 'kritis'], true)) return false;
            if ($this->statusFilter === 'idle' && !($r['stock'] > 0 && $r['demand'] <= 0 && ($r['idle_days'] === null || $r['idle_days'] >= $this->range))) return false;
            if ($this->statusFilter === 'norop' && !$r['no_rop']) return false;
            if ($this->statusFilter === 'over' && $r['status'] !== 'over') return false;
            if ($this->categoryFilter !== 'all' && (string) $r['category_id'] !== (string) $this->categoryFilter) return false;
            if ($term === '') return true;
            return str_contains(mb_strtolower($r['name']), $term) || str_contains(mb_strtolower($r['category']), $term);
        }));

        usort($catalog, function ($a, $b) {
            if ($this->sort === 'value') return $b['value'] <=> $a['value'];
            if ($this->sort === 'name') return strcmp($a['name'], $b['name']);
            if ($this->sort === 'stock') return $a['net'] <=> $b['net'];
            $rank = ['habis' => 0, 'kritis' => 1, 'rendah' => 2, 'over' => 3, 'aman' => 4];
            return [$rank[$a['status']] ?? 9, $a['coverage'], $a['name']] <=> [$rank[$b['status']] ?? 9, $b['coverage'], $b['name']];
        });

        $catalogTotal = count($catalog);
        if (!$this->showAll) $catalog = array_slice($catalog, 0, 12);

        $insights = [];
        if ($critical > 0) {
            $insights[] = ['tone' => 'danger', 'title' => $critical . ' bahan perlu restock sekarang', 'body' => ($empty > 0 ? $empty . ' di antaranya habis total. ' : '') . 'Estimasi biaya pemulihan Rp ' . number_format($restockCost, 0, ',', '.') . '.', 'action' => 'restock'];
        }
        if ($idleTotal > 0 && $idleValue > 0) {
            $insights[] = ['tone' => 'warning', 'title' => 'Rp ' . number_format($idleValue, 0, ',', '.') . ' modal mengendap', 'body' => $idleTotal . ' bahan tidak terpakai ' . $this->range . ' hari dan tidak ada permintaan produksi aktif.', 'action' => 'idle'];
        }
        if ($expireValue > 0) {
            $insights[] = ['tone' => 'warning', 'title' => count($expiring) . ' batch mendekati kadaluarsa', 'body' => 'Nilai berisiko Rp ' . number_format($expireValue, 0, ',', '.') . ' dalam 30 hari. Prioritaskan pemakaian FIFO.', 'action' => 'batch'];
        }
        if ($noPrice > 0) {
            $insights[] = ['tone' => 'info', 'title' => $noPrice . ' bahan belum punya harga master', 'body' => 'Valuasi persediaan belum lengkap. Lengkapi harga agar nilai stok akurat.', 'action' => 'price'];
        }
        if ($noRop > 0) {
            $insights[] = ['tone' => 'info', 'title' => $noRop . ' bahan belum diatur batas minimum', 'body' => 'MRP tidak bisa memberi peringatan restock untuk bahan tersebut.', 'action' => 'norop'];
        }
        if (empty($insights)) {
            $insights[] = ['tone' => 'ok', 'title' => 'Gudang dalam kondisi sehat', 'body' => 'Semua bahan di atas batas minimum, tidak ada batch mendekati kadaluarsa, dan valuasi lengkap.', 'action' => null];
        }

        $verdict = $empty > 0 ? 'kritis' : (($critical > 0 || $expireValue > 0 || $noRop > 0 || $noPrice > 0) ? 'perhatian' : 'sehat');

        return view('livewire.admin.inventory-dashboard', [
            'stats' => [
                'value' => round($totalValue, 2),
                'net_value' => round($netValue, 2),
                'reserved_value' => round($reservedValue, 2),
                'sku' => $sku,
                'critical' => $critical,
                'empty' => $empty,
                'low' => $low,
                'over' => $over,
                'no_rop' => $noRop,
                'no_price' => $noPrice,
                'restock_cost' => round($restockCost, 2),
                'pending_pr' => count($pendingList),
                'pending_value' => round($pendingValue, 2),
                'incoming_pr' => count($approvedPrs),
                'incoming_value' => round($incomingValue, 2),
                'month_count' => $monthCount,
                'month_value' => round($monthValue, 2),
                'expire_value' => round($expireValue, 2),
                'idle_value' => round($idleValue, 2),
                'idle_total' => $idleTotal,
                'in_qty' => round($inQty, 2),
                'out_qty' => round($outQty, 2),
                'in_val' => round($inVal, 2),
                'out_val' => round($outVal, 2),
            ],
            'verdict' => $verdict,
            'insights' => $insights,
            'restock' => array_slice($restock, 0, 8),
            'restock_total' => count($restock),
            'pending' => array_slice($pendingList, 0, 6),
            'series' => $series,
            'peak' => $peak,
            'composition' => $composition,
            'movers' => $movers,
            'mover_source' => $moverSource,
            'idle' => $idle,
            'expiring' => $expiring,
            'suppliers' => $topSuppliers,
            'catalog' => $catalog,
            'catalog_total' => $catalogTotal,
            'categories' => $categories->map(fn($c) => ['id' => $c->id, 'name' => (string) $c->name])->values()->all(),
            'has_filter' => $term !== '' || $this->categoryFilter !== 'all' || $this->statusFilter !== 'all',
        ]);
    }
}