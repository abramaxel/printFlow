<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Daftar Supplier')]
class Suppliers extends Component
{
    public string $search = '';
    public string $statusFilter = 'all';
    public string $sort = 'spend';

    public bool $createOpen = false;
    public string $cName = '';
    public string $cContact = '';
    public string $cPhone = '';
    public string $cEmail = '';
    public string $cAddress = '';
    public string $cNotes = '';
    public bool $cActive = true;

    public ?int $editId = null;
    public string $eName = '';
    public string $eContact = '';
    public string $ePhone = '';
    public string $eEmail = '';
    public string $eAddress = '';
    public string $eNotes = '';
    public bool $eActive = true;

    public ?int $deleteId = null;
    public ?int $detailId = null;

    public array $cols = [];
    public bool $hasReceipts = false;
    public bool $hasItems = false;
    public bool $hasBatches = false;

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'admin', 'produksi', 'production'], true)) {
            abort(403, 'Master supplier hanya untuk owner, admin, dan tim gudang.');
        }

        $this->cols = Schema::hasTable('suppliers') ? Schema::getColumnListing('suppliers') : [];
        $this->hasReceipts = Schema::hasTable('purchase_receipts');
        $this->hasItems = Schema::hasTable('purchase_receipt_items');
        $this->hasBatches = Schema::hasTable('material_batches');
    }

    private function has(string $c): bool
    {
        return in_array($c, $this->cols, true);
    }

    private function waOf(string $raw): string
    {
        $d = preg_replace('/\D+/', '', $raw) ?? '';
        if ($d === '') return '';
        if (str_starts_with($d, '0')) $d = '62' . substr($d, 1);
        elseif (str_starts_with($d, '8')) $d = '62' . $d;
        return $d;
    }

    /* ================= TAMBAH ================= */

    public function openCreate(): void
    {
        $this->createOpen = true;
        $this->cName = ''; $this->cContact = ''; $this->cPhone = '';
        $this->cEmail = ''; $this->cAddress = ''; $this->cNotes = '';
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
        $data = $this->validate([
            'cName' => 'required|string|min:2|max:150|unique:suppliers,name',
            'cContact' => 'nullable|string|max:120',
            'cPhone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s().]+$/'],
            'cEmail' => 'nullable|email:rfc|max:150',
            'cAddress' => 'nullable|string|max:1000',
            'cNotes' => 'nullable|string|max:1000',
        ], [
            'cName.required' => 'Nama supplier wajib diisi.',
            'cName.min' => 'Nama supplier minimal 2 karakter.',
            'cName.unique' => 'Nama supplier ini sudah terdaftar.',
            'cPhone.regex' => 'Nomor hanya boleh angka, spasi, +, -, dan tanda kurung.',
            'cEmail.email' => 'Format email tidak valid.',
        ]);

        $fill = ['name' => trim($data['cName'])];
        if ($this->has('contact_person')) $fill['contact_person'] = trim((string) $data['cContact']);
        if ($this->has('phone')) $fill['phone'] = trim((string) $data['cPhone']);
        if ($this->has('email')) $fill['email'] = trim((string) $data['cEmail']);
        if ($this->has('address')) $fill['address'] = trim((string) $data['cAddress']);
        if ($this->has('notes')) $fill['notes'] = trim((string) $data['cNotes']);
        if ($this->has('is_active')) $fill['is_active'] = $this->cActive;

        $s = new Supplier();
        $s->forceFill($fill)->save();

        $this->closeCreate();
        session()->flash('sup_success', 'Supplier "' . $s->name . '" ditambahkan dan siap dipakai pada penerimaan barang.');
    }

    /* ================= EDIT ================= */

    public function openEdit($id): void
    {
        $s = Supplier::find($id);
        if (!$s) return;

        $this->editId = (int) $s->id;
        $this->eName = (string) $s->name;
        $this->eContact = (string) ($s->contact_person ?? '');
        $this->ePhone = (string) ($s->phone ?? '');
        $this->eEmail = (string) ($s->email ?? '');
        $this->eAddress = (string) ($s->address ?? '');
        $this->eNotes = (string) ($s->notes ?? '');
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
        $s = Supplier::find($this->editId);
        if (!$s) { $this->closeEdit(); return; }

        $data = $this->validate([
            'eName' => ['required', 'string', 'min:2', 'max:150', Rule::unique('suppliers', 'name')->ignore($s->id)],
            'eContact' => 'nullable|string|max:120',
            'ePhone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s().]+$/'],
            'eEmail' => 'nullable|email:rfc|max:150',
            'eAddress' => 'nullable|string|max:1000',
            'eNotes' => 'nullable|string|max:1000',
        ], [
            'eName.required' => 'Nama supplier wajib diisi.',
            'eName.unique' => 'Nama supplier sudah dipakai.',
            'ePhone.regex' => 'Nomor hanya boleh angka, spasi, +, -, dan tanda kurung.',
            'eEmail.email' => 'Format email tidak valid.',
        ]);

        $fill = ['name' => trim($data['eName'])];
        if ($this->has('contact_person')) $fill['contact_person'] = trim((string) $data['eContact']);
        if ($this->has('phone')) $fill['phone'] = trim((string) $data['ePhone']);
        if ($this->has('email')) $fill['email'] = trim((string) $data['eEmail']);
        if ($this->has('address')) $fill['address'] = trim((string) $data['eAddress']);
        if ($this->has('notes')) $fill['notes'] = trim((string) $data['eNotes']);
        if ($this->has('is_active')) $fill['is_active'] = $this->eActive;

        $s->forceFill($fill)->save();

        $this->closeEdit();
        session()->flash('sup_success', 'Data supplier "' . $s->name . '" diperbarui. Dokumen BPB lama tetap memakai nama tercatat.');
    }

    public function toggleActive($id): void
    {
        if (!$this->has('is_active')) return;
        $s = Supplier::find($id);
        if (!$s) return;

        $s->forceFill(['is_active' => !(bool) $s->is_active])->save();
        session()->flash('sup_success', 'Supplier "' . $s->name . '" sekarang ' . ($s->is_active ? 'aktif' : 'nonaktif') . '. Riwayat pembelian tetap utuh.');
    }

    /* ================= HAPUS ================= */

    public function openDelete($id): void
    {
        $s = Supplier::find($id);
        if (!$s) return;
        $this->deleteId = (int) $s->id;
        $this->detailId = null;
    }

    public function closeDelete(): void { $this->deleteId = null; }

    public function confirmDelete(): void
    {
        $s = Supplier::find($this->deleteId);
        if (!$s) { $this->closeDelete(); return; }

        $refs = $this->hasReceipts ? DB::table('purchase_receipts')->where('supplier_id', $s->id)->count() : 0;
        if ($refs > 0) {
            session()->flash('sup_error', 'Supplier ini punya ' . $refs . ' dokumen penerimaan dan tidak boleh dihapus. Gunakan nonaktifkan.');
            $this->closeDelete();
            return;
        }

        $name = $s->name;
        $s->delete();
        $this->closeDelete();
        session()->flash('sup_success', 'Supplier "' . $name . '" dihapus karena belum memiliki transaksi.');
    }

    public function deactivateInstead(): void
    {
        if (!$this->has('is_active')) return;
        $s = Supplier::find($this->deleteId);
        if (!$s) { $this->closeDelete(); return; }

        $s->forceFill(['is_active' => false])->save();
        $name = $s->name;
        $this->closeDelete();
        session()->flash('sup_success', 'Supplier "' . $name . '" dinonaktifkan. Tidak muncul di penerimaan baru, riwayat aman.');
    }

    /* ================= DETAIL & DATA ================= */

    public function openDetail($id): void { $this->detailId = (int) $id; }
    public function closeDetail(): void { $this->detailId = null; }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->sort = 'spend';
    }

    private function buildRows(): array
    {
        $suppliers = Supplier::orderBy('name')->get();
        $monthStart = now()->startOfMonth()->toDateString();
        $yearStart = now()->startOfYear()->toDateString();

        $agg = collect();
        if ($this->hasReceipts) {
            $agg = DB::table('purchase_receipts')
                ->selectRaw('supplier_id as sid')
                ->selectRaw('COUNT(*) as c')
                ->selectRaw('COALESCE(SUM(total_cost),0) as total')
                ->selectRaw('MIN(receipt_date) as first_at')
                ->selectRaw('MAX(receipt_date) as last_at')
                ->selectRaw('COALESCE(SUM(CASE WHEN receipt_date >= ? THEN total_cost ELSE 0 END),0) as month_total', [$monthStart])
                ->selectRaw('COALESCE(SUM(CASE WHEN receipt_date >= ? THEN total_cost ELSE 0 END),0) as year_total', [$yearStart])
                ->groupBy('supplier_id')->get()->keyBy('sid');
        }

        $itemMap = [];
        if ($this->hasReceipts && $this->hasItems) {
            $rows = DB::table('purchase_receipt_items')
                ->join('purchase_receipts', 'purchase_receipts.id', '=', 'purchase_receipt_items.purchase_receipt_id')
                ->join('materials', 'materials.id', '=', 'purchase_receipt_items.material_id')
                ->selectRaw('purchase_receipts.supplier_id as sid')
                ->selectRaw('materials.name as mname')
                ->selectRaw('purchase_receipt_items.unit as munit')
                ->selectRaw('COUNT(*) as lines')
                ->selectRaw('COALESCE(SUM(purchase_receipt_items.quantity),0) as qty')
                ->selectRaw('COALESCE(SUM(purchase_receipt_items.total_cost),0) as val')
                ->groupBy('purchase_receipts.supplier_id', 'materials.name', 'purchase_receipt_items.unit')
                ->get();

            foreach ($rows as $r) {
                $sid = (int) $r->sid;
                if (!isset($itemMap[$sid])) $itemMap[$sid] = [];
                $itemMap[$sid][] = [
                    'name' => (string) $r->mname,
                    'unit' => (string) ($r->munit ?? 'm²'),
                    'lines' => (int) $r->lines,
                    'qty' => round((float) $r->qty, 2),
                    'value' => round((float) $r->val, 2),
                ];
            }
            foreach ($itemMap as $sid => $list) {
                usort($list, fn($a, $b) => $b['value'] <=> $a['value']);
                $itemMap[$sid] = $list;
            }
        }

        $grand = 0.0;
        foreach ($agg as $a) $grand += (float) $a->total;

        $rows = [];
        foreach ($suppliers as $s) {
            $a = $agg[$s->id] ?? null;
            $count = (int) ($a->c ?? 0);
            $total = (float) ($a->total ?? 0);
            $last = !empty($a->last_at) ? Carbon::parse($a->last_at) : null;
            $first = !empty($a->first_at) ? Carbon::parse($a->first_at) : null;
            $recency = $last ? (int) $last->startOfDay()->diffInDays(now()->startOfDay()) : null;
            $materials = $itemMap[$s->id] ?? [];

            $rows[] = [
                'id' => (int) $s->id,
                'name' => (string) $s->name,
                'contact' => (string) ($s->contact_person ?? ''),
                'phone' => (string) ($s->phone ?? ''),
                'wa' => $this->waOf((string) ($s->phone ?? '')),
                'email' => (string) ($s->email ?? ''),
                'address' => (string) ($s->address ?? ''),
                'notes' => (string) ($s->notes ?? ''),
                'active' => (bool) ($s->is_active ?? true),
                'receipts' => $count,
                'total' => round($total, 2),
                'month_total' => round((float) ($a->month_total ?? 0), 2),
                'year_total' => round((float) ($a->year_total ?? 0), 2),
                'avg' => $count > 0 ? round($total / $count, 2) : 0.0,
                'share' => $grand > 0 ? round($total / $grand * 100, 1) : 0.0,
                'first' => $first?->format('d M Y'),
                'last' => $last?->format('d M Y'),
                'recency' => $recency,
                'material_count' => count($materials),
                'materials' => array_slice($materials, 0, 6),
                'created' => $s->created_at?->format('d M Y'),
                'no_contact' => trim((string) ($s->phone ?? '')) === '' && trim((string) ($s->email ?? '')) === '',
            ];
        }

        return $rows;
    }

    private function filterRows(array $rows): array
    {
        $term = mb_strtolower(trim($this->search));

        $out = array_values(array_filter($rows, function ($r) use ($term) {
            if ($this->statusFilter === 'active' && !$r['active']) return false;
            if ($this->statusFilter === 'inactive' && $r['active']) return false;
            if ($this->statusFilter === 'used' && $r['receipts'] === 0) return false;
            if ($this->statusFilter === 'unused' && $r['receipts'] > 0) return false;
            if ($this->statusFilter === 'nocontact' && !$r['no_contact']) return false;
            if ($this->statusFilter === 'dormant' && !($r['recency'] !== null && $r['recency'] > 90)) return false;
            if ($term === '') return true;
            return str_contains(mb_strtolower($r['name']), $term)
                || str_contains(mb_strtolower($r['contact']), $term)
                || str_contains(mb_strtolower($r['phone']), $term)
                || str_contains(mb_strtolower($r['email']), $term);
        }));

        usort($out, function ($a, $b) {
            if ($this->sort === 'name') return strcmp($a['name'], $b['name']);
            if ($this->sort === 'receipts') return $b['receipts'] <=> $a['receipts'];
            if ($this->sort === 'recent') return [$a['recency'] ?? 99999, $a['name']] <=> [$b['recency'] ?? 99999, $b['name']];
            if ($this->sort === 'newest') return strcmp((string) $b['created'], (string) $a['created']);
            if ($this->sort === 'materials') return $b['material_count'] <=> $a['material_count'];
            return $b['total'] <=> $a['total'];
        });

        return $out;
    }

    /* ================= EKSPOR ================= */

    private function exportHead(): array
    {
        return ['Nama Supplier', 'Narahubung', 'Telepon', 'Email', 'Alamat', 'Jumlah BPB', 'Total Pembelian', 'Rata-rata per BPB', 'Pembelian Bulan Ini', 'Pembelian Tahun Ini', 'Porsi %', 'Jenis Bahan', 'BPB Pertama', 'BPB Terakhir', 'Jeda (hari)', 'Status', 'Catatan'];
    }

    private function exportLine(array $r): array
    {
        return [
            $r['name'], $r['contact'], $r['phone'], $r['email'], $r['address'],
            $r['receipts'], $r['total'], $r['avg'], $r['month_total'], $r['year_total'],
            $r['share'], $r['material_count'], $r['first'] ?? '-', $r['last'] ?? '-',
            $r['recency'] ?? '-', $r['active'] ? 'Aktif' : 'Nonaktif', $r['notes'],
        ];
    }

    public function exportExcel()
    {
        $rows = $this->filterRows($this->buildRows());

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= '<Styles><Style ss:ID="h"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#0A3F33" ss:Pattern="Solid"/></Style>'
              . '<Style ss:ID="n"><NumberFormat ss:Format="#,##0.00"/></Style></Styles>' . "\n";
        $xml .= '<Worksheet ss:Name="Daftar Supplier"><Table>' . "\n";
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

        return response()->streamDownload(function () use ($xml) { echo $xml; }, 'daftar-supplier-' . now()->format('Ymd-His') . '.xls', [
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
        }, 'daftar-supplier-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $all = $this->buildRows();
        $rows = $this->filterRows($all);

        $active = 0; $used = 0; $unused = 0; $noContact = 0; $dormant = 0;
        $grand = 0.0; $month = 0.0; $year = 0.0; $receipts = 0;
        foreach ($all as $r) {
            if ($r['active']) $active++;
            if ($r['receipts'] > 0) $used++; else $unused++;
            if ($r['no_contact']) $noContact++;
            if ($r['recency'] !== null && $r['recency'] > 90) $dormant++;
            $grand += $r['total'];
            $month += $r['month_total'];
            $year += $r['year_total'];
            $receipts += $r['receipts'];
        }

        $ranked = $all;
        usort($ranked, fn($a, $b) => $b['total'] <=> $a['total']);
        $ranked = array_values(array_filter($ranked, fn($r) => $r['total'] > 0));
        $maxSpend = $ranked ? $ranked[0]['total'] : 0;
        foreach ($ranked as $i => $r) {
            $ranked[$i]['bar'] = $maxSpend > 0 ? max(4, (int) round($r['total'] / $maxSpend * 100)) : 4;
        }
        $top = array_slice($ranked, 0, 5);

        $concentration = null;
        if ($grand > 0 && !empty($ranked) && $ranked[0]['share'] >= 50) {
            $concentration = $ranked[0];
        }

        $detail = null;
        if ($this->detailId) {
            $detail = collect($all)->firstWhere('id', $this->detailId);
            if ($detail) {
                $detail['receipts_list'] = [];
                if ($this->hasReceipts) {
                    $q = DB::table('purchase_receipts')->where('supplier_id', $this->detailId)
                        ->orderByDesc('receipt_date')->orderByDesc('id')->limit(8);
                    $detail['receipts_list'] = $q->get()->map(function ($x) {
                        $items = $this->hasItems
                            ? DB::table('purchase_receipt_items')->where('purchase_receipt_id', $x->id)->count()
                            : 0;
                        return [
                            'id' => (int) $x->id,
                            'bpb' => (string) $x->bpb_number,
                            'date' => $x->receipt_date ? Carbon::parse($x->receipt_date)->format('d M Y') : '—',
                            'doc' => (string) ($x->supplier_doc_number ?? ''),
                            'total' => (float) $x->total_cost,
                            'items' => $items,
                        ];
                    })->all();
                }
            }
        }

        $deleteTarget = $this->deleteId ? collect($all)->firstWhere('id', $this->deleteId) : null;

        return view('livewire.admin.suppliers', [
            'rows' => $rows,
            'detail' => $detail,
            'deleteTarget' => $deleteTarget,
            'top' => $top,
            'concentration' => $concentration,
            'stats' => [
                'total' => count($all),
                'shown' => count($rows),
                'active' => $active,
                'used' => $used,
                'unused' => $unused,
                'no_contact' => $noContact,
                'dormant' => $dormant,
                'grand' => round($grand, 2),
                'month' => round($month, 2),
                'year' => round($year, 2),
                'receipts' => $receipts,
                'avg' => $receipts > 0 ? round($grand / $receipts, 2) : 0,
            ],
            'hasActive' => $this->has('is_active'),
            'hasContact' => $this->has('contact_person'),
            'hasEmail' => $this->has('email'),
            'hasAddress' => $this->has('address'),
            'hasNotes' => $this->has('notes'),
            'hasFilter' => $this->search !== '' || $this->statusFilter !== 'all',
        ]);
    }
}