<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Service;
use App\Models\Category;
use App\Models\Material;
use App\Models\Size;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.admin')]
#[Title('Kelola Layanan')]
class Services extends Component
{
    public string $search = '';
    public string $categoryFilter = 'all';
    public string $statusFilter = 'all';
    public string $bomFilter = 'all';
    public string $sort = 'name';

    public bool $createOpen = false;
    public string $cName = '';
    public string $cCategory = '';
    public string $cPrice = '';
    public string $cDescription = '';
    public bool $cActive = true;

    public ?int $editId = null;
    public string $eName = '';
    public string $eCategory = '';
    public string $ePrice = '';
    public string $eDescription = '';
    public bool $eActive = true;

    public ?int $deleteId = null;
    public ?int $dupId = null;
    public string $dupName = '';
    public bool $dupBom = true;

    public ?int $detailId = null;
    public string $simArea = '1';
    public string $simQty = '1';

    public bool $bulkOpen = false;
    public string $bulkCategory = 'all';
    public string $bulkPercent = '5';
    public string $bulkDirection = 'up';

    public array $cols = [];
    public bool $bomReady = false;
    public ?string $qtyCol = null;
    public ?string $scrapCol = null;
    public ?string $fkCol = null;
    public bool $ordersSoft = false;
    public bool $hasSizeCat = false;

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'admin'], true)) {
            abort(403, 'Master layanan hanya untuk owner dan admin.');
        }

        $this->cols = Schema::hasTable('services') ? Schema::getColumnListing('services') : [];
        $this->ordersSoft = Schema::hasColumn('orders', 'deleted_at');
        $this->hasSizeCat = Schema::hasColumn('sizes', 'category_id');

        if (Schema::hasTable('service_boms') && Schema::hasTable('service_bom_items')) {
            $i = Schema::getColumnListing('service_bom_items');
            foreach (['quantity_per_unit', 'quantity_per_m2', 'quantity', 'usage_per_unit', 'qty_per_unit'] as $c) {
                if (in_array($c, $i, true)) { $this->qtyCol = $c; break; }
            }
            foreach (['scrap_percentage', 'scrap_allowance', 'scrap_percent', 'waste_percentage'] as $c) {
                if (in_array($c, $i, true)) { $this->scrapCol = $c; break; }
            }
            foreach (['service_bom_id', 'bom_id'] as $c) {
                if (in_array($c, $i, true)) { $this->fkCol = $c; break; }
            }
            $this->bomReady = $this->qtyCol && $this->fkCol;
        }
    }

    private function has(string $c): bool
    {
        return in_array($c, $this->cols, true);
    }

    private function slugFor(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'layanan';
        $slug = $base;
        $i = 2;
        while (Service::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }

    /* ================= CRUD ================= */

    public function openCreate(): void
    {
        $this->createOpen = true;
        $this->cName = '';
        $this->cCategory = '';
        $this->cPrice = '';
        $this->cDescription = '';
        $this->cActive = true;
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
            'cName' => 'required|string|min:3|max:150|unique:services,name',
            'cCategory' => 'required|integer|exists:categories,id',
            'cPrice' => 'required|numeric|min:0|max:100000000',
        ];
        if ($this->has('description')) $rules['cDescription'] = 'nullable|string|max:500';

        $data = $this->validate($rules, [
            'cName.required' => 'Nama layanan wajib diisi.',
            'cName.min' => 'Nama layanan minimal 3 karakter.',
            'cName.unique' => 'Nama layanan ini sudah terdaftar.',
            'cCategory.required' => 'Divisi produksi wajib dipilih.',
            'cCategory.exists' => 'Divisi tidak ditemukan.',
            'cPrice.required' => 'Harga jasa per m² wajib diisi.',
            'cPrice.numeric' => 'Harga harus berupa angka.',
            'cPrice.min' => 'Harga tidak boleh negatif.',
        ]);

        $fill = [
            'name' => trim($data['cName']),
            'category_id' => (int) $data['cCategory'],
            'price_per_m2' => round((float) $data['cPrice'], 2),
        ];
        if ($this->has('slug')) $fill['slug'] = $this->slugFor($fill['name']);
        if ($this->has('description')) $fill['description'] = trim((string) ($data['cDescription'] ?? ''));
        if ($this->has('is_active')) $fill['is_active'] = $this->cActive;

        $s = new Service();
        $s->forceFill($fill)->save();

        $this->closeCreate();
        session()->flash('svc_success', 'Layanan "' . $s->name . '" ditambahkan. Lengkapi BOM agar MRP dapat menghitung kebutuhan bahannya.');
    }

    public function openEdit($id): void
    {
        $s = Service::find($id);
        if (!$s) return;

        $this->editId = (int) $s->id;
        $this->eName = (string) $s->name;
        $this->eCategory = (string) $s->category_id;
        $this->ePrice = (string) (float) ($s->price_per_m2 ?? 0);
        $this->eDescription = (string) ($s->description ?? '');
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
        $s = Service::find($this->editId);
        if (!$s) { $this->closeEdit(); return; }

        $rules = [
            'eName' => ['required', 'string', 'min:3', 'max:150', Rule::unique('services', 'name')->ignore($s->id)],
            'eCategory' => 'required|integer|exists:categories,id',
            'ePrice' => 'required|numeric|min:0|max:100000000',
        ];
        if ($this->has('description')) $rules['eDescription'] = 'nullable|string|max:500';

        $data = $this->validate($rules, [
            'eName.required' => 'Nama layanan wajib diisi.',
            'eName.unique' => 'Nama layanan sudah dipakai.',
            'eCategory.required' => 'Divisi produksi wajib dipilih.',
            'ePrice.required' => 'Harga jasa per m² wajib diisi.',
            'ePrice.numeric' => 'Harga harus berupa angka.',
        ]);

        $fill = [
            'name' => trim($data['eName']),
            'category_id' => (int) $data['eCategory'],
            'price_per_m2' => round((float) $data['ePrice'], 2),
        ];
        if ($this->has('slug')) $fill['slug'] = $this->slugFor($fill['name'], $s->id);
        if ($this->has('description')) $fill['description'] = trim((string) ($data['eDescription'] ?? ''));
        if ($this->has('is_active')) $fill['is_active'] = $this->eActive;

        $s->forceFill($fill)->save();

        $this->closeEdit();
        session()->flash('svc_success', 'Layanan "' . $s->name . '" diperbarui. Harga baru berlaku untuk pesanan berikutnya.');
    }

    public function toggleActive($id): void
    {
        if (!$this->has('is_active')) return;
        $s = Service::find($id);
        if (!$s) return;

        $s->forceFill(['is_active' => !(bool) $s->is_active])->save();
        session()->flash('svc_success', 'Layanan "' . $s->name . '" sekarang ' . ($s->is_active ? 'aktif' : 'nonaktif') . '. Riwayat pesanan tetap utuh.');
    }

    public function openDelete($id): void
    {
        $s = Service::find($id);
        if (!$s) return;
        $this->deleteId = (int) $s->id;
        $this->detailId = null;
    }

    public function closeDelete(): void { $this->deleteId = null; }

    public function confirmDelete(): void
    {
        $s = Service::find($this->deleteId);
        if (!$s) { $this->closeDelete(); return; }

        $refs = OrderItem::where('service_id', $s->id)->count();
        if ($refs > 0) {
            session()->flash('svc_error', 'Layanan ini dipakai ' . $refs . ' baris pesanan dan tidak boleh dihapus. Gunakan nonaktifkan agar riwayat tetap sahih.');
            $this->closeDelete();
            return;
        }

        try {
            DB::transaction(function () use ($s) {
                if ($this->bomReady) {
                    $bomIds = DB::table('service_boms')->where('service_id', $s->id)->pluck('id')->all();
                    if (!empty($bomIds)) {
                        DB::table('service_bom_items')->whereIn($this->fkCol, $bomIds)->delete();
                        DB::table('service_boms')->where('service_id', $s->id)->delete();
                    }
                }
                $s->delete();
            });
        } catch (\Throwable $e) {
            session()->flash('svc_error', 'Gagal menghapus: ' . $e->getMessage());
            return;
        }

        $name = $s->name;
        $this->closeDelete();
        session()->flash('svc_success', 'Layanan "' . $name . '" beserta resepnya dihapus karena belum pernah dipakai.');
    }

    public function deactivateInstead(): void
    {
        if (!$this->has('is_active')) return;
        $s = Service::find($this->deleteId);
        if (!$s) { $this->closeDelete(); return; }

        $s->forceFill(['is_active' => false])->save();
        $name = $s->name;
        $this->closeDelete();
        session()->flash('svc_success', 'Layanan "' . $name . '" dinonaktifkan. Tidak muncul lagi di pesanan baru, riwayat lama aman.');
    }

    /* ================= DUPLIKAT ================= */

    public function openDuplicate($id): void
    {
        $s = Service::find($id);
        if (!$s) return;
        $this->dupId = (int) $s->id;
        $this->dupName = $s->name . ' (Varian)';
        $this->dupBom = true;
        $this->detailId = null;
        $this->resetErrorBag('dupName');
    }

    public function closeDuplicate(): void
    {
        $this->dupId = null;
        $this->resetErrorBag('dupName');
    }

    public function saveDuplicate(): void
    {
        $this->validate([
            'dupName' => 'required|string|min:3|max:150|unique:services,name',
        ], [
            'dupName.required' => 'Nama varian wajib diisi.',
            'dupName.unique' => 'Nama itu sudah dipakai layanan lain.',
        ]);

        $src = Service::find($this->dupId);
        if (!$src) { $this->closeDuplicate(); return; }

        try {
            $newName = trim($this->dupName);

            DB::transaction(function () use ($src, $newName) {
                $fill = [
                    'name' => $newName,
                    'category_id' => $src->category_id,
                    'price_per_m2' => (float) ($src->price_per_m2 ?? 0),
                ];
                if ($this->has('slug')) $fill['slug'] = $this->slugFor($newName);
                if ($this->has('description')) $fill['description'] = (string) ($src->description ?? '');
                if ($this->has('is_active')) $fill['is_active'] = false;

                $new = new Service();
                $new->forceFill($fill)->save();

                if ($this->dupBom && $this->bomReady) {
                    $bom = DB::table('service_boms')->where('service_id', $src->id)->first();
                    if ($bom) {
                        $bomData = ['service_id' => $new->id, 'created_at' => now(), 'updated_at' => now()];
                        $bCols = Schema::getColumnListing('service_boms');
                        foreach (['name', 'notes', 'is_active', 'version', 'scrap_percentage'] as $c) {
                            if (in_array($c, $bCols, true) && isset($bom->{$c})) $bomData[$c] = $bom->{$c};
                        }
                        if (in_array('name', $bCols, true)) $bomData['name'] = 'Resep ' . $newName;
                        if (in_array('version', $bCols, true)) $bomData['version'] = 1;

                        $newBomId = DB::table('service_boms')->insertGetId($bomData);
                        $items = DB::table('service_bom_items')->where($this->fkCol, $bom->id)->get();
                        $iCols = Schema::getColumnListing('service_bom_items');

                        foreach ($items as $it) {
                            $row = (array) $it;
                            unset($row['id']);
                            $row[$this->fkCol] = $newBomId;
                            if (in_array('created_at', $iCols, true)) $row['created_at'] = now();
                            if (in_array('updated_at', $iCols, true)) $row['updated_at'] = now();
                            DB::table('service_bom_items')->insert($row);
                        }
                    }
                }
            });
        } catch (\Throwable $e) {
            session()->flash('svc_error', 'Gagal menduplikasi: ' . $e->getMessage());
            return;
        }

        $name = trim($this->dupName);
        $this->closeDuplicate();
        session()->flash('svc_success', 'Varian "' . $name . '" dibuat sebagai nonaktif. Sesuaikan harga & resep lalu aktifkan.');
    }

    /* ================= HARGA MASSAL ================= */

    public function openBulk(): void
    {
        $this->bulkOpen = true;
        $this->bulkCategory = 'all';
        $this->bulkPercent = '5';
        $this->bulkDirection = 'up';
        $this->resetErrorBag(['bulkPercent']);
    }

    public function closeBulk(): void
    {
        $this->bulkOpen = false;
        $this->resetErrorBag(['bulkPercent']);
    }

    public function applyBulk(): void
    {
        $this->validate([
            'bulkPercent' => 'required|numeric|min:0.1|max:200',
            'bulkDirection' => 'required|in:up,down',
        ], [
            'bulkPercent.required' => 'Persentase penyesuaian wajib diisi.',
            'bulkPercent.min' => 'Persentase minimal 0,1%.',
            'bulkPercent.max' => 'Persentase maksimal 200%.',
        ]);

        $pct = (float) $this->bulkPercent / 100;
        $factor = $this->bulkDirection === 'up' ? (1 + $pct) : (1 - $pct);
        if ($factor <= 0) {
            $this->addError('bulkPercent', 'Penurunan sebesar itu membuat harga nol atau negatif.');
            return;
        }

        $q = Service::query();
        if ($this->bulkCategory !== 'all') $q->where('category_id', (int) $this->bulkCategory);

        $count = 0;
        DB::transaction(function () use ($q, $factor, &$count) {
            foreach ($q->get() as $s) {
                $s->forceFill(['price_per_m2' => round((float) $s->price_per_m2 * $factor, 2)])->save();
                $count++;
            }
        });

        $this->closeBulk();
        session()->flash('svc_success', $count . ' layanan disesuaikan harganya ' . ($this->bulkDirection === 'up' ? 'naik' : 'turun') . ' ' . $this->bulkPercent . '%. Pesanan lama tidak berubah.');
    }

    /* ================= DETAIL & FILTER ================= */

    public function openDetail($id): void
    {
        $this->detailId = (int) $id;
        $this->simArea = '1';
        $this->simQty = '1';
    }

    public function closeDetail(): void { $this->detailId = null; }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = 'all';
        $this->statusFilter = 'all';
        $this->bomFilter = 'all';
        $this->sort = 'name';
    }

    private function bomData(): array
    {
        if (!$this->bomReady) return [];

        $rows = DB::table('service_bom_items')
            ->join('service_boms', 'service_boms.id', '=', 'service_bom_items.' . $this->fkCol)
            ->join('materials', 'materials.id', '=', 'service_bom_items.material_id')
            ->selectRaw('service_boms.service_id as sid')
            ->selectRaw('service_bom_items.material_id as mid')
            ->selectRaw('materials.name as mname')
            ->selectRaw('materials.unit as munit')
            ->selectRaw('COALESCE(materials.price_per_m2,0) as mprice')
            ->selectRaw('service_bom_items.' . $this->qtyCol . ' as qty')
            ->selectRaw($this->scrapCol ? 'COALESCE(service_bom_items.' . $this->scrapCol . ',0) as scrap' : '0 as scrap')
            ->get();

        $out = [];
        foreach ($rows as $r) {
            $sid = (int) $r->sid;
            $qty = (float) $r->qty;
            $scrap = (float) $r->scrap;
            $gross = $qty * (1 + $scrap / 100);
            $cost = $gross * (float) $r->mprice;

            if (!isset($out[$sid])) $out[$sid] = ['items' => [], 'cost' => 0.0, 'qty' => 0.0, 'count' => 0];
            $out[$sid]['items'][] = [
                'name' => (string) $r->mname,
                'unit' => (string) ($r->munit ?? 'm²'),
                'qty' => round($qty, 4),
                'scrap' => round($scrap, 2),
                'gross' => round($gross, 4),
                'price' => (float) $r->mprice,
                'cost' => round($cost, 2),
            ];
            $out[$sid]['cost'] += $cost;
            $out[$sid]['qty'] += $gross;
            $out[$sid]['count']++;
        }

        foreach ($out as $sid => $v) {
            $out[$sid]['cost'] = round($v['cost'], 2);
            $out[$sid]['qty'] = round($v['qty'], 4);
        }

        return $out;
    }

    private function buildRows(): array
    {
        $cats = Category::pluck('name', 'id')->all();
        $services = Service::orderBy('name')->get();

        $usage = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->when($this->ordersSoft, fn($q) => $q->whereNull('orders.deleted_at'))
            ->whereNotIn('orders.status', ['cancelled', 'rejected'])
            ->selectRaw('order_items.service_id as sid')
            ->selectRaw('COUNT(*) as lines')
            ->selectRaw('COALESCE(SUM(order_items.area_m2),0) as area')
            ->selectRaw('COALESCE(SUM(order_items.final_subtotal),0) as revenue')
            ->groupBy('order_items.service_id')
            ->get()->keyBy('sid');

        $refs = OrderItem::selectRaw('service_id, COUNT(*) as c')->groupBy('service_id')->pluck('c', 'service_id');
        $bom = $this->bomData();

        $rows = [];
        foreach ($services as $s) {
            $u = $usage[$s->id] ?? null;
            $b = $bom[$s->id] ?? null;
            $price = (float) ($s->price_per_m2 ?? 0);
            $matCost = $b['cost'] ?? 0.0;

            $rows[] = [
                'id' => (int) $s->id,
                'name' => (string) $s->name,
                'slug' => (string) ($s->slug ?? Str::slug($s->name)),
                'description' => (string) ($s->description ?? ''),
                'category_id' => (int) $s->category_id,
                'category' => (string) ($cats[$s->category_id] ?? 'Tanpa divisi'),
                'price' => round($price, 2),
                'active' => (bool) ($s->is_active ?? true),
                'has_bom' => (bool) $b,
                'bom_count' => $b['count'] ?? 0,
                'bom_cost' => $matCost,
                'bom_qty' => $b['qty'] ?? 0.0,
                'bom_items' => $b['items'] ?? [],
                'fair_price' => round($price + $matCost, 2),
                'material_ratio' => $price > 0 ? (int) round($matCost / $price * 100) : 0,
                'orders' => (int) ($u->lines ?? 0),
                'area' => round((float) ($u->area ?? 0), 2),
                'revenue' => round((float) ($u->revenue ?? 0), 2),
                'refs' => (int) ($refs[$s->id] ?? 0),
                'created' => $s->created_at?->format('d M Y'),
            ];
        }

        return $rows;
    }

    private function filterRows(array $rows): array
    {
        $term = mb_strtolower(trim($this->search));

        $out = array_values(array_filter($rows, function ($r) use ($term) {
            if ($this->categoryFilter !== 'all' && (string) $r['category_id'] !== (string) $this->categoryFilter) return false;
            if ($this->statusFilter === 'active' && !$r['active']) return false;
            if ($this->statusFilter === 'inactive' && $r['active']) return false;
            if ($this->bomFilter === 'with' && !$r['has_bom']) return false;
            if ($this->bomFilter === 'without' && $r['has_bom']) return false;
            if ($this->statusFilter === 'unused' && $r['orders'] > 0) return false;
            if ($term === '') return true;
            return str_contains(mb_strtolower($r['name']), $term)
                || str_contains(mb_strtolower($r['slug']), $term)
                || str_contains(mb_strtolower($r['category']), $term);
        }));

        usort($out, function ($a, $b) {
            if ($this->sort === 'price_high') return $b['price'] <=> $a['price'];
            if ($this->sort === 'price_low') return $a['price'] <=> $b['price'];
            if ($this->sort === 'revenue') return $b['revenue'] <=> $a['revenue'];
            if ($this->sort === 'popular') return $b['orders'] <=> $a['orders'];
            if ($this->sort === 'category') return [$a['category'], $a['name']] <=> [$b['category'], $b['name']];
            if ($this->sort === 'nobom') return [$a['has_bom'] ? 1 : 0, $a['name']] <=> [$b['has_bom'] ? 1 : 0, $b['name']];
            return strcmp($a['name'], $b['name']);
        });

        return $out;
    }

    /* ================= EKSPOR ================= */

    private function exportHead(): array
    {
        return ['Nama Layanan', 'Slug', 'Divisi', 'Harga Jasa /m²', 'Status', 'Punya BOM', 'Jumlah Bahan Resep', 'Biaya Bahan /m²', 'Estimasi Harga Wajar /m²', 'Rasio Bahan %', 'Baris Pesanan', 'Total Luas m²', 'Omzet', 'Dibuat'];
    }

    private function exportLine(array $r): array
    {
        return [
            $r['name'], $r['slug'], $r['category'], $r['price'],
            $r['active'] ? 'Aktif' : 'Nonaktif',
            $r['has_bom'] ? 'Ya' : 'Belum',
            $r['bom_count'], $r['bom_cost'], $r['fair_price'], $r['material_ratio'],
            $r['orders'], $r['area'], $r['revenue'], $r['created'] ?? '-',
        ];
    }

    public function exportExcel()
    {
        $rows = $this->filterRows($this->buildRows());

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= '<Styles><Style ss:ID="h"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#0A3F33" ss:Pattern="Solid"/></Style>'
              . '<Style ss:ID="n"><NumberFormat ss:Format="#,##0.00"/></Style></Styles>' . "\n";
        $xml .= '<Worksheet ss:Name="Master Layanan"><Table>' . "\n";
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

        return response()->streamDownload(function () use ($xml) { echo $xml; }, 'master-layanan-' . now()->format('Ymd-His') . '.xls', [
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
        }, 'master-layanan-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $all = $this->buildRows();
        $rows = $this->filterRows($all);

        $active = 0; $withBom = 0; $unused = 0; $revenue = 0.0; $priceSum = 0.0;
        $minPrice = null; $maxPrice = 0.0;
        $noBom = [];
        $catStats = [];

        foreach ($all as $r) {
            if ($r['active']) $active++;
            if ($r['has_bom']) $withBom++; else $noBom[] = $r;
            if ($r['orders'] === 0) $unused++;
            $revenue += $r['revenue'];
            $priceSum += $r['price'];
            $minPrice = $minPrice === null ? $r['price'] : min($minPrice, $r['price']);
            $maxPrice = max($maxPrice, $r['price']);

            if (!isset($catStats[$r['category']])) $catStats[$r['category']] = ['count' => 0, 'bom' => 0, 'revenue' => 0.0];
            $catStats[$r['category']]['count']++;
            if ($r['has_bom']) $catStats[$r['category']]['bom']++;
            $catStats[$r['category']]['revenue'] += $r['revenue'];
        }

        $total = count($all);
        $coverage = $total > 0 ? (int) round($withBom / $total * 100) : 0;
        $maxRev = $catStats ? max(array_column($catStats, 'revenue')) : 0;
        $hues = ['#c8ec3f', '#38bdf8', '#fbbf24', '#f472b6', '#34d399', '#a78bfa', '#fb7185', '#22d3ee'];
        $catBars = [];
        $ci = 0;
        arsort($catStats);
        foreach ($catStats as $name => $v) {
            $catBars[] = [
                'name' => $name,
                'count' => $v['count'],
                'bom' => $v['bom'],
                'revenue' => round($v['revenue'], 2),
                'bar' => $maxRev > 0 ? max(4, (int) round($v['revenue'] / $maxRev * 100)) : 4,
                'hue' => $hues[$ci % 8],
            ];
            $ci++;
            if ($ci >= 8) break;
        }

        $detail = null;
        if ($this->detailId) {
            $detail = collect($all)->firstWhere('id', $this->detailId);
            if ($detail) {
                $area = max(0, (float) str_replace(',', '.', $this->simArea));
                $qty = max(0, (int) $this->simQty);
                $factor = $area * $qty;
                $detail['sim'] = [
                    'area' => $area,
                    'qty' => $qty,
                    'factor' => round($factor, 4),
                    'jasa' => round($detail['price'] * $factor, 2),
                    'bahan' => round($detail['bom_cost'] * $factor, 2),
                    'total' => round($detail['fair_price'] * $factor, 2),
                ];
                $detail['sizes'] = $this->hasSizeCat
                    ? Size::where('is_active', true)
                        ->where(fn($q) => $q->where('category_id', $detail['category_id'])->orWhereNull('category_id'))
                        ->orderBy('name')->limit(10)->pluck('name')->all()
                    : [];
                $detail['recent'] = OrderItem::query()
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->when($this->ordersSoft, fn($q) => $q->whereNull('orders.deleted_at'))
                    ->where('order_items.service_id', $this->detailId)
                    ->orderByDesc('order_items.id')->limit(6)
                    ->get(['orders.id as oid', 'orders.invoice_number', 'orders.status', 'order_items.quantity', 'order_items.area_m2', 'order_items.final_subtotal'])
                    ->map(fn($x) => [
                        'oid' => (int) $x->oid,
                        'invoice' => (string) $x->invoice_number,
                        'status' => (string) $x->status,
                        'qty' => (int) $x->quantity,
                        'area' => round((float) $x->area_m2, 2),
                        'value' => round((float) $x->final_subtotal, 2),
                    ])->all();
            }
        }

        $deleteTarget = $this->deleteId ? collect($all)->firstWhere('id', $this->deleteId) : null;
        $dupTarget = $this->dupId ? collect($all)->firstWhere('id', $this->dupId) : null;

        $bulkPreview = [];
        if ($this->bulkOpen && is_numeric($this->bulkPercent)) {
            $pct = (float) $this->bulkPercent / 100;
            $factor = $this->bulkDirection === 'up' ? (1 + $pct) : (1 - $pct);
            foreach ($all as $r) {
                if ($this->bulkCategory !== 'all' && (string) $r['category_id'] !== (string) $this->bulkCategory) continue;
                $bulkPreview[] = [
                    'name' => $r['name'],
                    'old' => $r['price'],
                    'new' => round($r['price'] * max(0, $factor), 2),
                ];
                if (count($bulkPreview) >= 8) break;
            }
        }

        $bomRoute = null;
        foreach (['admin.boms', 'admin.service-boms', 'admin.bom', 'admin.service.boms'] as $r) {
            if (Route::has($r)) { $bomRoute = $r; break; }
        }

        return view('livewire.admin.services', [
            'rows' => $rows,
            'detail' => $detail,
            'deleteTarget' => $deleteTarget,
            'dupTarget' => $dupTarget,
            'noBom' => array_slice($noBom, 0, 6),
            'noBomTotal' => count($noBom),
            'catBars' => $catBars,
            'bulkPreview' => $bulkPreview,
            'bomRoute' => $bomRoute,
            'categories' => Category::orderBy('name')->get(['id', 'name'])->map(fn($c) => ['id' => $c->id, 'name' => (string) $c->name])->all(),
            'stats' => [
                'total' => $total,
                'shown' => count($rows),
                'active' => $active,
                'with_bom' => $withBom,
                'no_bom' => count($noBom),
                'unused' => $unused,
                'coverage' => $coverage,
                'revenue' => round($revenue, 2),
                'avg_price' => $total > 0 ? round($priceSum / $total, 2) : 0,
                'min_price' => $minPrice ?? 0,
                'max_price' => $maxPrice,
            ],
            'bomReady' => $this->bomReady,
            'hasActive' => $this->has('is_active'),
            'hasDesc' => $this->has('description'),
            'hasFilter' => $this->search !== '' || $this->categoryFilter !== 'all' || $this->statusFilter !== 'all' || $this->bomFilter !== 'all',
        ]);
    }
}