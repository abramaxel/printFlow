<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Stock Opname')]
class StockOpname extends Component
{
    public ?int $sessionId = null;
    public array $items = [];
    public string $sessionNotes = '';

    public string $search = '';
    public string $rowFilter = 'all';
    public string $addQuery = '';
    public array $addMatches = [];
    public string $startCategory = '';

    public ?int $detailId = null;
    public array $moveCols = [];

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'produksi', 'production'], true)) {
            abort(403, 'Stock opname hanya untuk tim gudang/produksi dan owner.');
        }

        $this->moveCols = Schema::hasTable('material_movements') ? Schema::getColumnListing('material_movements') : [];

        $draft = DB::table('stock_opnames')->where('status', 'draft')->latest('id')->first();
        if ($draft) $this->openSession($draft->id);
    }

    private function priceOf($m): float
    {
        foreach (['price_per_m2', 'price', 'unit_price'] as $k) {
            $v = (float) ($m->{$k} ?? 0);
            if ($v > 0) return $v;
        }
        return 0.0;
    }

    public function reasons(): array
    {
        return [
            'miscount'  => 'Salah catat / salah input sebelumnya',
            'damaged'   => 'Bahan rusak / cacat',
            'expired'   => 'Kadaluarsa / tidak layak pakai',
            'lost'      => 'Hilang / tidak ditemukan',
            'shrinkage' => 'Susut alami / potongan produksi',
            'unrecorded'=> 'Pemakaian belum tercatat',
            'surplus'   => 'Kelebihan penerimaan / bonus supplier',
            'other'     => 'Sebab lain (jelaskan di catatan)',
        ];
    }

    private function nextNumber(): string
    {
        $last = DB::table('stock_opnames')->latest('id')->first();
        $next = $last ? ((int) substr((string) $last->opname_number, -4)) + 1 : 1;
        return 'OPN-' . now()->format('Ymd') . '-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function startSession(string $scope): void
    {
        $existing = DB::table('stock_opnames')->where('status', 'draft')->latest('id')->first();
        if ($existing) {
            $this->openSession($existing->id);
            session()->flash('op_warning', 'Ada lembar opname draft yang belum diposting. Dilanjutkan dulu.');
            return;
        }

        $query = Material::where('is_active', true);
        $catId = null;

        if ($scope === 'critical') {
            $query->whereRaw('(stock - stock_reserved) <= minimum_stock');
        } elseif ($scope === 'category') {
            $catId = (int) $this->startCategory ?: null;
            if (!$catId) {
                session()->flash('op_warning', 'Pilih divisi dulu sebelum memulai opname per divisi.');
                return;
            }
            $query->where('category_id', $catId);
        }

        $materials = $scope === 'manual' ? collect() : $query->orderBy('name')->get();

        $id = DB::transaction(function () use ($scope, $catId, $materials) {
            $sid = DB::table('stock_opnames')->insertGetId([
                'opname_number' => $this->nextNumber(),
                'status' => 'draft',
                'scope' => $scope,
                'category_id' => $catId,
                'counted_by' => auth()->id(),
                'items_count' => $materials->count(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($materials as $m) {
                DB::table('stock_opname_items')->insert([
                    'stock_opname_id' => $sid,
                    'material_id' => $m->id,
                    'system_qty' => round((float) $m->stock, 4),
                    'reserved_qty' => round((float) $m->stock_reserved, 4),
                    'physical_qty' => null,
                    'variance_qty' => 0,
                    'unit_cost' => $this->priceOf($m),
                    'variance_value' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $sid;
        });

        $this->openSession($id);
        session()->flash('op_success', 'Lembar opname dibuat. Mulai hitung fisik bahan.');
    }

    public function openSession($id): void
    {
        $s = DB::table('stock_opnames')->find($id);
        if (!$s) return;

        $this->sessionId = (int) $s->id;
        $this->sessionNotes = (string) ($s->notes ?? '');
        $this->addQuery = '';
        $this->addMatches = [];
        $this->rowFilter = 'all';
        $this->search = '';
        $this->loadItems();
    }

    private function loadItems(): void
    {
        $this->items = [];
        if (!$this->sessionId) return;

        $rows = DB::table('stock_opname_items')->where('stock_opname_id', $this->sessionId)->orderBy('id')->get();
        $materials = Material::with([])->whereIn('id', $rows->pluck('material_id')->all())->get()->keyBy('id');
        $cats = Category::pluck('name', 'id');

        foreach ($rows as $r) {
            $m = $materials[$r->material_id] ?? null;
            $this->items[] = [
                'id' => (int) $r->id,
                'material_id' => (int) $r->material_id,
                'name' => (string) ($m->name ?? 'Bahan dihapus'),
                'category' => (string) ($cats[$m->category_id] ?? 'Tanpa divisi'),
                'unit' => (string) ($m->unit ?? 'm²'),
                'system_qty' => (float) $r->system_qty,
                'reserved_qty' => (float) $r->reserved_qty,
                'physical_qty' => $r->physical_qty === null ? '' : (string) (float) $r->physical_qty,
                'variance_qty' => (float) $r->variance_qty,
                'unit_cost' => (float) $r->unit_cost,
                'variance_value' => (float) $r->variance_value,
                'reason' => (string) ($r->reason ?? ''),
                'notes' => (string) ($r->notes ?? ''),
                'live_stock' => (float) ($m->stock ?? 0),
            ];
        }
    }

    public function closeWorkspace(): void
    {
        $this->sessionId = null;
        $this->items = [];
        $this->sessionNotes = '';
    }

    public function updatedItems($value, $key): void
    {
        $parts = explode('.', (string) $key);
        $i = (int) ($parts[0] ?? -1);
        $field = $parts[1] ?? '';
        if (!isset($this->items[$i])) return;

        $it = &$this->items[$i];

        if ($field === 'physical_qty') {
            if ($value === '' || $value === null) {
                $it['physical_qty'] = '';
                $it['variance_qty'] = 0.0;
                $it['variance_value'] = 0.0;
                DB::table('stock_opname_items')->where('id', $it['id'])->update([
                    'physical_qty' => null, 'variance_qty' => 0, 'variance_value' => 0, 'updated_at' => now(),
                ]);
            } else {
                $phys = max(0, round((float) str_replace(',', '.', (string) $value), 4));
                $var = round($phys - $it['system_qty'], 4);
                $val = round($var * $it['unit_cost'], 2);
                $it['physical_qty'] = (string) $phys;
                $it['variance_qty'] = $var;
                $it['variance_value'] = $val;
                if ($var == 0.0) $it['reason'] = '';
                DB::table('stock_opname_items')->where('id', $it['id'])->update([
                    'physical_qty' => $phys,
                    'variance_qty' => $var,
                    'variance_value' => $val,
                    'reason' => $var == 0.0 ? null : ($it['reason'] ?: null),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($field === 'reason') {
            $it['reason'] = (string) $value;
            DB::table('stock_opname_items')->where('id', $it['id'])->update(['reason' => $value ?: null, 'updated_at' => now()]);
        }

        if ($field === 'notes') {
            $it['notes'] = mb_substr((string) $value, 0, 255);
            DB::table('stock_opname_items')->where('id', $it['id'])->update(['notes' => $it['notes'] ?: null, 'updated_at' => now()]);
        }

        $this->refreshTotals();
    }

    public function fillMatch(int $index): void
    {
        if (!isset($this->items[$index])) return;
        $this->items[$index]['physical_qty'] = (string) $this->items[$index]['system_qty'];
        $this->updatedItems($this->items[$index]['physical_qty'], $index . '.physical_qty');
    }

    public function fillAllMatch(): void
    {
        foreach ($this->items as $i => $it) {
            if ($it['physical_qty'] === '') $this->fillMatch($i);
        }
        session()->flash('op_success', 'Semua baris kosong diisi sama dengan stok sistem. Periksa ulang sebelum posting.');
    }

    public function removeItem(int $index): void
    {
        if (!isset($this->items[$index])) return;
        DB::table('stock_opname_items')->where('id', $this->items[$index]['id'])->delete();
        $this->loadItems();
        $this->refreshTotals();
    }

    public function updatedAddQuery($value): void
    {
        $this->addMatches = [];
        $q = trim((string) $value);
        if (mb_strlen($q) < 2) return;

        $used = array_column($this->items, 'material_id');
        $like = '%' . mb_strtolower($q) . '%';

        $this->addMatches = Material::where('is_active', true)
            ->whereRaw('LOWER(name) LIKE ?', [$like])
            ->whereNotIn('id', $used ?: [0])
            ->orderBy('name')->limit(6)->get()
            ->map(fn($m) => [
                'id' => $m->id, 'name' => (string) $m->name, 'unit' => (string) ($m->unit ?? 'm²'),
                'stock' => (float) $m->stock,
            ])->all();
    }

    public function addMaterial($id): void
    {
        if (!$this->sessionId) return;
        $m = Material::find($id);
        if (!$m) return;

        $exists = DB::table('stock_opname_items')->where('stock_opname_id', $this->sessionId)->where('material_id', $m->id)->exists();
        if ($exists) {
            session()->flash('op_warning', 'Bahan itu sudah ada di lembar opname.');
            return;
        }

        DB::table('stock_opname_items')->insert([
            'stock_opname_id' => $this->sessionId,
            'material_id' => $m->id,
            'system_qty' => round((float) $m->stock, 4),
            'reserved_qty' => round((float) $m->stock_reserved, 4),
            'unit_cost' => $this->priceOf($m),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->addQuery = '';
        $this->addMatches = [];
        $this->loadItems();
        $this->refreshTotals();
    }

    public function saveNotes(): void
    {
        if (!$this->sessionId) return;
        DB::table('stock_opnames')->where('id', $this->sessionId)->update([
            'notes' => mb_substr(trim($this->sessionNotes), 0, 500) ?: null,
            'updated_at' => now(),
        ]);
        session()->flash('op_success', 'Catatan lembar opname disimpan.');
    }

    private function refreshTotals(): void
    {
        if (!$this->sessionId) return;

        $rows = DB::table('stock_opname_items')->where('stock_opname_id', $this->sessionId)->get();
        $surplus = 0.0; $shrink = 0.0; $varCount = 0;
        foreach ($rows as $r) {
            $v = (float) $r->variance_value;
            if ((float) $r->variance_qty > 0) $surplus += $v;
            if ((float) $r->variance_qty < 0) $shrink += abs($v);
            if ((float) $r->variance_qty != 0.0) $varCount++;
        }

        DB::table('stock_opnames')->where('id', $this->sessionId)->update([
            'items_count' => $rows->count(),
            'variance_count' => $varCount,
            'surplus_value' => round($surplus, 2),
            'shrinkage_value' => round($shrink, 2),
            'updated_at' => now(),
        ]);
    }

    public function post(): void
    {
        if (!$this->sessionId) return;

        $session = DB::table('stock_opnames')->where('status', 'draft')->find($this->sessionId);
        if (!$session) {
            session()->flash('op_error', 'Lembar opname sudah diposting atau dibatalkan.');
            return;
        }

        $rows = DB::table('stock_opname_items')->where('stock_opname_id', $this->sessionId)->get();
        if ($rows->isEmpty()) {
            session()->flash('op_error', 'Lembar opname masih kosong. Tambahkan bahan dulu.');
            return;
        }

        foreach ($rows as $r) {
            if ($r->physical_qty === null) {
                session()->flash('op_error', 'Masih ada bahan yang belum dihitung fisik. Lengkapi semua baris.');
                $this->rowFilter = 'uncounted';
                return;
            }
            if ((float) $r->variance_qty != 0.0 && empty($r->reason)) {
                session()->flash('op_error', 'Setiap selisih wajib punya alasan. Periksa baris bertanda selisih.');
                $this->rowFilter = 'variance';
                return;
            }
            if ((float) $r->variance_qty != 0.0 && $r->reason === 'other' && empty($r->notes)) {
                session()->flash('op_error', 'Alasan "Sebab lain" wajib dijelaskan pada catatan baris.');
                $this->rowFilter = 'variance';
                return;
            }
        }

        $reasons = $this->reasons();

        try {
            DB::transaction(function () use ($rows, $session, $reasons) {
                foreach ($rows as $r) {
                    $var = (float) $r->variance_qty;
                    if ($var == 0.0) continue;

                    $m = Material::whereKey($r->material_id)->lockForUpdate()->first();
                    if (!$m) continue;

                    $phys = max(0, (float) $r->physical_qty);
                    $reserved = (float) $m->stock_reserved;
                    $clampNote = '';
                    if ($reserved > $phys) {
                        $clampNote = ' Alokasi produksi disesuaikan dari ' . number_format($reserved, 2) . ' menjadi ' . number_format($phys, 2) . '.';
                        $reserved = $phys;
                    }

                    $m->stock = $phys;
                    $m->stock_reserved = $reserved;
                    $m->save();

                    if (!empty($this->moveCols)) {
                        $qty = abs($var);
                        $cost = (float) $r->unit_cost;
                        $label = $reasons[$r->reason] ?? 'Penyesuaian opname';
                        $note = '[OPNAME ' . $session->opname_number . '] ' . $label
                            . ' — sistem ' . number_format((float) $r->system_qty, 2)
                            . ' → fisik ' . number_format($phys, 2) . ' ' . ($m->unit ?? '')
                            . ($r->notes ? ' · ' . $r->notes : '') . $clampNote;

                        $map = [
                            'material_id' => $m->id,
                            'movement_type' => $var > 0 ? 'in' : 'out',
                            'sub_type' => 'opname',
                            'quantity' => $qty,
                            'unit' => $m->unit ?? 'm2',
                            'unit_cost' => $cost,
                            'total_cost' => round($qty * $cost, 2),
                            'movement_date' => now()->toDateString(),
                            'reference_type' => 'stock_opname',
                            'reference_id' => $session->id,
                            'notes' => mb_substr($note, 0, 500),
                            'user_id' => auth()->id(),
                        ];
                        $data = [];
                        foreach ($map as $k => $v) {
                            if (in_array($k, $this->moveCols, true) && !is_null($v)) $data[$k] = $v;
                        }
                        if (in_array('created_at', $this->moveCols, true)) $data['created_at'] = now();
                        if (in_array('updated_at', $this->moveCols, true)) $data['updated_at'] = now();
                        if (isset($data['material_id'])) DB::table('material_movements')->insert($data);
                    }
                }

                $this->refreshTotals();

                DB::table('stock_opnames')->where('id', $session->id)->update([
                    'status' => 'posted',
                    'posted_by' => auth()->id(),
                    'posted_at' => now(),
                    'notes' => mb_substr(trim($this->sessionNotes), 0, 500) ?: $session->notes,
                    'updated_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            session()->flash('op_error', 'Gagal posting: ' . $e->getMessage());
            return;
        }

        $number = $session->opname_number;
        $this->closeWorkspace();
        session()->flash('op_success', 'Opname ' . $number . ' diposting. Stok diperbarui dan koreksi tercatat di audit trail.');
    }

    public function cancelSession(): void
    {
        if (!$this->sessionId) return;
        DB::table('stock_opnames')->where('id', $this->sessionId)->where('status', 'draft')->update([
            'status' => 'cancelled', 'updated_at' => now(),
        ]);
        $this->closeWorkspace();
        session()->flash('op_success', 'Lembar opname dibatalkan. Stok tidak diubah.');
    }

    public function reopenSession($id): void
    {
        $s = DB::table('stock_opnames')->where('status', 'cancelled')->find($id);
        if (!$s) return;
        DB::table('stock_opnames')->where('id', $id)->update(['status' => 'draft', 'updated_at' => now()]);
        $this->openSession($id);
    }

    public function deleteSession($id): void
    {
        $s = DB::table('stock_opnames')->whereIn('status', ['draft', 'cancelled'])->find($id);
        if (!$s) return;

        DB::transaction(function () use ($id) {
            DB::table('stock_opname_items')->where('stock_opname_id', $id)->delete();
            DB::table('stock_opnames')->where('id', $id)->delete();
        });

        if ($this->sessionId === (int) $id) $this->closeWorkspace();
        session()->flash('op_success', 'Lembar opname dihapus permanen.');
    }

    public function openDetail($id): void { $this->detailId = (int) $id; }
    public function closeDetail(): void { $this->detailId = null; }
    public function setRowFilter(string $v): void { $this->rowFilter = $v; }

    public function render()
    {
        $active = null;
        $rows = [];
        $totals = ['items' => 0, 'counted' => 0, 'match' => 0, 'surplus' => 0, 'shrink' => 0, 'surplus_value' => 0.0, 'shrink_value' => 0.0, 'net_value' => 0.0, 'ready' => false, 'missing_reason' => 0];

        if ($this->sessionId) {
            $s = DB::table('stock_opnames')->find($this->sessionId);
            if ($s) {
                $active = [
                    'id' => (int) $s->id,
                    'number' => (string) $s->opname_number,
                    'status' => (string) $s->status,
                    'scope' => (string) $s->scope,
                    'created' => Carbon::parse($s->created_at)->format('d M Y, H:i'),
                    'counter' => (string) (DB::table('users')->where('id', $s->counted_by)->value('name') ?? '—'),
                ];

                foreach ($this->items as $i => $it) {
                    $counted = $it['physical_qty'] !== '';
                    $var = (float) $it['variance_qty'];
                    $state = !$counted ? 'uncounted' : ($var > 0 ? 'surplus' : ($var < 0 ? 'shrink' : 'match'));

                    $totals['items']++;
                    if ($counted) $totals['counted']++;
                    if ($state === 'match') $totals['match']++;
                    if ($state === 'surplus') { $totals['surplus']++; $totals['surplus_value'] += (float) $it['variance_value']; }
                    if ($state === 'shrink') { $totals['shrink']++; $totals['shrink_value'] += abs((float) $it['variance_value']); }
                    if ($counted && $var != 0.0 && empty($it['reason'])) $totals['missing_reason']++;

                    $rows[] = $it + ['index' => $i, 'state' => $state, 'counted' => $counted];
                }

                $totals['net_value'] = round($totals['surplus_value'] - $totals['shrink_value'], 2);
                $totals['ready'] = $totals['items'] > 0
                    && $totals['counted'] === $totals['items']
                    && $totals['missing_reason'] === 0;
            }
        }

        $term = mb_strtolower(trim($this->search));
        $visible = array_values(array_filter($rows, function ($r) use ($term) {
            if ($this->rowFilter === 'uncounted' && $r['counted']) return false;
            if ($this->rowFilter === 'variance' && $r['variance_qty'] == 0.0) return false;
            if ($this->rowFilter === 'match' && $r['state'] !== 'match') return false;
            if ($term === '') return true;
            return str_contains(mb_strtolower($r['name']), $term) || str_contains(mb_strtolower($r['category']), $term);
        }));

        $history = [];
        $sessions = DB::table('stock_opnames')->latest('id')->limit(12)->get();
        foreach ($sessions as $s) {
            $history[] = [
                'id' => (int) $s->id,
                'number' => (string) $s->opname_number,
                'status' => (string) $s->status,
                'scope' => (string) $s->scope,
                'items' => (int) $s->items_count,
                'variance' => (int) $s->variance_count,
                'surplus' => (float) $s->surplus_value,
                'shrink' => (float) $s->shrinkage_value,
                'created' => Carbon::parse($s->created_at)->format('d M Y, H:i'),
                'posted' => $s->posted_at ? Carbon::parse($s->posted_at)->format('d M Y, H:i') : null,
                'poster' => $s->posted_by ? (string) (DB::table('users')->where('id', $s->posted_by)->value('name') ?? '—') : null,
                'notes' => (string) ($s->notes ?? ''),
            ];
        }

        $detail = null;
        if ($this->detailId) {
            $s = DB::table('stock_opnames')->find($this->detailId);
            if ($s) {
                $items = DB::table('stock_opname_items')->where('stock_opname_id', $s->id)->orderBy('id')->get();
                $mats = Material::whereIn('id', $items->pluck('material_id')->all())->get()->keyBy('id');
                $reasons = $this->reasons();
                $detail = [
                    'number' => (string) $s->opname_number,
                    'status' => (string) $s->status,
                    'created' => Carbon::parse($s->created_at)->format('d M Y, H:i'),
                    'posted' => $s->posted_at ? Carbon::parse($s->posted_at)->format('d M Y, H:i') : null,
                    'notes' => (string) ($s->notes ?? ''),
                    'rows' => $items->map(function ($r) use ($mats, $reasons) {
                        $m = $mats[$r->material_id] ?? null;
                        return [
                            'name' => (string) ($m->name ?? 'Bahan dihapus'),
                            'unit' => (string) ($m->unit ?? 'm²'),
                            'system' => (float) $r->system_qty,
                            'physical' => $r->physical_qty === null ? null : (float) $r->physical_qty,
                            'variance' => (float) $r->variance_qty,
                            'value' => (float) $r->variance_value,
                            'reason' => $r->reason ? ($reasons[$r->reason] ?? $r->reason) : null,
                            'notes' => (string) ($r->notes ?? ''),
                        ];
                    })->all(),
                ];
            }
        }

        $monthPosted = DB::table('stock_opnames')->where('status', 'posted')
            ->whereYear('posted_at', now()->year)->whereMonth('posted_at', now()->month)->count();
        $draftCount = DB::table('stock_opnames')->where('status', 'draft')->count();

        return view('livewire.admin.stock-opname', [
            'active' => $active,
            'rows' => $visible,
            'all_rows' => $rows,
            'totals' => $totals,
            'history' => $history,
            'detail' => $detail,
            'reasons' => $this->reasons(),
            'categories' => Category::orderBy('name')->get(['id', 'name'])->map(fn($c) => ['id' => $c->id, 'name' => (string) $c->name])->all(),
            'stats' => [
                'materials' => Material::where('is_active', true)->count(),
                'draft' => $draftCount,
                'month_posted' => (int) $monthPosted,
            ],
        ]);
    }
}