<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Size;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Ukuran')]
class Sizes extends Component
{
    public string $search = '';
    public string $categoryFilter = 'all';
    public string $kindFilter = 'all';
    public string $statusFilter = 'all';
    public string $sort = 'name';

    public bool $createOpen = false;
    public string $cName = '';
    public string $cCategory = '';
    public string $cMode = 'dimension';
    public string $cWidth = '';
    public string $cHeight = '';
    public string $cUnits = '1';
    public string $cArea = '';
    public string $cMultiplier = '1';
    public bool $cActive = true;

    public ?int $editId = null;
    public string $eName = '';
    public string $eCategory = '';
    public string $eMode = 'dimension';
    public string $eWidth = '';
    public string $eHeight = '';
    public string $eUnits = '1';
    public string $eArea = '';
    public string $eMultiplier = '1';
    public bool $eActive = true;

    public ?int $deleteId = null;
    public ?int $detailId = null;
    public string $simService = '';
    public string $simQty = '1';

    public array $cols = [];
    public bool $ordersSoft = false;

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'admin'], true)) {
            abort(403, 'Master ukuran hanya untuk owner dan admin.');
        }

        $this->cols = Schema::hasTable('sizes') ? Schema::getColumnListing('sizes') : [];
        $this->ordersSoft = Schema::hasColumn('orders', 'deleted_at');
    }

    private function has(string $c): bool
    {
        return in_array($c, $this->cols, true);
    }

    private function multiplierColumn(): ?string
    {
        foreach (['price_multiplier', 'multiplier', 'price_factor'] as $c) {
            if ($this->has($c)) return $c;
        }
        return null;
    }

    private function unitsColumn(): ?string
    {
        foreach (['units_per_sheet', 'pcs_per_sheet', 'units_per_unit', 'qty_per_unit'] as $c) {
            if ($this->has($c)) return $c;
        }
        return null;
    }

    private function slugFor(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'ukuran';
        $slug = $base;
        $i = 2;
        while (Size::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }

    private function singleArea($w, $h): ?float
    {
        $w = (float) $w;
        $h = (float) $h;
        if ($w <= 0 || $h <= 0) return null;
        return round(($w * $h) / 10000, 8);
    }

    private function computedArea($w, $h, int $units = 1): ?float
    {
        $single = $this->singleArea($w, $h);
        if ($single === null) return null;
        return round($single * max(1, $units), 6);
    }

    private function unitsOf($s): int
    {
        $uc = $this->unitsColumn();
        if (!$uc) return 1;
        return max(1, (int) ($s->{$uc} ?? 1));
    }

    /* ================= TAMBAH ================= */

    public function openCreate(): void
    {
        $this->createOpen = true;
        $this->cName = ''; $this->cCategory = ''; $this->cMode = 'dimension';
        $this->cWidth = ''; $this->cHeight = ''; $this->cUnits = '1'; $this->cArea = '';
        $this->cMultiplier = '1'; $this->cActive = true;
        $this->resetErrorBag();
    }

    public function closeCreate(): void
    {
        $this->createOpen = false;
        $this->resetErrorBag();
    }

    public function saveCreate(): void
    {
        $rules = [
            'cName' => 'required|string|min:2|max:120|unique:sizes,name',
            'cCategory' => 'nullable|integer|exists:categories,id',
            'cMode' => 'required|in:dimension,flat',
        ];
        if ($this->multiplierColumn()) $rules['cMultiplier'] = 'required|numeric|min:0.01|max:100';

        if ($this->cMode === 'dimension') {
            $rules['cWidth'] = 'required|numeric|min:0.1|max:10000';
            $rules['cHeight'] = 'required|numeric|min:0.1|max:10000';
            if ($this->unitsColumn()) $rules['cUnits'] = 'required|integer|min:1|max:100000';
        } else {
            $rules['cArea'] = 'required|numeric|min:0.0001|max:10000';
        }

        $data = $this->validate($rules, [
            'cName.required' => 'Nama ukuran wajib diisi.',
            'cName.unique' => 'Nama ukuran ini sudah terdaftar.',
            'cWidth.required' => 'Lebar wajib diisi untuk ukuran berbasis dimensi.',
            'cHeight.required' => 'Tinggi wajib diisi untuk ukuran berbasis dimensi.',
            'cWidth.min' => 'Lebar minimal 0,1 cm.',
            'cHeight.min' => 'Tinggi minimal 0,1 cm.',
            'cUnits.required' => 'Isi per lembar wajib diisi. Isi 1 bila satu unit jual = satu pcs.',
            'cUnits.integer' => 'Isi per lembar harus bilangan bulat.',
            'cUnits.min' => 'Isi per lembar minimal 1.',
            'cArea.required' => 'Luas setara wajib diisi untuk ukuran tanpa dimensi.',
            'cArea.min' => 'Luas setara harus lebih dari 0.',
            'cMultiplier.required' => 'Pengali harga wajib diisi.',
        ]);

        $isDim = $data['cMode'] === 'dimension';
        $w = $isDim ? round((float) $data['cWidth'], 2) : null;
        $h = $isDim ? round((float) $data['cHeight'], 2) : null;
        $units = $isDim ? max(1, (int) ($data['cUnits'] ?? 1)) : 1;
        $area = $isDim ? $this->computedArea($w, $h, $units) : round((float) $data['cArea'], 6);

        $fill = ['name' => trim($data['cName'])];
        if ($this->has('slug')) $fill['slug'] = $this->slugFor($fill['name']);
        if ($this->has('category_id')) $fill['category_id'] = $data['cCategory'] !== '' ? (int) $data['cCategory'] : null;
        if ($this->has('width_cm')) $fill['width_cm'] = $w;
        if ($this->has('height_cm')) $fill['height_cm'] = $h;
        if ($this->has('area_m2')) $fill['area_m2'] = $area;
        if ($this->has('is_active')) $fill['is_active'] = $this->cActive;
        if ($uc = $this->unitsColumn()) $fill[$uc] = $units;
        if ($mc = $this->multiplierColumn()) $fill[$mc] = round((float) $data['cMultiplier'], 4);

        $s = new Size();
        $s->forceFill($fill)->save();

        $this->closeCreate();
        session()->flash('sz_success', 'Ukuran "' . $s->name . '" ditambahkan. Luas per unit jual ' . number_format((float) $area, 4) . ' m²' . ($units > 1 ? ' (' . $units . ' pcs per lembar).' : '.'));
    }

    /* ================= EDIT ================= */

    public function openEdit($id): void
    {
        $s = Size::find($id);
        if (!$s) return;

        $w = $s->width_cm !== null ? (float) $s->width_cm : null;
        $h = $s->height_cm !== null ? (float) $s->height_cm : null;
        $isDim = $w !== null && $h !== null && $w > 0 && $h > 0;

        $this->editId = (int) $s->id;
        $this->eName = (string) $s->name;
        $this->eCategory = $s->category_id ? (string) $s->category_id : '';
        $this->eMode = $isDim ? 'dimension' : 'flat';
        $this->eWidth = $isDim ? (string) $w : '';
        $this->eHeight = $isDim ? (string) $h : '';
        $this->eUnits = (string) $this->unitsOf($s);
        $this->eArea = (string) (float) ($s->area_m2 ?? 0);
        $this->eMultiplier = $this->multiplierColumn() ? (string) (float) ($s->{$this->multiplierColumn()} ?? 1) : '1';
        $this->eActive = (bool) ($s->is_active ?? true);
        $this->detailId = null;
        $this->resetErrorBag();
    }

    public function closeEdit(): void
    {
        $this->editId = null;
        $this->resetErrorBag();
    }

    public function saveEdit(): void
    {
        $s = Size::find($this->editId);
        if (!$s) { $this->closeEdit(); return; }

        $rules = [
            'eName' => ['required', 'string', 'min:2', 'max:120', Rule::unique('sizes', 'name')->ignore($s->id)],
            'eCategory' => 'nullable|integer|exists:categories,id',
            'eMode' => 'required|in:dimension,flat',
        ];
        if ($this->multiplierColumn()) $rules['eMultiplier'] = 'required|numeric|min:0.01|max:100';

        if ($this->eMode === 'dimension') {
            $rules['eWidth'] = 'required|numeric|min:0.1|max:10000';
            $rules['eHeight'] = 'required|numeric|min:0.1|max:10000';
            if ($this->unitsColumn()) $rules['eUnits'] = 'required|integer|min:1|max:100000';
        } else {
            $rules['eArea'] = 'required|numeric|min:0.0001|max:10000';
        }

        $data = $this->validate($rules, [
            'eName.required' => 'Nama ukuran wajib diisi.',
            'eName.unique' => 'Nama ukuran sudah dipakai.',
            'eWidth.required' => 'Lebar wajib diisi.',
            'eHeight.required' => 'Tinggi wajib diisi.',
            'eUnits.required' => 'Isi per lembar wajib diisi.',
            'eUnits.min' => 'Isi per lembar minimal 1.',
            'eArea.required' => 'Luas setara wajib diisi.',
            'eArea.min' => 'Luas setara harus lebih dari 0.',
        ]);

        $isDim = $data['eMode'] === 'dimension';
        $w = $isDim ? round((float) $data['eWidth'], 2) : null;
        $h = $isDim ? round((float) $data['eHeight'], 2) : null;
        $units = $isDim ? max(1, (int) ($data['eUnits'] ?? 1)) : 1;
        $area = $isDim ? $this->computedArea($w, $h, $units) : round((float) $data['eArea'], 6);

        $fill = ['name' => trim($data['eName'])];
        if ($this->has('slug')) $fill['slug'] = $this->slugFor($fill['name'], $s->id);
        if ($this->has('category_id')) $fill['category_id'] = $data['eCategory'] !== '' ? (int) $data['eCategory'] : null;
        if ($this->has('width_cm')) $fill['width_cm'] = $w;
        if ($this->has('height_cm')) $fill['height_cm'] = $h;
        if ($this->has('area_m2')) $fill['area_m2'] = $area;
        if ($this->has('is_active')) $fill['is_active'] = $this->eActive;
        if ($uc = $this->unitsColumn()) $fill[$uc] = $units;
        if ($mc = $this->multiplierColumn()) $fill[$mc] = round((float) $data['eMultiplier'], 4);

        $s->forceFill($fill)->save();

        $this->closeEdit();
        session()->flash('sz_success', 'Ukuran "' . $s->name . '" diperbarui. Luas per unit jual kini ' . number_format((float) $area, 4) . ' m².');
    }

    public function toggleActive($id): void
    {
        if (!$this->has('is_active')) return;
        $s = Size::find($id);
        if (!$s) return;

        $s->forceFill(['is_active' => !(bool) $s->is_active])->save();
        session()->flash('sz_success', 'Ukuran "' . $s->name . '" sekarang ' . ($s->is_active ? 'aktif' : 'nonaktif') . '. Riwayat pesanan tetap utuh.');
    }

    /* ================= KOREKSI LUAS ================= */

    public function fixArea($id): void
    {
        if (!$this->has('area_m2')) return;
        $s = Size::find($id);
        if (!$s) return;

        $units = $this->unitsOf($s);
        $area = $this->computedArea($s->width_cm, $s->height_cm, $units);
        if ($area === null) {
            session()->flash('sz_error', 'Ukuran ini tidak punya dimensi, luasnya harus diatur manual.');
            return;
        }

        $old = (float) ($s->area_m2 ?? 0);
        $s->forceFill(['area_m2' => $area])->save();

        session()->flash('sz_success', 'Luas "' . $s->name . '" dikoreksi dari ' . number_format($old, 4) . ' menjadi ' . number_format($area, 4) . ' m²' . ($units > 1 ? ' (isi ' . $units . ' pcs/lembar).' : '.') . ' Harga pesanan baru mengikuti nilai ini.');
    }

    public function fixAllAreas(): void
    {
        if (!$this->has('area_m2')) return;

        $fixed = 0;
        DB::transaction(function () use (&$fixed) {
            foreach (Size::all() as $s) {
                $units = $this->unitsOf($s);
                $area = $this->computedArea($s->width_cm, $s->height_cm, $units);
                if ($area === null || $area <= 0) continue;

                $stored = (float) ($s->area_m2 ?? 0);
                if ($stored <= 0) { $s->forceFill(['area_m2' => $area])->save(); $fixed++; continue; }
                if (abs($stored - $area) / $area > 0.01) {
                    $s->forceFill(['area_m2' => $area])->save();
                    $fixed++;
                }
            }
        });

        session()->flash($fixed > 0 ? 'sz_success' : 'sz_error', $fixed > 0
            ? $fixed . ' ukuran dikoreksi luasnya sesuai dimensi × isi per lembar. Pesanan lama tidak berubah.'
            : 'Tidak ada luas yang perlu dikoreksi.');
    }

    /* ================= HAPUS ================= */

    public function openDelete($id): void
    {
        $s = Size::find($id);
        if (!$s) return;
        $this->deleteId = (int) $s->id;
        $this->detailId = null;
    }

    public function closeDelete(): void { $this->deleteId = null; }

    public function confirmDelete(): void
    {
        $s = Size::find($this->deleteId);
        if (!$s) { $this->closeDelete(); return; }

        $refs = OrderItem::where('size_id', $s->id)->count();
        if ($refs > 0) {
            session()->flash('sz_error', 'Ukuran ini dipakai ' . $refs . ' baris pesanan dan tidak boleh dihapus. Gunakan nonaktifkan.');
            $this->closeDelete();
            return;
        }

        $name = $s->name;
        $s->delete();
        $this->closeDelete();
        session()->flash('sz_success', 'Ukuran "' . $name . '" dihapus karena belum pernah dipakai.');
    }

    public function deactivateInstead(): void
    {
        if (!$this->has('is_active')) return;
        $s = Size::find($this->deleteId);
        if (!$s) { $this->closeDelete(); return; }

        $s->forceFill(['is_active' => false])->save();
        $name = $s->name;
        $this->closeDelete();
        session()->flash('sz_success', 'Ukuran "' . $name . '" dinonaktifkan. Tidak muncul di pesanan baru, riwayat aman.');
    }

    /* ================= DETAIL & FILTER ================= */

    public function openDetail($id): void
    {
        $this->detailId = (int) $id;
        $this->simQty = '1';
        $this->simService = '';
    }

    public function closeDetail(): void { $this->detailId = null; }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = 'all';
        $this->kindFilter = 'all';
        $this->statusFilter = 'all';
        $this->sort = 'name';
    }

    private function buildRows(): array
    {
        $cats = Category::pluck('name', 'id')->all();
        $sizes = Size::orderBy('name')->get();
        $mc = $this->multiplierColumn();

        $usage = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->when($this->ordersSoft, fn($q) => $q->whereNull('orders.deleted_at'))
            ->whereNotIn('orders.status', ['cancelled', 'rejected'])
            ->whereNotNull('order_items.size_id')
            ->selectRaw('order_items.size_id as sid')
            ->selectRaw('COUNT(*) as lines')
            ->selectRaw('COALESCE(SUM(order_items.quantity),0) as qty')
            ->selectRaw('COALESCE(SUM(order_items.final_subtotal),0) as revenue')
            ->groupBy('order_items.size_id')
            ->get()->keyBy('sid');

        $refs = OrderItem::whereNotNull('size_id')->selectRaw('size_id, COUNT(*) as c')->groupBy('size_id')->pluck('c', 'size_id');

        $rows = [];
        foreach ($sizes as $s) {
            $w = $s->width_cm !== null ? (float) $s->width_cm : null;
            $h = $s->height_cm !== null ? (float) $s->height_cm : null;
            $isDim = $w !== null && $h !== null && $w > 0 && $h > 0;
            $stored = (float) ($s->area_m2 ?? 0);
            $units = $this->unitsOf($s);
            $single = $isDim ? $this->singleArea($w, $h) : null;
            $computed = $isDim ? $this->computedArea($w, $h, $units) : null;

            $drift = null;
            $mismatch = false;
            if ($computed !== null && $computed > 0) {
                $drift = round(($stored - $computed) / $computed * 100, 1);
                $mismatch = abs($drift) > 1.0;
            }

            $u = $usage[$s->id] ?? null;
            $maxSide = $isDim ? max($w, $h) : 0;
            $boxW = $isDim ? max(4, (int) round($w / $maxSide * 34)) : 0;
            $boxH = $isDim ? max(4, (int) round($h / $maxSide * 34)) : 0;

            $rows[] = [
                'id' => (int) $s->id,
                'name' => (string) $s->name,
                'slug' => (string) ($s->slug ?? Str::slug($s->name)),
                'category_id' => $s->category_id,
                'category' => $s->category_id ? (string) ($cats[$s->category_id] ?? 'Divisi terhapus') : 'Semua divisi',
                'dimensional' => $isDim,
                'width' => $w,
                'height' => $h,
                'units' => $units,
                'multi_up' => $isDim && $units > 1,
                'single_area' => $single,
                'area' => $stored,
                'computed' => $computed,
                'drift' => $drift,
                'mismatch' => $mismatch,
                'no_area' => $stored <= 0,
                'multiplier' => $mc ? (float) ($s->{$mc} ?? 1) : 1.0,
                'active' => (bool) ($s->is_active ?? true),
                'orders' => (int) ($u->lines ?? 0),
                'qty' => (int) ($u->qty ?? 0),
                'revenue' => round((float) ($u->revenue ?? 0), 2),
                'refs' => (int) ($refs[$s->id] ?? 0),
                'box_w' => $boxW,
                'box_h' => $boxH,
                'created' => $s->created_at?->format('d M Y'),
            ];
        }

        return $rows;
    }

    private function filterRows(array $rows): array
    {
        $term = mb_strtolower(trim($this->search));

        $out = array_values(array_filter($rows, function ($r) use ($term) {
            if ($this->categoryFilter === 'global' && $r['category_id'] !== null) return false;
            if ($this->categoryFilter !== 'all' && $this->categoryFilter !== 'global' && (string) $r['category_id'] !== (string) $this->categoryFilter) return false;
            if ($this->kindFilter === 'dimension' && !$r['dimensional']) return false;
            if ($this->kindFilter === 'flat' && $r['dimensional']) return false;
            if ($this->kindFilter === 'multiup' && !$r['multi_up']) return false;
            if ($this->statusFilter === 'active' && !$r['active']) return false;
            if ($this->statusFilter === 'inactive' && $r['active']) return false;
            if ($this->statusFilter === 'issue' && !($r['mismatch'] || $r['no_area'])) return false;
            if ($this->statusFilter === 'unused' && $r['orders'] > 0) return false;
            if ($term === '') return true;
            return str_contains(mb_strtolower($r['name']), $term)
                || str_contains(mb_strtolower($r['slug']), $term)
                || str_contains(mb_strtolower($r['category']), $term);
        }));

        usort($out, function ($a, $b) {
            if ($this->sort === 'area_desc') return $b['area'] <=> $a['area'];
            if ($this->sort === 'area_asc') return $a['area'] <=> $b['area'];
            if ($this->sort === 'popular') return $b['orders'] <=> $a['orders'];
            if ($this->sort === 'revenue') return $b['revenue'] <=> $a['revenue'];
            if ($this->sort === 'category') return [$a['category'], $a['name']] <=> [$b['category'], $b['name']];
            if ($this->sort === 'issue') return [($a['mismatch'] || $a['no_area']) ? 0 : 1, $a['name']] <=> [($b['mismatch'] || $b['no_area']) ? 0 : 1, $b['name']];
            return strcmp($a['name'], $b['name']);
        });

        return $out;
    }

    /* ================= EKSPOR ================= */

    private function exportHead(): array
    {
        return ['Nama Ukuran', 'Slug', 'Divisi', 'Jenis', 'Lebar (cm)', 'Tinggi (cm)', 'Isi per Lembar', 'Luas per Pcs (m²)', 'Luas per Unit Jual (m²)', 'Luas Seharusnya (m²)', 'Selisih %', 'Pengali Harga', 'Baris Pesanan', 'Qty Terjual', 'Omzet', 'Status', 'Dibuat'];
    }

    private function exportLine(array $r): array
    {
        return [
            $r['name'], $r['slug'], $r['category'],
            $r['dimensional'] ? 'Berbasis dimensi' : 'Luas setara',
            $r['width'] ?? '-', $r['height'] ?? '-',
            $r['dimensional'] ? $r['units'] : '-',
            $r['single_area'] ?? '-',
            $r['area'], $r['computed'] ?? '-', $r['drift'] ?? '-', $r['multiplier'],
            $r['orders'], $r['qty'], $r['revenue'],
            $r['active'] ? 'Aktif' : 'Nonaktif', $r['created'] ?? '-',
        ];
    }

    public function exportExcel()
    {
        $rows = $this->filterRows($this->buildRows());

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= '<Styles><Style ss:ID="h"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#0A3F33" ss:Pattern="Solid"/></Style>'
              . '<Style ss:ID="n"><NumberFormat ss:Format="#,##0.0000"/></Style></Styles>' . "\n";
        $xml .= '<Worksheet ss:Name="Master Ukuran"><Table>' . "\n";
        $xml .= '<Row>';
        foreach ($this->exportHead() as $h) $xml .= '<Cell ss:StyleID="h"><Data ss:Type="String">' . htmlspecialchars($h, ENT_XML1) . '</Data></Cell>';
        $xml .= '</Row>' . "\n";

        foreach ($rows as $r) {
            $xml .= '<Row>';
            foreach ($this->exportLine($r) as $v) {
                $isNum = is_int($v) || is_float($v);
                $xml .= '<Cell' . ($isNum ? ' ss:StyleID="n"' : '') . '><Data ss:Type="' . ($isNum ? 'Number' : 'String') . '">'
                      . htmlspecialchars((string) $v, ENT_XML1) . '</Data></Cell>';
            }
            $xml .= '</Row>' . "\n";
        }
        $xml .= '</Table></Worksheet></Workbook>';

        return response()->streamDownload(function () use ($xml) { echo $xml; }, 'master-ukuran-' . now()->format('Ymd-His') . '.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function exportCsv()
    {
        $rows = $this->filterRows($this->buildRows());
        $head = $this->exportHead();

        return response()->streamDownload(function () use ($rows, $head) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $head, ';');
            foreach ($rows as $r) fputcsv($out, $this->exportLine($r), ';');
            fclose($out);
        }, 'master-ukuran-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $all = $this->buildRows();
        $rows = $this->filterRows($all);

        $active = 0; $dim = 0; $flat = 0; $issues = 0; $unused = 0; $multiUp = 0; $revenue = 0.0;
        $issueList = [];
        foreach ($all as $r) {
            if ($r['active']) $active++;
            if ($r['dimensional']) $dim++; else $flat++;
            if ($r['multi_up']) $multiUp++;
            if ($r['mismatch'] || $r['no_area']) { $issues++; $issueList[] = $r; }
            if ($r['orders'] === 0) $unused++;
            $revenue += $r['revenue'];
        }

        $detail = null;
        if ($this->detailId) {
            $detail = collect($all)->firstWhere('id', $this->detailId);
            if ($detail) {
                $svcOptions = Service::where('is_active', true)
                    ->when($detail['category_id'] !== null, fn($q) => $q->where('category_id', $detail['category_id']))
                    ->orderBy('name')->limit(40)->get(['id', 'name', 'price_per_m2'])
                    ->map(fn($x) => ['id' => (int) $x->id, 'name' => (string) $x->name, 'price' => (float) ($x->price_per_m2 ?? 0)])->all();

                if (empty($svcOptions)) {
                    $svcOptions = Service::where('is_active', true)->orderBy('name')->limit(40)->get(['id', 'name', 'price_per_m2'])
                        ->map(fn($x) => ['id' => (int) $x->id, 'name' => (string) $x->name, 'price' => (float) ($x->price_per_m2 ?? 0)])->all();
                }

                $detail['services'] = $svcOptions;

                $chosen = null;
                foreach ($svcOptions as $o) {
                    if ((string) $o['id'] === (string) $this->simService) { $chosen = $o; break; }
                }
                if (!$chosen && !empty($svcOptions)) $chosen = $svcOptions[0];

                $qty = max(1, (int) $this->simQty);
                $factor = $detail['area'] * $qty * $detail['multiplier'];
                $detail['sim'] = [
                    'service' => $chosen,
                    'qty' => $qty,
                    'factor' => round($factor, 6),
                    'jasa' => $chosen ? round($chosen['price'] * $factor, 2) : 0.0,
                    'pcs' => $qty * $detail['units'],
                ];

                $detail['recent'] = OrderItem::query()
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->when($this->ordersSoft, fn($q) => $q->whereNull('orders.deleted_at'))
                    ->where('order_items.size_id', $this->detailId)
                    ->orderByDesc('order_items.id')->limit(6)
                    ->get(['orders.id as oid', 'orders.invoice_number', 'orders.status', 'order_items.quantity', 'order_items.final_subtotal'])
                    ->map(fn($x) => [
                        'oid' => (int) $x->oid,
                        'invoice' => (string) $x->invoice_number,
                        'status' => (string) $x->status,
                        'qty' => (int) $x->quantity,
                        'value' => round((float) $x->final_subtotal, 2),
                    ])->all();
            }
        }

        $deleteTarget = $this->deleteId ? collect($all)->firstWhere('id', $this->deleteId) : null;

        return view('livewire.admin.sizes', [
            'rows' => $rows,
            'detail' => $detail,
            'deleteTarget' => $deleteTarget,
            'issueList' => array_slice($issueList, 0, 6),
            'issueTotal' => count($issueList),
            'categories' => Category::orderBy('name')->get(['id', 'name'])->map(fn($c) => ['id' => $c->id, 'name' => (string) $c->name])->all(),
            'stats' => [
                'total' => count($all),
                'shown' => count($rows),
                'active' => $active,
                'dimension' => $dim,
                'flat' => $flat,
                'multi_up' => $multiUp,
                'issues' => $issues,
                'unused' => $unused,
                'revenue' => round($revenue, 2),
            ],
            'hasActive' => $this->has('is_active'),
            'hasCategory' => $this->has('category_id'),
            'hasMultiplier' => $this->multiplierColumn() !== null,
            'hasUnits' => $this->unitsColumn() !== null,
            'hasFilter' => $this->search !== '' || $this->categoryFilter !== 'all' || $this->kindFilter !== 'all' || $this->statusFilter !== 'all',
        ]);
    }
}