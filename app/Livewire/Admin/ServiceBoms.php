<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Service;
use App\Models\Material;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Layout('components.layouts.admin')]
#[Title('Kelola BOM Layanan')]
class ServiceBoms extends Component
{
    public string $search = '';
    public string $categoryFilter = 'all';
    public bool $onlyMissing = false;
    public string $sort = 'name';

    public ?int $serviceId = null;
    public array $items = [];
    public string $bomNotes = '';
    public bool $bomActive = true;
    public int $bomVersion = 0;
    public bool $bomExists = false;

    public string $simArea = '1';
    public string $simQty = '1';

    public string $materialQuery = '';
    public array $materialMatches = [];

    public bool $showCopy = false;
    public string $copyQuery = '';
    public ?int $copyTargetId = null;

    public array $schema = ['ready' => false];

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'produksi', 'production'], true)) {
            abort(403, 'Kelola BOM hanya untuk tim produksi/gudang dan owner.');
        }

        $this->resolveSchema();

        if ($sid = (int) request('service', 0)) {
            $this->selectService($sid);
        }
    }

    private function pick(array $cols, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if (in_array($c, $cols, true)) return $c;
        }
        return null;
    }

    private function resolveSchema(): void
    {
        if (!Schema::hasTable('service_boms') || !Schema::hasTable('service_bom_items')) {
            $this->schema = ['ready' => false];
            return;
        }

        $b = Schema::getColumnListing('service_boms');
        $i = Schema::getColumnListing('service_bom_items');

        $qty = $this->pick($i, ['quantity_per_unit', 'quantity_per_m2', 'quantity', 'usage_per_unit', 'qty_per_unit']);
        $fk  = $this->pick($i, ['service_bom_id', 'bom_id']);

        if (!$qty || !$fk || !in_array('service_id', $b, true)) {
            $this->schema = ['ready' => false];
            return;
        }

        $this->schema = [
            'ready'    => true,
            'fk'       => $fk,
            'qty'      => $qty,
            'scrap'    => $this->pick($i, ['scrap_percentage', 'scrap_allowance', 'scrap_percent', 'waste_percentage']),
            'unit'     => $this->pick($i, ['unit']),
            'notes'    => $this->pick($i, ['notes', 'note', 'description']),
            'order'    => $this->pick($i, ['sort_order', 'position', 'order']),
            'optional' => $this->pick($i, ['is_optional', 'optional']),
            'b_active' => $this->pick($b, ['is_active', 'active']),
            'b_notes'  => $this->pick($b, ['notes', 'note', 'description']),
            'b_ver'    => $this->pick($b, ['version']),
            'b_name'   => $this->pick($b, ['name']),
            'b_scrap'  => $this->pick($b, ['scrap_percentage', 'scrap_allowance']),
        ];
    }

    private function materials(): array
    {
        $out = [];
        foreach (Material::orderBy('name')->get() as $m) {
            $out[$m->id] = [
                'id'     => $m->id,
                'name'   => (string) $m->name,
                'unit'   => (string) ($m->unit ?? 'm²'),
                'price'  => (float) ($m->price_per_m2 ?? $m->price ?? 0),
                'net'    => round((float) $m->stock - (float) $m->stock_reserved, 4),
                'min'    => (float) ($m->minimum_stock ?? 0),
                'active' => (bool) ($m->is_active ?? true),
            ];
        }
        return $out;
    }

    public function selectService($id): void
    {
        $service = Service::find($id);
        if (!$service) return;

        $this->serviceId = (int) $service->id;
        $this->items = [];
        $this->bomNotes = '';
        $this->bomActive = true;
        $this->bomVersion = 0;
        $this->bomExists = false;
        $this->materialQuery = '';
        $this->materialMatches = [];
        $this->showCopy = false;
        $this->copyTargetId = null;
        $this->copyQuery = '';
        $this->resetErrorBag();

        if (!($this->schema['ready'] ?? false)) return;

        $bom = DB::table('service_boms')->where('service_id', $service->id)->first();
        if (!$bom) {
            $this->addItem();
            return;
        }

        $this->bomExists = true;
        $this->bomVersion = (int) ($bom->version ?? 1);
        if ($this->schema['b_active']) $this->bomActive = (bool) ($bom->{$this->schema['b_active']} ?? true);
        if ($this->schema['b_notes']) $this->bomNotes = (string) ($bom->{$this->schema['b_notes']} ?? '');

        $orderCol = $this->schema['order'] ?: 'id';
        $rows = DB::table('service_bom_items')->where($this->schema['fk'], $bom->id)->orderBy($orderCol)->orderBy('id')->get();

        $catalog = $this->materials();
        foreach ($rows as $r) {
            $mid = (int) $r->material_id;
            $this->items[] = [
                'material_id' => $mid,
                'qty'         => (float) ($r->{$this->schema['qty']} ?? 1),
                'scrap'       => (float) ($this->schema['scrap'] ? ($r->{$this->schema['scrap']} ?? 0) : 0),
                'unit'        => (string) ($this->schema['unit'] ? ($r->{$this->schema['unit']} ?? '') : '') ?: ($catalog[$mid]['unit'] ?? 'm²'),
                'optional'    => (bool) ($this->schema['optional'] ? ($r->{$this->schema['optional']} ?? false) : false),
                'notes'       => (string) ($this->schema['notes'] ? ($r->{$this->schema['notes']} ?? '') : ''),
            ];
        }

        if (empty($this->items)) $this->addItem();
        $this->dispatch('bom-scroll-to-editor');
    }

    public function addItem(): void
    {
        $this->items[] = ['material_id' => '', 'qty' => 1, 'scrap' => 0, 'unit' => 'm²', 'optional' => false, 'notes' => ''];
    }

    public function removeItem($index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function moveItem($index, $direction): void
    {
        $to = $direction === 'up' ? $index - 1 : $index + 1;
        if (!isset($this->items[$index]) || !isset($this->items[$to])) return;
        $tmp = $this->items[$index];
        $this->items[$index] = $this->items[$to];
        $this->items[$to] = $tmp;
    }

    public function updatedItems($value, $key): void
    {
        if (!str_ends_with((string) $key, '.material_id')) return;
        $idx = (int) explode('.', (string) $key)[1];
        $m = Material::find($this->items[$idx]['material_id'] ?? null);
        if ($m) $this->items[$idx]['unit'] = (string) ($m->unit ?? 'm²');
    }

    public function updatedMaterialQuery($value): void
    {
        $this->materialMatches = [];
        $q = trim((string) $value);
        if (mb_strlen($q) < 2) return;

        $like = '%' . mb_strtolower($q) . '%';
        $used = array_filter(array_column($this->items, 'material_id'));

        $this->materialMatches = Material::whereRaw('LOWER(name) LIKE ?', [$like])
            ->whereNotIn('id', $used ?: [0])
            ->orderBy('name')->limit(6)->get()
            ->map(fn($m) => [
                'id' => $m->id, 'name' => (string) $m->name, 'unit' => (string) ($m->unit ?? 'm²'),
                'price' => (float) ($m->price_per_m2 ?? 0),
                'net' => round((float) $m->stock - (float) $m->stock_reserved, 4),
                'active' => (bool) ($m->is_active ?? true),
            ])->all();
    }

    public function attachMaterial($id): void
    {
        $m = Material::find($id);
        if (!$m) return;

        foreach ($this->items as $it) {
            if ((int) $it['material_id'] === (int) $id) {
                session()->flash('bom_warning', 'Bahan ' . $m->name . ' sudah ada di resep.');
                return;
            }
        }

        $blank = null;
        foreach ($this->items as $i => $it) {
            if (empty($it['material_id'])) { $blank = $i; break; }
        }

        $row = [
            'material_id' => (int) $m->id,
            'qty' => 1,
            'scrap' => 0,
            'unit' => (string) ($m->unit ?? 'm²'),
            'optional' => false,
            'notes' => '',
        ];

        if ($blank !== null) $this->items[$blank] = $row;
        else $this->items[] = $row;

        $this->materialQuery = '';
        $this->materialMatches = [];
    }

    public function save(): void
    {
        if (!$this->serviceId) {
            $this->addError('service', 'Pilih layanan terlebih dahulu.');
            return;
        }
        if (!($this->schema['ready'] ?? false)) {
            session()->flash('bom_error', 'Skema BOM belum siap. Jalankan php artisan migrate.');
            return;
        }

        $clean = [];
        foreach ($this->items as $it) {
            if (empty($it['material_id'])) continue;
            $clean[] = $it;
        }

        if (empty($clean)) {
            $this->addError('items', 'Resep harus punya minimal satu bahan.');
            return;
        }

        $this->items = array_values($clean);

        $this->validate([
            'items.*.material_id' => 'required|integer|exists:materials,id',
            'items.*.qty'         => 'required|numeric|min:0.0001|max:100000',
            'items.*.scrap'       => 'nullable|numeric|min:0|max:100',
            'items.*.notes'       => 'nullable|string|max:255',
            'bomNotes'            => 'nullable|string|max:500',
        ], [
            'items.*.material_id.required' => 'Setiap baris wajib memilih bahan.',
            'items.*.qty.required'         => 'Kebutuhan per satuan wajib diisi.',
            'items.*.qty.min'              => 'Kebutuhan harus lebih dari 0.',
            'items.*.scrap.max'            => 'Scrap maksimal 100%.',
        ]);

        $seen = [];
        foreach ($this->items as $i => $it) {
            $mid = (int) $it['material_id'];
            if (isset($seen[$mid])) {
                $this->addError('items.' . $i . '.material_id', 'Bahan ini sudah dipakai di baris lain.');
                return;
            }
            $seen[$mid] = true;
        }

        $service = Service::find($this->serviceId);
        if (!$service) return;

        try {
            DB::transaction(function () use ($service) {
                $existing = DB::table('service_boms')->where('service_id', $service->id)->first();

                $data = ['updated_at' => now()];
                if ($this->schema['b_name']) $data[$this->schema['b_name']] = 'Resep ' . $service->name;
                if ($this->schema['b_active']) $data[$this->schema['b_active']] = $this->bomActive;
                if ($this->schema['b_notes']) $data[$this->schema['b_notes']] = trim($this->bomNotes) ?: null;
                if ($this->schema['b_ver']) $data[$this->schema['b_ver']] = ((int) ($existing->version ?? 0)) + 1;

                if ($existing) {
                    DB::table('service_boms')->where('id', $existing->id)->update($data);
                    $bomId = $existing->id;
                } else {
                    $data['service_id'] = $service->id;
                    $data['created_at'] = now();
                    if ($this->schema['b_ver']) $data[$this->schema['b_ver']] = 1;
                    $bomId = DB::table('service_boms')->insertGetId($data);
                }

                DB::table('service_bom_items')->where($this->schema['fk'], $bomId)->delete();

                foreach ($this->items as $i => $it) {
                    $row = [
                        $this->schema['fk'] => $bomId,
                        'material_id' => (int) $it['material_id'],
                        $this->schema['qty'] => round((float) $it['qty'], 4),
                    ];
                    if ($this->schema['scrap']) $row[$this->schema['scrap']] = round((float) ($it['scrap'] ?? 0), 2);
                    if ($this->schema['unit']) $row[$this->schema['unit']] = (string) ($it['unit'] ?: 'm2');
                    if ($this->schema['optional']) $row[$this->schema['optional']] = (bool) ($it['optional'] ?? false);
                    if ($this->schema['order']) $row[$this->schema['order']] = $i;
                    if ($this->schema['notes']) $row[$this->schema['notes']] = trim((string) ($it['notes'] ?? '')) ?: null;
                    if (in_array('created_at', Schema::getColumnListing('service_bom_items'), true)) {
                        $row['created_at'] = now();
                        $row['updated_at'] = now();
                    }
                    DB::table('service_bom_items')->insert($row);
                }
            });
        } catch (\Throwable $e) {
            session()->flash('bom_error', 'Gagal menyimpan resep: ' . $e->getMessage());
            return;
        }

        $this->bomExists = true;
        $this->bomVersion++;
        session()->flash('bom_success', 'Resep ' . $service->name . ' disimpan (' . count($this->items) . ' bahan).');
    }

    public function deleteBom(): void
    {
        if (!$this->serviceId || !($this->schema['ready'] ?? false)) return;

        $bom = DB::table('service_boms')->where('service_id', $this->serviceId)->first();
        if (!$bom) return;

        DB::transaction(function () use ($bom) {
            DB::table('service_bom_items')->where($this->schema['fk'], $bom->id)->delete();
            DB::table('service_boms')->where('id', $bom->id)->delete();
        });

        $this->bomExists = false;
        $this->bomVersion = 0;
        $this->bomNotes = '';
        $this->bomActive = true;
        $this->items = [];
        $this->addItem();

        session()->flash('bom_success', 'Resep dihapus. Layanan ini kembali tanpa BOM.');
    }

    public function openCopy(): void
    {
        if (!$this->serviceId) return;
        $this->showCopy = true;
        $this->copyQuery = '';
        $this->copyTargetId = null;
    }

    public function closeCopy(): void
    {
        $this->showCopy = false;
        $this->copyTargetId = null;
        $this->copyQuery = '';
    }

    public function applyCopy(): void
    {
        if (!$this->serviceId || !$this->copyTargetId || !($this->schema['ready'] ?? false)) {
            $this->addError('copy', 'Pilih layanan tujuan.');
            return;
        }
        if ((int) $this->copyTargetId === (int) $this->serviceId) {
            $this->addError('copy', 'Layanan tujuan tidak boleh sama dengan sumber.');
            return;
        }

        $rows = array_values(array_filter($this->items, fn($it) => !empty($it['material_id'])));
        if (empty($rows)) {
            $this->addError('copy', 'Resep sumber masih kosong.');
            return;
        }

        $target = Service::find($this->copyTargetId);
        if (!$target) return;

        try {
            DB::transaction(function () use ($target, $rows) {
                $existing = DB::table('service_boms')->where('service_id', $target->id)->first();

                $data = ['updated_at' => now()];
                if ($this->schema['b_name']) $data[$this->schema['b_name']] = 'Resep ' . $target->name;
                if ($this->schema['b_active']) $data[$this->schema['b_active']] = true;
                if ($this->schema['b_notes']) $data[$this->schema['b_notes']] = trim($this->bomNotes) ?: null;
                if ($this->schema['b_ver']) $data[$this->schema['b_ver']] = ((int) ($existing->version ?? 0)) + 1;

                if ($existing) {
                    DB::table('service_boms')->where('id', $existing->id)->update($data);
                    $bomId = $existing->id;
                } else {
                    $data['service_id'] = $target->id;
                    $data['created_at'] = now();
                    if ($this->schema['b_ver']) $data[$this->schema['b_ver']] = 1;
                    $bomId = DB::table('service_boms')->insertGetId($data);
                }

                DB::table('service_bom_items')->where($this->schema['fk'], $bomId)->delete();

                $itemCols = Schema::getColumnListing('service_bom_items');
                foreach ($rows as $i => $it) {
                    $row = [
                        $this->schema['fk'] => $bomId,
                        'material_id' => (int) $it['material_id'],
                        $this->schema['qty'] => round((float) $it['qty'], 4),
                    ];
                    if ($this->schema['scrap']) $row[$this->schema['scrap']] = round((float) ($it['scrap'] ?? 0), 2);
                    if ($this->schema['unit']) $row[$this->schema['unit']] = (string) ($it['unit'] ?: 'm2');
                    if ($this->schema['optional']) $row[$this->schema['optional']] = (bool) ($it['optional'] ?? false);
                    if ($this->schema['order']) $row[$this->schema['order']] = $i;
                    if ($this->schema['notes']) $row[$this->schema['notes']] = trim((string) ($it['notes'] ?? '')) ?: null;
                    if (in_array('created_at', $itemCols, true)) { $row['created_at'] = now(); $row['updated_at'] = now(); }
                    DB::table('service_bom_items')->insert($row);
                }
            });
        } catch (\Throwable $e) {
            session()->flash('bom_error', 'Gagal menyalin resep: ' . $e->getMessage());
            return;
        }

        $name = $target->name;
        $this->closeCopy();
        session()->flash('bom_success', 'Resep disalin ke ' . $name . '.');
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = 'all';
        $this->onlyMissing = false;
        $this->sort = 'name';
    }

    public function render()
    {
        $ready = (bool) ($this->schema['ready'] ?? false);
        $catalog = $this->materials();
        $categories = Category::orderBy('name')->get(['id', 'name'])->map(fn($c) => ['id' => $c->id, 'name' => (string) $c->name])->all();
        $catMap = collect($categories)->keyBy('id');

        $bomMap = collect();
        $countMap = collect();
        if ($ready) {
            $bomMap = DB::table('service_boms')->get()->keyBy('service_id');
            $countMap = DB::table('service_bom_items')
                ->selectRaw($this->schema['fk'] . ' as bom_id, COUNT(*) as c')
                ->groupBy($this->schema['fk'])
                ->pluck('c', 'bom_id');
        }

        $all = Service::orderBy('name')->get();
        $services = [];
        $withBom = 0;
        $totalItems = 0;
        $usedMaterials = [];

        foreach ($all as $s) {
            $bom = $bomMap[$s->id] ?? null;
            $count = $bom ? (int) ($countMap[$bom->id] ?? 0) : 0;
            if ($bom && $count > 0) $withBom++;
            $totalItems += $count;

            $services[] = [
                'id' => $s->id,
                'name' => (string) $s->name,
                'category' => (string) ($catMap[$s->category_id]['name'] ?? 'Tanpa divisi'),
                'category_id' => $s->category_id,
                'price' => (float) ($s->price_per_m2 ?? 0),
                'active' => (bool) ($s->is_active ?? true),
                'has_bom' => (bool) $bom,
                'items' => $count,
                'bom_active' => $bom && $this->schema['b_active'] ? (bool) ($bom->{$this->schema['b_active']} ?? true) : true,
            ];
        }

        if ($ready) {
            foreach (DB::table('service_bom_items')->pluck('material_id') as $mid) $usedMaterials[(int) $mid] = true;
        }

        $term = mb_strtolower(trim($this->search));
        $list = array_values(array_filter($services, function ($s) use ($term) {
            if ($this->onlyMissing && $s['has_bom'] && $s['items'] > 0) return false;
            if ($this->categoryFilter !== 'all' && (string) $s['category_id'] !== (string) $this->categoryFilter) return false;
            if ($term === '') return true;
            return str_contains(mb_strtolower($s['name']), $term) || str_contains(mb_strtolower($s['category']), $term);
        }));

        usort($list, function ($a, $b) {
            if ($this->sort === 'missing') return [$a['items'] > 0 ? 1 : 0, $a['name']] <=> [$b['items'] > 0 ? 1 : 0, $b['name']];
            if ($this->sort === 'richest') return [$b['items'], $a['name']] <=> [$a['items'], $b['name']];
            if ($this->sort === 'category') return [$a['category'], $a['name']] <=> [$b['category'], $b['name']];
            return strcmp($a['name'], $b['name']);
        });

        $selected = null;
        $lines = [];
        $simTotalCost = 0.0;
        $simRevenue = 0.0;
        $simShort = 0;

        if ($this->serviceId) {
            $selected = collect($services)->firstWhere('id', $this->serviceId);
            if (!$selected) {
                $s = Service::find($this->serviceId);
                if ($s) {
                    $selected = [
                        'id' => $s->id, 'name' => (string) $s->name,
                        'category' => (string) ($catMap[$s->category_id]['name'] ?? 'Tanpa divisi'),
                        'category_id' => $s->category_id,
                        'price' => (float) ($s->price_per_m2 ?? 0),
                        'active' => (bool) ($s->is_active ?? true),
                        'has_bom' => $this->bomExists, 'items' => count($this->items), 'bom_active' => $this->bomActive,
                    ];
                }
            }

            $area = max(0, (float) str_replace(',', '.', $this->simArea));
            $qty = max(0, (int) $this->simQty);
            $factor = $area * $qty;
            $simRevenue = round(($selected['price'] ?? 0) * $factor, 2);

            foreach ($this->items as $i => $it) {
                $m = $catalog[$it['material_id']] ?? null;
                $perUnit = (float) ($it['qty'] ?? 0);
                $scrap = (float) ($it['scrap'] ?? 0);
                $gross = round($perUnit * (1 + $scrap / 100), 6);
                $need = round($gross * $factor, 4);
                $cost = round($need * ($m['price'] ?? 0), 2);
                $simTotalCost += $cost;
                $short = $m ? ($need > $m['net'] + 0.0001) : false;
                if ($short && !($it['optional'] ?? false)) $simShort++;

                $lines[] = [
                    'index' => $i,
                    'name' => $m['name'] ?? null,
                    'unit' => $m['unit'] ?? ($it['unit'] ?: 'm²'),
                    'per_unit' => $perUnit,
                    'scrap' => $scrap,
                    'gross' => $gross,
                    'need' => $need,
                    'price' => $m['price'] ?? 0,
                    'cost' => $cost,
                    'net' => $m['net'] ?? 0,
                    'short' => $short,
                    'inactive' => $m ? !$m['active'] : false,
                    'optional' => (bool) ($it['optional'] ?? false),
                ];
            }
        }

        $total = count($services);
        $copyTargets = [];
        if ($this->showCopy) {
            $q = mb_strtolower(trim($this->copyQuery));
            foreach ($services as $s) {
                if ($s['id'] === $this->serviceId) continue;
                if ($q !== '' && !str_contains(mb_strtolower($s['name']), $q) && !str_contains(mb_strtolower($s['category']), $q)) continue;
                $copyTargets[] = $s;
                if (count($copyTargets) >= 8) break;
            }
        }

        return view('livewire.admin.service-boms', [
            'ready' => $ready,
            'services' => $list,
            'categories' => $categories,
            'catalog' => $catalog,
            'selected' => $selected,
            'lines' => $lines,
            'copyTargets' => $copyTargets,
            'stats' => [
                'services' => $total,
                'with_bom' => $withBom,
                'missing' => max(0, $total - $withBom),
                'items' => $totalItems,
                'materials' => count($usedMaterials),
                'coverage' => $total > 0 ? (int) round($withBom / $total * 100) : 0,
            ],
            'sim' => [
                'area' => (float) str_replace(',', '.', $this->simArea),
                'qty' => (int) $this->simQty,
                'cost' => round($simTotalCost, 2),
                'revenue' => $simRevenue,
                'ratio' => $simRevenue > 0 ? round($simTotalCost / $simRevenue * 100, 1) : 0,
                'short' => $simShort,
            ],
            'hasFilter' => $term !== '' || $this->categoryFilter !== 'all' || $this->onlyMissing,
        ]);
    }
}