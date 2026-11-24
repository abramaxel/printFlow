<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Bahan Baku')]
class Materials extends Component
{
    public string $search = '';
    public string $categoryFilter = 'all';
    public string $statusFilter = 'all';
    public string $sort = 'urgency';

    public bool $createOpen = false;
    public string $cName = '';
    public string $cCategory = '';
    public string $cUnit = 'm2';
    public string $cPrice = '';
    public string $cMin = '';
    public string $cMax = '';
    public string $cLead = '3';
    public string $cStock = '';
    public string $cShelf = '';
    public bool $cBatch = false;
    public bool $cActive = true;

    public ?int $editId = null;
    public string $eName = '';
    public string $eCategory = '';
    public string $eUnit = 'm2';
    public string $ePrice = '';
    public string $eMin = '';
    public string $eMax = '';
    public string $eLead = '3';
    public string $eShelf = '';
    public bool $eBatch = false;
    public bool $eActive = true;

    public ?int $deleteId = null;
    public ?int $detailId = null;

    public array $cols = [];
    public array $moveCols = [];
    public bool $hasBatchTable = false;

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'admin', 'produksi', 'production'], true)) {
            abort(403, 'Master bahan baku hanya untuk owner, admin, dan tim gudang.');
        }

        $this->cols = Schema::hasTable('materials') ? Schema::getColumnListing('materials') : [];
        $this->moveCols = Schema::hasTable('material_movements') ? Schema::getColumnListing('material_movements') : [];
        $this->hasBatchTable = Schema::hasTable('material_batches');
    }

    private function has(string $c): bool
    {
        return in_array($c, $this->cols, true);
    }

    private function priceColumns(): array
    {
        return array_values(array_filter(['price_per_m2', 'price', 'unit_price'], fn($c) => $this->has($c)));
    }

    private function priceOf($m): float
    {
        foreach (['price_per_m2', 'price', 'unit_price'] as $k) {
            $v = (float) ($m->{$k} ?? 0);
            if ($v > 0) return $v;
        }
        return 0.0;
    }

    private function unitLabel(string $u): string
    {
        $u = trim($u);
        if ($u === '' ) return 'm²';
        return str_ireplace(['m2', 'M2'], 'm²', $u);
    }

    private function slugFor(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'bahan';
        $slug = $base;
        $i = 2;
        while (Material::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }

    private function applyFill(array $fill, float $price): array
    {
        foreach ($this->priceColumns() as $c) $fill[$c] = $price;
        return $fill;
    }

    /* ================= TAMBAH ================= */

    public function openCreate(): void
    {
        $this->createOpen = true;
        $this->cName = ''; $this->cCategory = ''; $this->cUnit = 'm2';
        $this->cPrice = ''; $this->cMin = ''; $this->cMax = '';
        $this->cLead = '3'; $this->cStock = ''; $this->cShelf = '';
        $this->cBatch = false; $this->cActive = true;
        $this->resetErrorBag();
    }

    public function closeCreate(): void
    {
        $this->createOpen = false;
        $this->resetErrorBag();
    }

    public function saveCreate(): void
    {
        $data = $this->validate([
            'cName' => 'required|string|min:2|max:150|unique:materials,name',
            'cCategory' => 'required|integer|exists:categories,id',
            'cUnit' => 'required|string|max:20',
            'cPrice' => 'required|numeric|min:0|max:100000000',
            'cMin' => 'required|numeric|min:0|max:1000000',
            'cMax' => 'nullable|numeric|min:0|max:1000000',
            'cLead' => 'required|integer|min:0|max:365',
            'cStock' => 'nullable|numeric|min:0|max:1000000',
            'cShelf' => 'nullable|integer|min:1|max:3650',
        ], [
            'cName.required' => 'Nama bahan wajib diisi.',
            'cName.unique' => 'Nama bahan ini sudah terdaftar.',
            'cCategory.required' => 'Divisi produksi wajib dipilih.',
            'cUnit.required' => 'Satuan wajib diisi (m2, pcs, roll, kg).',
            'cPrice.required' => 'Harga satuan wajib diisi agar nilai stok dapat dihitung.',
            'cPrice.numeric' => 'Harga harus berupa angka.',
            'cMin.required' => 'Batas minimum (ROP) wajib diisi agar sistem bisa memperingatkan restock.',
            'cMin.numeric' => 'Batas minimum harus berupa angka.',
            'cLead.required' => 'Lead time wajib diisi.',
        ]);

        if ($data['cMax'] !== null && $data['cMax'] !== '' && (float) $data['cMax'] > 0 && (float) $data['cMax'] < (float) $data['cMin']) {
            $this->addError('cMax', 'Stok maksimum tidak boleh lebih kecil dari batas minimum.');
            return;
        }

        $initial = (float) ($data['cStock'] ?? 0);
        $price = round((float) $data['cPrice'], 2);

        try {
            $m = DB::transaction(function () use ($data, $initial, $price) {
                $fill = [
                    'name' => trim($data['cName']),
                    'category_id' => (int) $data['cCategory'],
                    'stock' => $initial,
                    'stock_reserved' => 0,
                    'minimum_stock' => round((float) $data['cMin'], 4),
                ];
                if ($this->has('slug')) $fill['slug'] = $this->slugFor($fill['name']);
                if ($this->has('unit')) $fill['unit'] = trim($data['cUnit']);
                if ($this->has('maximum_stock')) $fill['maximum_stock'] = round((float) ($data['cMax'] ?? 0), 4);
                if ($this->has('lead_time_days')) $fill['lead_time_days'] = (int) $data['cLead'];
                if ($this->has('shelf_life_days')) $fill['shelf_life_days'] = $data['cShelf'] !== '' ? (int) $data['cShelf'] : null;
                if ($this->has('is_batch_tracked')) $fill['is_batch_tracked'] = $this->cBatch;
                if ($this->has('is_active')) $fill['is_active'] = $this->cActive;
                $fill = $this->applyFill($fill, $price);

                $mat = new Material();
                $mat->forceFill($fill)->save();

                if ($initial > 0) $this->writeMovement($mat, 'in', 'initial', $initial, $price, 'Stok awal saat pendaftaran bahan');

                return $mat;
            });
        } catch (\Throwable $e) {
            session()->flash('mat_error', 'Gagal menyimpan bahan: ' . $e->getMessage());
            return;
        }

        $this->closeCreate();
        session()->flash('mat_success', 'Bahan "' . $m->name . '" ditambahkan.' . ($initial > 0 ? ' Stok awal tercatat di audit trail.' : ''));
    }

    /* ================= EDIT ================= */

    public function openEdit($id): void
    {
        $m = Material::find($id);
        if (!$m) return;

        $this->editId = (int) $m->id;
        $this->eName = (string) $m->name;
        $this->eCategory = (string) $m->category_id;
        $this->eUnit = (string) ($m->unit ?? 'm2');
        $this->ePrice = (string) $this->priceOf($m);
        $this->eMin = (string) (float) ($m->minimum_stock ?? 0);
        $this->eMax = (string) (float) ($m->maximum_stock ?? 0);
        $this->eLead = (string) (int) ($m->lead_time_days ?? 3);
        $this->eShelf = $m->shelf_life_days ? (string) $m->shelf_life_days : '';
        $this->eBatch = (bool) ($m->is_batch_tracked ?? false);
        $this->eActive = (bool) ($m->is_active ?? true);
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
        $m = Material::find($this->editId);
        if (!$m) { $this->closeEdit(); return; }

        $data = $this->validate([
            'eName' => ['required', 'string', 'min:2', 'max:150', Rule::unique('materials', 'name')->ignore($m->id)],
            'eCategory' => 'required|integer|exists:categories,id',
            'eUnit' => 'required|string|max:20',
            'ePrice' => 'required|numeric|min:0|max:100000000',
            'eMin' => 'required|numeric|min:0|max:1000000',
            'eMax' => 'nullable|numeric|min:0|max:1000000',
            'eLead' => 'required|integer|min:0|max:365',
            'eShelf' => 'nullable|integer|min:1|max:3650',
        ], [
            'eName.required' => 'Nama bahan wajib diisi.',
            'eName.unique' => 'Nama bahan sudah dipakai.',
            'eCategory.required' => 'Divisi produksi wajib dipilih.',
            'ePrice.required' => 'Harga satuan wajib diisi.',
            'eMin.required' => 'Batas minimum (ROP) wajib diisi.',
        ]);

        if ($data['eMax'] !== null && $data['eMax'] !== '' && (float) $data['eMax'] > 0 && (float) $data['eMax'] < (float) $data['eMin']) {
            $this->addError('eMax', 'Stok maksimum tidak boleh lebih kecil dari batas minimum.');
            return;
        }

        $fill = [
            'name' => trim($data['eName']),
            'category_id' => (int) $data['eCategory'],
            'minimum_stock' => round((float) $data['eMin'], 4),
        ];
        if ($this->has('slug')) $fill['slug'] = $this->slugFor($fill['name'], $m->id);
        if ($this->has('unit')) $fill['unit'] = trim($data['eUnit']);
        if ($this->has('maximum_stock')) $fill['maximum_stock'] = round((float) ($data['eMax'] ?? 0), 4);
        if ($this->has('lead_time_days')) $fill['lead_time_days'] = (int) $data['eLead'];
        if ($this->has('shelf_life_days')) $fill['shelf_life_days'] = $data['eShelf'] !== '' ? (int) $data['eShelf'] : null;
        if ($this->has('is_batch_tracked')) $fill['is_batch_tracked'] = $this->eBatch;
        if ($this->has('is_active')) $fill['is_active'] = $this->eActive;
        $fill = $this->applyFill($fill, round((float) $data['ePrice'], 2));

        $m->forceFill($fill)->save();

        $this->closeEdit();
        session()->flash('mat_success', 'Bahan "' . $m->name . '" diperbarui. Jumlah stok hanya berubah lewat penerimaan, produksi, atau opname.');
    }

    public function toggleActive($id): void
    {
        if (!$this->has('is_active')) return;
        $m = Material::find($id);
        if (!$m) return;

        $m->forceFill(['is_active' => !(bool) $m->is_active])->save();
        session()->flash('mat_success', 'Bahan "' . $m->name . '" sekarang ' . ($m->is_active ? 'aktif' : 'nonaktif') . '. Stok dan riwayat tetap utuh.');
    }

    /* ================= HAPUS ================= */

    public function openDelete($id): void
    {
        $m = Material::find($id);
        if (!$m) return;
        $this->deleteId = (int) $m->id;
        $this->detailId = null;
    }

    public function closeDelete(): void { $this->deleteId = null; }

    public function confirmDelete(): void
    {
        $m = Material::find($this->deleteId);
        if (!$m) { $this->closeDelete(); return; }

        if ($this->refCount($m->id) > 0) {
            session()->flash('mat_error', 'Bahan ini sudah punya jejak transaksi dan tidak boleh dihapus. Gunakan nonaktifkan.');
            $this->closeDelete();
            return;
        }

        try {
            DB::transaction(function () use ($m) {
                if ($this->hasBatchTable) DB::table('material_batches')->where('material_id', $m->id)->delete();
                $bomIds = Schema::hasTable('service_bom_items') ? DB::table('service_bom_items')->where('material_id', $m->id)->delete() : 0;
                $m->delete();
            });
        } catch (\Throwable $e) {
            session()->flash('mat_error', 'Gagal menghapus: ' . $e->getMessage());
            return;
        }

        $name = $m->name;
        $this->closeDelete();
        session()->flash('mat_success', 'Bahan "' . $name . '" dihapus karena belum memiliki jejak transaksi.');
    }

    public function deactivateInstead(): void
    {
        if (!$this->has('is_active')) return;
        $m = Material::find($this->deleteId);
        if (!$m) { $this->closeDelete(); return; }

        $m->forceFill(['is_active' => false])->save();
        $name = $m->name;
        $this->closeDelete();
        session()->flash('mat_success', 'Bahan "' . $name . '" dinonaktifkan. Tidak dipakai lagi untuk pesanan baru, riwayat aman.');
    }

    private function refCount(int $id): int
    {
        $refs = OrderItem::where('material_id', $id)->count();
        if (!empty($this->moveCols)) $refs += DB::table('material_movements')->where('material_id', $id)->count();
        if (Schema::hasTable('service_bom_items')) $refs += DB::table('service_bom_items')->where('material_id', $id)->count();
        return $refs;
    }

    /* ================= AUDIT ================= */

    private function writeMovement(Material $m, string $type, string $subType, float $qty, float $unitCost, string $note): void
    {
        if (empty($this->moveCols)) return;

        $map = [
            'material_id' => $m->id,
            'movement_type' => $type,
            'sub_type' => $subType,
            'quantity' => $qty,
            'unit' => (string) ($m->unit ?? 'm2'),
            'unit_cost' => $unitCost,
            'total_cost' => round($qty * $unitCost, 2),
            'movement_date' => now()->toDateString(),
            'reference_type' => 'material_setup',
            'reference_id' => $m->id,
            'notes' => $note,
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

    /* ================= DETAIL & FILTER ================= */

    public function openDetail($id): void { $this->detailId = (int) $id; }
    public function closeDetail(): void { $this->detailId = null; }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = 'all';
        $this->statusFilter = 'all';
        $this->sort = 'urgency';
    }

    private function buildRows(): array
    {
        $cats = Category::pluck('name', 'id')->all();
        $materials = Material::orderBy('name')->get();

        $since = now()->subDays(29)->startOfDay()->toDateString();
        $usage = [];
        $hasUsageData = false;
        if (!empty($this->moveCols)) {
            $hasUsageData = DB::table('material_movements')->where('movement_type', 'out')->exists();
            $rows = DB::table('material_movements')
                ->where('movement_type', 'out')
                ->where('movement_date', '>=', $since)
                ->selectRaw('material_id, COALESCE(SUM(quantity),0) as q')
                ->groupBy('material_id')->get();
            foreach ($rows as $r) $usage[(int) $r->material_id] = (float) $r->q;
        }

        $expiring = [];
        if ($this->hasBatchTable) {
            $rows = DB::table('material_batches')
                ->where('status', 'active')
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<=', now()->addDays(30)->toDateString())
                ->selectRaw('material_id, COUNT(*) as c, COALESCE(SUM(remaining_quantity),0) as q')
                ->groupBy('material_id')->get();
            foreach ($rows as $r) $expiring[(int) $r->material_id] = ['count' => (int) $r->c, 'qty' => (float) $r->q];
        }

        $out = [];
        foreach ($materials as $m) {
            $stock = (float) $m->stock;
            $reserved = (float) $m->stock_reserved;
            $net = round($stock - $reserved, 4);
            $min = (float) ($m->minimum_stock ?? 0);
            $max = (float) ($m->maximum_stock ?? 0);
            $price = $this->priceOf($m);
            $used = $usage[$m->id] ?? 0.0;
            $avg = $hasUsageData ? round($used / 30, 4) : null;
            $cover = ($avg !== null && $avg > 0) ? (int) floor(max(0, $net) / $avg) : null;

            if ($net <= 0) $status = 'habis';
            elseif ($min <= 0) $status = 'norop';
            elseif ($net <= $min) $status = 'kritis';
            elseif ($net <= $min * 1.5) $status = 'reorder';
            elseif ($max > 0 && $net > $max) $status = 'over';
            else $status = 'aman';

            $target = $max > 0 ? $max : max($min * 2, $min + 1);
            $suggest = max(0, round($target - $net, 4));
            if ($suggest <= 0 && $min > 0) $suggest = $min;

            $out[] = [
                'id' => (int) $m->id,
                'name' => (string) $m->name,
                'category' => (string) ($cats[$m->category_id] ?? 'Tanpa divisi'),
                'category_id' => (int) $m->category_id,
                'unit' => $this->unitLabel((string) ($m->unit ?? 'm2')),
                'price' => $price,
                'no_price' => $price <= 0,
                'stock' => $stock,
                'reserved' => $reserved,
                'net' => $net,
                'min' => $min,
                'max' => $max,
                'no_rop' => $min <= 0,
                'value' => round($stock * $price, 2),
                'net_value' => round(max(0, $net) * $price, 2),
                'status' => $status,
                'coverage' => $min > 0 ? max(0, min(100, (int) round(max(0, $net) / $min * 100))) : null,
                'lead' => max(0, (int) ($m->lead_time_days ?? 3)),
                'avg' => $avg,
                'used30' => round($used, 2),
                'cover' => $cover,
                'suggest' => $suggest,
                'suggest_cost' => round($suggest * $price, 2),
                'active' => (bool) ($m->is_active ?? true),
                'batch' => (bool) ($m->is_batch_tracked ?? false),
                'shelf' => $m->shelf_life_days ? (int) $m->shelf_life_days : null,
                'expiring' => $expiring[$m->id] ?? null,
                'refs' => $this->refCount($m->id),
                'created' => $m->created_at?->format('d M Y'),
            ];
        }

        return $out;
    }

    private function filterRows(array $rows): array
    {
        $term = mb_strtolower(trim($this->search));

        $out = array_values(array_filter($rows, function ($r) use ($term) {
            if ($this->categoryFilter !== 'all' && (string) $r['category_id'] !== (string) $this->categoryFilter) return false;
            if ($this->statusFilter === 'critical' && !in_array($r['status'], ['habis', 'kritis'], true)) return false;
            if ($this->statusFilter === 'reorder' && $r['status'] !== 'reorder') return false;
            if ($this->statusFilter === 'safe' && !in_array($r['status'], ['aman', 'over'], true)) return false;
            if ($this->statusFilter === 'norop' && !$r['no_rop']) return false;
            if ($this->statusFilter === 'noprice' && !$r['no_price']) return false;
            if ($this->statusFilter === 'inactive' && $r['active']) return false;
            if ($term === '') return true;
            return str_contains(mb_strtolower($r['name']), $term) || str_contains(mb_strtolower($r['category']), $term);
        }));

        usort($out, function ($a, $b) {
            if ($this->sort === 'name') return strcmp($a['name'], $b['name']);
            if ($this->sort === 'value') return $b['value'] <=> $a['value'];
            if ($this->sort === 'stock') return $a['net'] <=> $b['net'];
            if ($this->sort === 'cover') return [$a['cover'] ?? 99999, $a['name']] <=> [$b['cover'] ?? 99999, $b['name']];
            if ($this->sort === 'category') return [$a['category'], $a['name']] <=> [$b['category'], $b['name']];
            $rank = ['habis' => 0, 'kritis' => 1, 'reorder' => 2, 'norop' => 3, 'over' => 4, 'aman' => 5];
            return [$rank[$a['status']] ?? 9, $a['coverage'] ?? 999, $a['name']] <=> [$rank[$b['status']] ?? 9, $b['coverage'] ?? 999, $b['name']];
        });

        return $out;
    }

    /* ================= EKSPOR ================= */

    private function exportHead(): array
    {
        return ['Nama Bahan', 'Divisi', 'Satuan', 'Harga Satuan', 'Stok Fisik', 'Teralokasi', 'Stok Bersih', 'Batas Minimum', 'Stok Maksimum', 'Cakupan ROP %', 'Pemakaian 30 Hari', 'Rata-rata /Hari', 'Cukup Untuk (hari)', 'Lead Time (hari)', 'Nilai Stok', 'Status', 'Saran Beli', 'Estimasi Biaya Beli', 'Aktif'];
    }

    private function exportLine(array $r): array
    {
        return [
            $r['name'], $r['category'], $r['unit'], $r['price'],
            $r['stock'], $r['reserved'], $r['net'], $r['min'], $r['max'],
            $r['coverage'] ?? '-', $r['used30'], $r['avg'] ?? '-', $r['cover'] ?? '-', $r['lead'],
            $r['value'], $r['status'], $r['suggest'], $r['suggest_cost'],
            $r['active'] ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function exportExcel()
    {
        $rows = $this->filterRows($this->buildRows());

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= '<Styles><Style ss:ID="h"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#0A3F33" ss:Pattern="Solid"/></Style>'
              . '<Style ss:ID="n"><NumberFormat ss:Format="#,##0.00"/></Style></Styles>' . "\n";
        $xml .= '<Worksheet ss:Name="Bahan Baku"><Table>' . "\n";
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

        return response()->streamDownload(function () use ($xml) { echo $xml; }, 'bahan-baku-' . now()->format('Ymd-His') . '.xls', [
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
        }, 'bahan-baku-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $all = $this->buildRows();
        $rows = $this->filterRows($all);

        $value = 0.0; $netValue = 0.0; $critical = 0; $reorder = 0; $safe = 0; $noRop = 0; $noPrice = 0; $restock = 0.0;
        foreach ($all as $r) {
            if (!$r['active']) continue;
            $value += $r['value'];
            $netValue += $r['net_value'];
            if (in_array($r['status'], ['habis', 'kritis'], true)) { $critical++; $restock += $r['suggest_cost']; }
            if ($r['status'] === 'reorder') $reorder++;
            if (in_array($r['status'], ['aman', 'over'], true)) $safe++;
            if ($r['no_rop']) $noRop++;
            if ($r['no_price']) $noPrice++;
        }

        $detail = null;
        if ($this->detailId) {
            $detail = collect($all)->firstWhere('id', $this->detailId);
            if ($detail) {
                $detail['movements'] = [];
                if (!empty($this->moveCols)) {
                    $detail['movements'] = DB::table('material_movements')
                        ->where('material_id', $this->detailId)
                        ->orderByDesc('id')->limit(8)->get()
                        ->map(function ($x) {
                            $type = strtolower((string) ($x->movement_type ?? ''));
                            $q = (float) ($x->quantity ?? 0);
                            return [
                                'date' => !empty($x->movement_date) ? Carbon::parse($x->movement_date)->format('d M Y') : '—',
                                'type' => $type,
                                'sub' => (string) ($x->sub_type ?? '—'),
                                'qty' => $type === 'out' ? -abs($q) : abs($q),
                                'value' => (float) ($x->total_cost ?? 0),
                                'note' => (string) ($x->notes ?? ''),
                            ];
                        })->all();
                }
                $detail['batches'] = [];
                if ($this->hasBatchTable) {
                    $detail['batches'] = DB::table('material_batches')
                        ->where('material_id', $this->detailId)
                        ->where('status', 'active')
                        ->orderBy('expiry_date')->limit(6)->get()
                        ->map(fn($x) => [
                            'number' => (string) $x->batch_number,
                            'qty' => (float) $x->remaining_quantity,
                            'cost' => (float) $x->unit_cost,
                            'received' => $x->received_date ? Carbon::parse($x->received_date)->format('d M Y') : '—',
                            'expiry' => $x->expiry_date ? Carbon::parse($x->expiry_date)->format('d M Y') : null,
                            'days' => $x->expiry_date ? (int) now()->startOfDay()->diffInDays(Carbon::parse($x->expiry_date)->startOfDay(), false) : null,
                        ])->all();
                }
            }
        }

        $deleteTarget = $this->deleteId ? collect($all)->firstWhere('id', $this->deleteId) : null;

        $opnameRoute = null;
        foreach (['admin.stock-opname', 'admin.opname', 'admin.stock.opname', 'admin.stock_opname'] as $r) {
            if (Route::has($r)) { $opnameRoute = $r; break; }
        }

        return view('livewire.admin.materials', [
            'rows' => $rows,
            'detail' => $detail,
            'deleteTarget' => $deleteTarget,
            'opnameRoute' => $opnameRoute,
            'categories' => Category::orderBy('name')->get(['id', 'name'])->map(fn($c) => ['id' => $c->id, 'name' => (string) $c->name])->all(),
            'stats' => [
                'total' => count($all),
                'shown' => count($rows),
                'value' => round($value, 2),
                'net_value' => round($netValue, 2),
                'critical' => $critical,
                'reorder' => $reorder,
                'safe' => $safe,
                'no_rop' => $noRop,
                'no_price' => $noPrice,
                'restock' => round($restock, 2),
            ],
            'hasActive' => $this->has('is_active'),
            'hasMax' => $this->has('maximum_stock'),
            'hasLead' => $this->has('lead_time_days'),
            'hasBatch' => $this->has('is_batch_tracked'),
            'hasShelf' => $this->has('shelf_life_days'),
            'hasFilter' => $this->search !== '' || $this->categoryFilter !== 'all' || $this->statusFilter !== 'all',
        ]);
    }
}