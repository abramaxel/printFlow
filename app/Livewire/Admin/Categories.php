<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Category;
use App\Models\Service;
use App\Models\Material;
use App\Models\Size;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Kategori')]
class Categories extends Component
{
    public string $search = '';
    public string $statusFilter = 'all';
    public string $sort = 'name';

    public string $name = '';
    public string $description = '';
    public bool $is_active = true;

    public ?int $editId = null;
    public string $eName = '';
    public string $eDescription = '';
    public bool $eActive = true;

    public ?int $deleteId = null;
    public ?int $moveTo = null;

    public ?int $detailId = null;

    public array $cols = [];
    public bool $hasSizeCat = false;

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'admin'], true)) {
            abort(403, 'Master kategori hanya untuk owner dan admin.');
        }

        $this->cols = Schema::hasTable('categories') ? Schema::getColumnListing('categories') : [];
        $this->hasSizeCat = Schema::hasColumn('sizes', 'category_id');
    }

    private function has(string $c): bool
    {
        return in_array($c, $this->cols, true);
    }

    private function slugFor(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') $base = 'kategori';
        $slug = $base;
        $i = 2;

        while (Category::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function create(): void
    {
        $rules = ['name' => 'required|string|min:2|max:120|unique:categories,name'];
        if ($this->has('description')) $rules['description'] = 'nullable|string|max:500';

        $data = $this->validate($rules, [
            'name.required' => 'Nama divisi wajib diisi.',
            'name.min' => 'Nama divisi minimal 2 karakter.',
            'name.unique' => 'Nama divisi ini sudah ada.',
        ]);

        $fill = ['name' => trim($data['name'])];
        if ($this->has('slug')) $fill['slug'] = $this->slugFor($fill['name']);
        if ($this->has('description')) $fill['description'] = trim((string) ($data['description'] ?? ''));
        if ($this->has('is_active')) $fill['is_active'] = $this->is_active;

        $cat = new Category();
        $cat->forceFill($fill)->save();

        $this->reset(['name', 'description']);
        $this->is_active = true;

        session()->flash('cat_success', 'Divisi "' . $cat->name . '" ditambahkan. Slug dibuat otomatis.');
        $this->dispatch('cat-created');
    }

    public function openEdit($id): void
    {
        $c = Category::find($id);
        if (!$c) return;

        $this->editId = (int) $c->id;
        $this->eName = (string) $c->name;
        $this->eDescription = (string) ($c->description ?? '');
        $this->eActive = (bool) ($c->is_active ?? true);
        $this->resetErrorBag();
    }

    public function closeEdit(): void
    {
        $this->editId = null;
        $this->resetErrorBag();
    }

    public function saveEdit(): void
    {
        $c = Category::find($this->editId);
        if (!$c) { $this->closeEdit(); return; }

        $rules = ['eName' => ['required', 'string', 'min:2', 'max:120', Rule::unique('categories', 'name')->ignore($c->id)]];
        if ($this->has('description')) $rules['eDescription'] = 'nullable|string|max:500';

        $data = $this->validate($rules, [
            'eName.required' => 'Nama divisi wajib diisi.',
            'eName.min' => 'Nama divisi minimal 2 karakter.',
            'eName.unique' => 'Nama divisi ini sudah dipakai.',
        ]);

        $fill = ['name' => trim($data['eName'])];
        if ($this->has('slug')) $fill['slug'] = $this->slugFor($fill['name'], $c->id);
        if ($this->has('description')) $fill['description'] = trim((string) ($data['eDescription'] ?? ''));
        if ($this->has('is_active')) $fill['is_active'] = $this->eActive;

        $c->forceFill($fill)->save();

        $this->closeEdit();
        session()->flash('cat_success', 'Divisi "' . $c->name . '" diperbarui.');
    }

    public function toggleActive($id): void
    {
        if (!$this->has('is_active')) return;
        $c = Category::find($id);
        if (!$c) return;

        $c->forceFill(['is_active' => !(bool) $c->is_active])->save();

        session()->flash('cat_success', 'Divisi "' . $c->name . '" sekarang ' . ($c->is_active ? 'aktif' : 'nonaktif') . '. Data layanan & bahan tetap utuh.');
    }

    public function openDelete($id): void
    {
        $c = Category::find($id);
        if (!$c) return;
        $this->deleteId = (int) $c->id;
        $this->moveTo = null;
        $this->resetErrorBag('moveTo');
    }

    public function closeDelete(): void
    {
        $this->deleteId = null;
        $this->moveTo = null;
        $this->resetErrorBag('moveTo');
    }

    public function confirmDelete(): void
    {
        $c = Category::find($this->deleteId);
        if (!$c) { $this->closeDelete(); return; }

        $svc = Service::where('category_id', $c->id)->count();
        $mat = Material::where('category_id', $c->id)->count();
        $siz = $this->hasSizeCat ? Size::where('category_id', $c->id)->count() : 0;
        $used = $svc + $mat + $siz;

        if ($used > 0 && !$this->moveTo) {
            $this->addError('moveTo', 'Divisi ini masih dipakai. Pilih divisi tujuan pemindahan terlebih dahulu.');
            return;
        }

        if ($this->moveTo && (int) $this->moveTo === (int) $c->id) {
            $this->addError('moveTo', 'Divisi tujuan tidak boleh sama dengan yang dihapus.');
            return;
        }

        $targetName = null;

        try {
            DB::transaction(function () use ($c, $used, &$targetName) {
                if ($used > 0 && $this->moveTo) {
                    $target = Category::find($this->moveTo);
                    if (!$target) throw new \RuntimeException('Divisi tujuan tidak ditemukan.');
                    $targetName = $target->name;

                    Service::where('category_id', $c->id)->update(['category_id' => $target->id]);
                    Material::where('category_id', $c->id)->update(['category_id' => $target->id]);
                    if ($this->hasSizeCat) Size::where('category_id', $c->id)->update(['category_id' => $target->id]);
                }

                $c->delete();
            });
        } catch (\Throwable $e) {
            session()->flash('cat_error', 'Gagal menghapus divisi: ' . $e->getMessage());
            return;
        }

        $name = $c->name;
        $this->closeDelete();

        session()->flash('cat_success', $targetName
            ? 'Divisi "' . $name . '" dihapus. Seluruh isinya dipindahkan ke "' . $targetName . '".'
            : 'Divisi "' . $name . '" dihapus karena tidak memiliki data terkait.');
    }

    public function openDetail($id): void { $this->detailId = (int) $id; }
    public function closeDetail(): void { $this->detailId = null; }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->sort = 'name';
    }

    public function render()
    {
        $cats = Category::orderBy('name')->get();

        $svcCount = Service::selectRaw('category_id, COUNT(*) as c')->groupBy('category_id')->pluck('c', 'category_id');
        $matCount = Material::selectRaw('category_id, COUNT(*) as c')->groupBy('category_id')->pluck('c', 'category_id');
        $sizCount = $this->hasSizeCat
            ? Size::selectRaw('category_id, COUNT(*) as c')->groupBy('category_id')->pluck('c', 'category_id')
            : collect();

        $svcValue = Service::selectRaw('category_id, COALESCE(SUM(price_per_m2),0) as v')->groupBy('category_id')->pluck('v', 'category_id');
        $matStock = Material::selectRaw('category_id, COALESCE(SUM(stock - stock_reserved),0) as net, COALESCE(SUM(stock * COALESCE(price_per_m2,0)),0) as val')
            ->groupBy('category_id')->get()->keyBy('category_id');

        $rows = [];
        $totalSvc = 0; $totalMat = 0; $totalSiz = 0; $activeCount = 0; $emptyCount = 0; $stockValue = 0.0;

        foreach ($cats as $c) {
            $s = (int) ($svcCount[$c->id] ?? 0);
            $m = (int) ($matCount[$c->id] ?? 0);
            $z = (int) ($sizCount[$c->id] ?? 0);
            $mv = (float) ($matStock[$c->id]->val ?? 0);
            $mn = (float) ($matStock[$c->id]->net ?? 0);
            $active = (bool) ($c->is_active ?? true);

            $totalSvc += $s; $totalMat += $m; $totalSiz += $z; $stockValue += $mv;
            if ($active) $activeCount++;
            if ($s + $m + $z === 0) $emptyCount++;

            $rows[] = [
                'id' => (int) $c->id,
                'name' => (string) $c->name,
                'slug' => (string) ($c->slug ?? Str::slug($c->name)),
                'description' => (string) ($c->description ?? ''),
                'active' => $active,
                'services' => $s,
                'materials' => $m,
                'sizes' => $z,
                'total' => $s + $m + $z,
                'avg_price' => $s > 0 ? round((float) ($svcValue[$c->id] ?? 0) / $s, 2) : 0.0,
                'stock_net' => round($mn, 2),
                'stock_value' => round($mv, 2),
                'created' => $c->created_at?->format('d M Y'),
            ];
        }

        $maxTotal = $rows ? max(array_column($rows, 'total')) : 0;
        foreach ($rows as $i => $r) {
            $rows[$i]['bar'] = $maxTotal > 0 ? max(4, (int) round($r['total'] / $maxTotal * 100)) : 4;
        }

        $term = mb_strtolower(trim($this->search));
        $filtered = array_values(array_filter($rows, function ($r) use ($term) {
            if ($this->statusFilter === 'active' && !$r['active']) return false;
            if ($this->statusFilter === 'inactive' && $r['active']) return false;
            if ($this->statusFilter === 'empty' && $r['total'] > 0) return false;
            if ($term === '') return true;
            return str_contains(mb_strtolower($r['name']), $term)
                || str_contains(mb_strtolower($r['slug']), $term)
                || str_contains(mb_strtolower($r['description']), $term);
        }));

        usort($filtered, function ($a, $b) {
            if ($this->sort === 'richest') return $b['total'] <=> $a['total'];
            if ($this->sort === 'value') return $b['stock_value'] <=> $a['stock_value'];
            if ($this->sort === 'newest') return strcmp((string) $b['created'], (string) $a['created']);
            return strcmp($a['name'], $b['name']);
        });

        $detail = null;
        if ($this->detailId) {
            $detail = collect($rows)->firstWhere('id', $this->detailId);
            if ($detail) {
                $detail['service_list'] = Service::where('category_id', $this->detailId)->orderBy('name')->limit(12)
                    ->get(['id', 'name', 'price_per_m2'])
                    ->map(fn($x) => ['id' => $x->id, 'name' => (string) $x->name, 'price' => (float) ($x->price_per_m2 ?? 0)])->all();
                $detail['material_list'] = Material::where('category_id', $this->detailId)->orderBy('name')->limit(12)
                    ->get(['id', 'name', 'unit', 'stock', 'stock_reserved', 'minimum_stock'])
                    ->map(fn($x) => [
                        'id' => $x->id, 'name' => (string) $x->name, 'unit' => (string) ($x->unit ?? 'm²'),
                        'net' => round((float) $x->stock - (float) $x->stock_reserved, 2),
                        'min' => (float) ($x->minimum_stock ?? 0),
                    ])->all();
            }
        }

        $deleteTarget = null;
        if ($this->deleteId) {
            $deleteTarget = collect($rows)->firstWhere('id', $this->deleteId);
        }

        $moveOptions = array_values(array_filter($rows, fn($r) => $r['id'] !== $this->deleteId));

        return view('livewire.admin.categories', [
            'rows' => $filtered,
            'allRows' => $rows,
            'detail' => $detail,
            'deleteTarget' => $deleteTarget,
            'moveOptions' => $moveOptions,
            'stats' => [
                'categories' => count($rows),
                'active' => $activeCount,
                'empty' => $emptyCount,
                'services' => $totalSvc,
                'materials' => $totalMat,
                'sizes' => $totalSiz,
                'stock_value' => round($stockValue, 2),
            ],
            'hasActive' => $this->has('is_active'),
            'hasDesc' => $this->has('description'),
            'hasSizeCat' => $this->hasSizeCat,
            'hasFilter' => $term !== '' || $this->statusFilter !== 'all',
        ]);
    }
}