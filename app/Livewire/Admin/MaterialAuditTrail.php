<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Audit Trail Stok')]
class MaterialAuditTrail extends Component
{
    use WithPagination;

    public string $search = '';
    public string $materialFilter = 'all';
    public string $typeFilter = 'all';
    public string $subTypeFilter = 'all';
    public string $userFilter = 'all';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $preset = 'all';
    public string $sort = 'newest';
    public ?int $detailId = null;

    public array $cols = [];

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'produksi', 'production'], true)) {
            abort(403, 'Audit trail stok hanya untuk tim gudang/produksi dan owner.');
        }
        $this->cols = Schema::hasTable('material_movements') ? Schema::getColumnListing('material_movements') : [];
    }

    public function types(): array
    {
        return [
            'in'         => ['Masuk', 'bg-signal-100 text-signal-700', 'bg-signal-400', 'up'],
            'out'        => ['Keluar', 'bg-red-100 text-red-700', 'bg-red-500', 'down'],
            'adjustment' => ['Adjustment', 'bg-sky-100 text-sky-700', 'bg-sky-500', 'equal'],
        ];
    }

    public function setPreset(string $p): void
    {
        $this->preset = $p;
        if ($p === 'all') {
            $this->dateFrom = '';
            $this->dateTo = '';
        } else {
            $days = (int) $p;
            $this->dateFrom = now()->subDays($days - 1)->toDateString();
            $this->dateTo = now()->toDateString();
        }
        $this->resetPage();
    }

    public function setTypeFilter(string $v): void { $this->typeFilter = $v; $this->resetPage(); }
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingMaterialFilter(): void { $this->resetPage(); }
    public function updatingSubTypeFilter(): void { $this->resetPage(); }
    public function updatingUserFilter(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->preset = 'custom'; $this->resetPage(); }
    public function updatingDateTo(): void { $this->preset = 'custom'; $this->resetPage(); }
    public function updatingSort(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->materialFilter = 'all';
        $this->typeFilter = 'all';
        $this->subTypeFilter = 'all';
        $this->userFilter = 'all';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->preset = 'all';
        $this->sort = 'newest';
        $this->resetPage();
    }

    public function openDetail($id): void { $this->detailId = (int) $id; }
    public function closeDetail(): void { $this->detailId = null; }

    private function hasCol(string $c): bool
    {
        return in_array($c, $this->cols, true);
    }

    private function baseQuery()
    {
        $q = DB::table('material_movements');

        if ($this->materialFilter !== 'all') {
            $q->where('material_id', (int) $this->materialFilter);
        }
        if ($this->typeFilter !== 'all') {
            $q->where('movement_type', $this->typeFilter);
        }
        if ($this->subTypeFilter !== 'all' && $this->hasCol('sub_type')) {
            $q->where('sub_type', $this->subTypeFilter);
        }
        if ($this->userFilter !== 'all' && $this->hasCol('user_id')) {
            $q->where('user_id', (int) $this->userFilter);
        }
        if ($this->dateFrom !== '' && $this->hasCol('movement_date')) {
            $q->where('movement_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo !== '' && $this->hasCol('movement_date')) {
            $q->where('movement_date', '<=', $this->dateTo . ' 23:59:59');
        }

        $term = trim($this->search);
        if ($term !== '') {
            $like = '%' . mb_strtolower($term) . '%';
            $matIds = Material::whereRaw('LOWER(name) LIKE ?', [$like])->pluck('id')->all();
            $q->where(function ($w) use ($like, $matIds) {
                $w->whereRaw('LOWER(coalesce(notes,\'\')) LIKE ?', [$like]);
                if ($this->hasCol('reference_type')) $w->orWhereRaw('LOWER(coalesce(reference_type,\'\')) LIKE ?', [$like]);
                if ($this->hasCol('sub_type')) $w->orWhereRaw('LOWER(coalesce(sub_type,\'\')) LIKE ?', [$like]);
                if ($this->hasCol('batch_number')) $w->orWhereRaw('LOWER(coalesce(batch_number,\'\')) LIKE ?', [$like]);
                if (!empty($matIds)) $w->orWhereIn('material_id', $matIds);
            });
        }

        return $q;
    }

    private function decorate($r, array $mats, array $users): array
    {
        $type = strtolower((string) ($r->movement_type ?? ''));
        $qty = (float) ($r->quantity ?? 0);
        $val = (float) ($r->total_cost ?? 0);
        $signed = $type === 'out' ? -abs($qty) : ($type === 'in' ? abs($qty) : $qty);
        $refType = (string) ($r->reference_type ?? '');
        $refId = $r->reference_id ?? null;

        $link = null;
        if ($refId) {
            $t = strtolower($refType);
            if (in_array($t, ['order', 'orders'], true)) $link = 'order';
            elseif (in_array($t, ['purchasereceipt', 'purchase_receipt'], true)) $link = 'receipt';
        }

        $when = $r->movement_date ?? $r->created_at ?? null;
        $carbon = $when ? Carbon::parse($when) : null;

        return [
            'id' => (int) $r->id,
            'day' => $carbon ? $carbon->format('Y-m-d') : null,
            'day_label' => $carbon ? $carbon->translatedFormat('d M Y') : 'Tanpa tanggal',
            'time' => $carbon ? $carbon->format('H:i') : '—',
            'stamp' => $carbon ? $carbon->format('d M Y, H:i') : '—',
            'material' => (string) ($mats[$r->material_id]->name ?? 'Bahan dihapus'),
            'unit' => (string) ($r->unit ?? ($mats[$r->material_id]->unit ?? 'm²')),
            'type' => $type,
            'sub_type' => (string) ($r->sub_type ?? '—'),
            'qty' => $qty,
            'signed' => $signed,
            'unit_cost' => (float) ($r->unit_cost ?? 0),
            'value' => $val,
            'ref_type' => $refType ?: '—',
            'ref_id' => $refId,
            'ref_label' => trim($refType . ($refId ? ' #' . $refId : '')) ?: '—',
            'link' => $link,
            'notes' => (string) ($r->notes ?? ''),
            'user' => (string) ($users[$r->user_id ?? 0] ?? 'Sistem'),
            'batch' => (string) ($r->batch_number ?? ''),
        ];
    }

    private function maps(): array
    {
        $materials = Material::query()->get(['id', 'name', 'unit'])->keyBy('id')->all();
        $users = DB::table('users')->pluck('name', 'id')->all();

        return [$materials, $users];
    }

    public function exportExcel()
    {
        [$mats, $users] = $this->maps();
        $rows = $this->baseQuery()->orderByDesc('id')->limit(5000)->get();
        $types = $this->types();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= '<Styles><Style ss:ID="h"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#0A3F33" ss:Pattern="Solid"/></Style>'
              . '<Style ss:ID="n"><NumberFormat ss:Format="#,##0.00"/></Style></Styles>' . "\n";
        $xml .= '<Worksheet ss:Name="Audit Trail Stok"><Table>' . "\n";

        $headers = ['Waktu', 'Material', 'Tipe', 'Sub Tipe', 'Qty', 'Satuan', 'Harga Satuan', 'Nilai', 'Referensi', 'Batch', 'Pelaku', 'Catatan'];
        $xml .= '<Row>';
        foreach ($headers as $h) $xml .= '<Cell ss:StyleID="h"><Data ss:Type="String">' . htmlspecialchars($h, ENT_XML1) . '</Data></Cell>';
        $xml .= '</Row>' . "\n";

        foreach ($rows as $r) {
            $d = $this->decorate($r, $mats, $users);
            $cells = [
                ['String', $d['stamp']],
                ['String', $d['material']],
                ['String', $types[$d['type']][0] ?? ucfirst($d['type'])],
                ['String', $d['sub_type']],
                ['Number', $d['signed']],
                ['String', $d['unit']],
                ['Number', $d['unit_cost']],
                ['Number', $d['value']],
                ['String', $d['ref_label']],
                ['String', $d['batch']],
                ['String', $d['user']],
                ['String', $d['notes']],
            ];
            $xml .= '<Row>';
            foreach ($cells as [$t, $v]) {
                $style = $t === 'Number' ? ' ss:StyleID="n"' : '';
                $xml .= '<Cell' . $style . '><Data ss:Type="' . $t . '">' . htmlspecialchars((string) $v, ENT_XML1) . '</Data></Cell>';
            }
            $xml .= '</Row>' . "\n";
        }

        $xml .= '</Table></Worksheet></Workbook>';
        $name = 'audit-trail-stok-' . now()->format('Ymd-His') . '.xls';

        return response()->streamDownload(function () use ($xml) { echo $xml; }, $name, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function exportCsv()
    {
        [$mats, $users] = $this->maps();
        $rows = $this->baseQuery()->orderByDesc('id')->limit(5000)->get();
        $types = $this->types();
        $name = 'audit-trail-stok-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows, $mats, $users, $types) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Waktu', 'Material', 'Tipe', 'Sub Tipe', 'Qty', 'Satuan', 'Harga Satuan', 'Nilai', 'Referensi', 'Batch', 'Pelaku', 'Catatan'], ';');
            foreach ($rows as $r) {
                $d = $this->decorate($r, $mats, $users);
                fputcsv($out, [
                    $d['stamp'], $d['material'], $types[$d['type']][0] ?? ucfirst($d['type']), $d['sub_type'],
                    number_format($d['signed'], 4, '.', ''), $d['unit'],
                    number_format($d['unit_cost'], 2, '.', ''), number_format($d['value'], 2, '.', ''),
                    $d['ref_label'], $d['batch'], $d['user'], $d['notes'],
                ], ';');
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        if (empty($this->cols)) {
            return view('livewire.admin.material-audit-trail', [
                'ready' => false, 'rows' => [], 'paginator' => null, 'stats' => [], 'integrity' => [],
                'topMaterials' => [], 'subTypes' => [], 'actors' => [], 'materials' => [],
                'detail' => null, 'types' => $this->types(), 'hasFilter' => false, 'period' => 'Semua waktu',
            ]);
        }

        [$mats, $users] = $this->maps();

        $agg = $this->baseQuery()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN movement_type='in' THEN 1 ELSE 0 END) as c_in")
            ->selectRaw("SUM(CASE WHEN movement_type='out' THEN 1 ELSE 0 END) as c_out")
            ->selectRaw("SUM(CASE WHEN movement_type='adjustment' THEN 1 ELSE 0 END) as c_adj")
            ->selectRaw("SUM(CASE WHEN movement_type='in' THEN quantity ELSE 0 END) as q_in")
            ->selectRaw("SUM(CASE WHEN movement_type='out' THEN quantity ELSE 0 END) as q_out")
            ->selectRaw("SUM(CASE WHEN movement_type='adjustment' THEN quantity ELSE 0 END) as q_adj")
            ->selectRaw("SUM(CASE WHEN movement_type='in' THEN total_cost ELSE 0 END) as v_in")
            ->selectRaw("SUM(CASE WHEN movement_type='out' THEN total_cost ELSE 0 END) as v_out")
            ->selectRaw("SUM(CASE WHEN movement_type='adjustment' THEN total_cost ELSE 0 END) as v_adj")
            ->selectRaw("SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as no_user")
            ->selectRaw("SUM(CASE WHEN reference_type IS NULL OR reference_type='' THEN 1 ELSE 0 END) as no_ref")
            ->selectRaw("SUM(CASE WHEN COALESCE(total_cost,0)=0 AND COALESCE(quantity,0)>0 THEN 1 ELSE 0 END) as no_value")
            ->first();

        $stats = [
            'total' => (int) ($agg->total ?? 0),
            'c_in' => (int) ($agg->c_in ?? 0), 'c_out' => (int) ($agg->c_out ?? 0), 'c_adj' => (int) ($agg->c_adj ?? 0),
            'q_in' => (float) ($agg->q_in ?? 0), 'q_out' => (float) ($agg->q_out ?? 0), 'q_adj' => (float) ($agg->q_adj ?? 0),
            'v_in' => (float) ($agg->v_in ?? 0), 'v_out' => (float) ($agg->v_out ?? 0), 'v_adj' => (float) ($agg->v_adj ?? 0),
        ];
        $stats['net_qty'] = round($stats['q_in'] - $stats['q_out'] + $stats['q_adj'], 4);
        $stats['net_value'] = round($stats['v_in'] - $stats['v_out'] + $stats['v_adj'], 2);

        $integrity = [
            'no_user' => (int) ($agg->no_user ?? 0),
            'no_ref' => (int) ($agg->no_ref ?? 0),
            'no_value' => (int) ($agg->no_value ?? 0),
        ];
        $integrity['total'] = $integrity['no_user'] + $integrity['no_ref'] + $integrity['no_value'];

        $topRaw = $this->baseQuery()
            ->selectRaw('material_id, COUNT(*) as c, SUM(quantity) as q, SUM(total_cost) as v')
            ->groupBy('material_id')->orderByDesc('c')->limit(6)->get();
        $topMax = (int) ($topRaw->max('c') ?? 0);
        $topMaterials = $topRaw->map(fn($t) => [
            'name' => (string) ($mats[$t->material_id]->name ?? 'Bahan dihapus'),
            'count' => (int) $t->c,
            'qty' => (float) $t->q,
            'value' => (float) $t->v,
            'bar' => $topMax > 0 ? max(4, (int) round($t->c / $topMax * 100)) : 4,
        ])->all();

        $subTypes = $this->hasCol('sub_type')
            ? DB::table('material_movements')->whereNotNull('sub_type')->distinct()->orderBy('sub_type')->pluck('sub_type')->all()
            : [];

        $actorIds = $this->hasCol('user_id')
            ? DB::table('material_movements')->whereNotNull('user_id')->distinct()->pluck('user_id')->all()
            : [];
        $actors = [];
        foreach ($actorIds as $uid) if (isset($users[$uid])) $actors[] = ['id' => (int) $uid, 'name' => (string) $users[$uid]];
        usort($actors, fn($a, $b) => strcmp($a['name'], $b['name']));

        $paginator = $this->baseQuery()
            ->orderBy('movement_date', $this->sort === 'oldest' ? 'asc' : 'desc')
            ->orderBy('id', $this->sort === 'oldest' ? 'asc' : 'desc')
            ->paginate(20);

        $rows = [];
        foreach ($paginator as $r) $rows[] = $this->decorate($r, $mats, $users);

        $detail = null;
        if ($this->detailId) {
            $r = DB::table('material_movements')->find($this->detailId);
            if ($r) {
                $detail = $this->decorate($r, $mats, $users);
                $detail['raw'] = [
                    'movement_id' => (int) $r->id,
                    'material_id' => (int) $r->material_id,
                    'movement_date' => (string) ($r->movement_date ?? '—'),
                    'created_at' => isset($r->created_at) ? Carbon::parse($r->created_at)->format('d M Y, H:i:s') : '—',
                ];
            }
        }

        $period = $this->preset === 'all'
            ? 'Semua waktu'
            : ($this->dateFrom && $this->dateTo
                ? Carbon::parse($this->dateFrom)->format('d M Y') . ' – ' . Carbon::parse($this->dateTo)->format('d M Y')
                : 'Rentang khusus');

        return view('livewire.admin.material-audit-trail', [
            'ready' => true,
            'rows' => $rows,
            'paginator' => $paginator,
            'stats' => $stats,
            'integrity' => $integrity,
            'topMaterials' => $topMaterials,
            'subTypes' => $subTypes,
            'actors' => $actors,
            'materials' => Material::orderBy('name')->get(['id', 'name'])->map(fn($m) => ['id' => $m->id, 'name' => (string) $m->name])->all(),
            'detail' => $detail,
            'types' => $this->types(),
            'period' => $period,
            'hasFilter' => $this->search !== '' || $this->materialFilter !== 'all' || $this->typeFilter !== 'all'
                || $this->subTypeFilter !== 'all' || $this->userFilter !== 'all' || $this->dateFrom !== '' || $this->dateTo !== '',
        ]);
    }
}