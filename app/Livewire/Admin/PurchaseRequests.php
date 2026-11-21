<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Layout('components.layouts.admin')]
#[Title('Purchase Request — Persetujuan Owner')]
class PurchaseRequests extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public string $urgencyFilter = 'all';
    public string $sort = 'priority';

    public array $selected = [];
    public ?int $detailId = null;
    public ?int $rejectId = null;
    public string $rejectReason = '';

    public bool $hasReceipts = false;

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'admin', 'produksi', 'production'], true)) {
            abort(403, 'Halaman purchase request hanya untuk staf internal.');
        }

        $this->hasReceipts = Schema::hasTable('purchase_receipts');

        $s = (string) request('status', '');
        if ($s !== '') $this->statusFilter = $s;

        if ($id = (int) request('pr', 0)) $this->detailId = $id;
    }

    private function canApprove(): bool
    {
        return in_array((string) (auth()->user()->role ?? ''), ['owner', 'super_admin'], true);
    }

    private function appendNote(PurchaseRequest $pr, string $line): string
    {
        $cur = trim((string) $pr->notes);
        return $cur === '' ? $line : $cur . "\n" . $line;
    }

    private function estimateFor(PurchaseRequest $pr, ?Material $m): array
    {
        $stored = (float) $pr->estimated_cost;
        $unit   = (float) ($m->price_per_m2 ?? 0);
        $calc   = round((float) $pr->requested_quantity * $unit, 2);

        return [
            'value' => $stored > 0 ? $stored : $calc,
            'auto'  => $stored <= 0 && $calc > 0,
            'unit'  => $unit,
        ];
    }

    private function decorate(PurchaseRequest $pr, ?Material $m): array
    {
        $net   = $m ? round((float) $m->stock - (float) $m->stock_reserved, 4) : 0.0;
        $mName = $m?->name ?? 'Bahan dihapus';
        $mUnit = $m?->unit ?? 'm²';
        $min   = (float) ($m->minimum_stock ?? 0);
        $qty   = (float) $pr->requested_quantity;
        $after = round($net + $qty, 4);
        $est   = $this->estimateFor($pr, $m);
        $lead  = max(0, (int) ($m->lead_time_days ?? 3));
        $need  = $pr->needed_by_date;

        $daysToNeed = $need ? (int) now()->startOfDay()->diffInDays($need->copy()->startOfDay(), false) : null;
        $earliest   = now()->addDays($lead)->startOfDay();
        $risk       = $need ? $need->copy()->startOfDay()->lt($earliest) : false;

        return [
            'id'             => $pr->id,
            'number'         => (string) $pr->pr_number,
            'material'       => $mName,
            'material_id'    => $pr->material_id,
            'unit'           => (string) ($pr->unit ?: $mUnit),
            'qty'            => $qty,
            'net'            => $net,
            'min'            => $min,
            'after'          => $after,
            'coverage'       => $min > 0 ? max(0, min(100, (int) round(max(0, $net) / $min * 100))) : ($net > 0 ? 100 : 0),
            'after_coverage' => $min > 0 ? max(0, min(100, (int) round(max(0, $after) / $min * 100))) : 100,
            'estimate'       => $est['value'],
            'estimate_auto'  => $est['auto'],
            'unit_price'     => $est['unit'],
            'urgency'        => (string) $pr->urgency,
            'status'         => (string) $pr->status,
            'reason'         => (string) ($pr->reason ?? ''),
            'notes'          => (string) ($pr->notes ?? ''),
            'requester'      => $pr->requester?->name ?? 'Sistem / MRP',
            'approver'       => $pr->approver?->name,
            'created'        => $pr->created_at?->format('d M Y, H:i'),
            'created_ts'     => $pr->created_at?->timestamp ?? 0,
            'age_days'       => $pr->created_at ? (int) $pr->created_at->diffInDays(now()) : 0,
            'approved'       => $pr->approved_at?->format('d M Y, H:i'),
            'needed'         => $need?->format('d M Y'),
            'days_to_need'   => $daysToNeed,
            'lead'           => $lead,
            'risk'           => $risk,
            'mine'           => (int) $pr->requested_by === (int) auth()->id(),
        ];
    }

    /* ================= AKSI ================= */

    public function openDetail($id): void { $this->detailId = (int) $id; }
    public function closeDetail(): void { $this->detailId = null; }

    public function openReject($id): void
    {
        if (!$this->canApprove()) return;
        $pr = PurchaseRequest::where('status', 'pending')->find($id);
        if (!$pr) return;
        $this->rejectId = (int) $id;
        $this->rejectReason = '';
        $this->resetErrorBag('rejectReason');
    }

    public function closeReject(): void
    {
        $this->rejectId = null;
        $this->rejectReason = '';
        $this->resetErrorBag('rejectReason');
    }

    public function approve($id): void
    {
        if (!$this->canApprove()) {
            session()->flash('pr_error', 'Hanya owner yang dapat menyetujui purchase request.');
            return;
        }

        $pr = PurchaseRequest::where('status', 'pending')->find($id);
        if (!$pr) {
            session()->flash('pr_error', 'PR sudah diproses sebelumnya.');
            return;
        }

        DB::transaction(function () use ($pr) {
            $line = '[DISETUJUI ' . now()->format('d M Y H:i') . '] oleh ' . (auth()->user()->name ?? 'Owner');
            $pr->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'notes'       => $this->appendNote($pr, $line),
            ]);
        });

        $this->selected = array_values(array_filter($this->selected, fn($x) => (int) $x !== (int) $id));
        session()->flash('pr_success', 'PR ' . $pr->pr_number . ' disetujui. Lanjutkan ke Penerimaan Barang saat kiriman tiba.');
    }

    public function submitReject(): void
    {
        if (!$this->canApprove()) return;

        $this->validate([
            'rejectReason' => 'required|string|min:5|max:500',
        ], [
            'rejectReason.required' => 'Alasan penolakan wajib diisi agar tim produksi tahu tindak lanjutnya.',
            'rejectReason.min'      => 'Alasan minimal 5 karakter.',
            'rejectReason.max'      => 'Alasan maksimal 500 karakter.',
        ]);

        $pr = PurchaseRequest::where('status', 'pending')->find($this->rejectId);
        if (!$pr) { $this->closeReject(); return; }

        DB::transaction(function () use ($pr) {
            $line = '[DITOLAK ' . now()->format('d M Y H:i') . '] oleh ' . (auth()->user()->name ?? 'Owner') . ' — ' . trim($this->rejectReason);
            $pr->update([
                'status'      => 'cancelled',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'notes'       => $this->appendNote($pr, $line),
            ]);
        });

        $this->selected = array_values(array_filter($this->selected, fn($x) => (int) $x !== (int) $pr->id));
        $number = $pr->pr_number;
        $this->closeReject();
        session()->flash('pr_success', 'PR ' . $number . ' ditolak. Alasan tercatat untuk tim produksi.');
    }

    public function toggleSelect($id): void
    {
        $id = (int) $id;
        if (in_array($id, $this->selected, true)) {
            $this->selected = array_values(array_filter($this->selected, fn($x) => (int) $x !== $id));
        } else {
            $this->selected[] = $id;
        }
    }

    public function selectAllPending(): void
    {
        $this->selected = PurchaseRequest::where('status', 'pending')->pluck('id')->map(fn($x) => (int) $x)->all();
    }

    public function clearSelection(): void { $this->selected = []; }

    public function bulkApprove(): void
    {
        if (!$this->canApprove()) return;
        if (empty($this->selected)) return;

        $done = 0;
        DB::transaction(function () use (&$done) {
            $prs = PurchaseRequest::whereIn('id', $this->selected)->where('status', 'pending')->get();
            foreach ($prs as $pr) {
                $line = '[DISETUJUI MASSAL ' . now()->format('d M Y H:i') . '] oleh ' . (auth()->user()->name ?? 'Owner');
                $pr->update([
                    'status'      => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'notes'       => $this->appendNote($pr, $line),
                ]);
                $done++;
            }
        });

        $this->selected = [];
        session()->flash($done > 0 ? 'pr_success' : 'pr_error', $done > 0 ? $done . ' PR disetujui sekaligus.' : 'Tidak ada PR pending yang bisa disetujui.');
    }

    public function setStatusFilter(string $v): void { $this->statusFilter = $v; $this->resetPage(); }
    public function setUrgencyFilter(string $v): void { $this->urgencyFilter = $v; $this->resetPage(); }
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingSort(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->urgencyFilter = 'all';
        $this->sort = 'priority';
        $this->resetPage();
    }

    public function render()
    {
        $agg = PurchaseRequest::selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved")
            ->selectRaw("SUM(CASE WHEN status='ordered' THEN 1 ELSE 0 END) as ordered")
            ->selectRaw("SUM(CASE WHEN status='received' THEN 1 ELSE 0 END) as received")
            ->selectRaw("SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as cancelled")
            ->first();

        $pendingRaw = PurchaseRequest::with(['material', 'requester', 'approver'])
            ->where('status', 'pending')
            ->orderByRaw("CASE WHEN urgency='critical' THEN 0 WHEN urgency='warning' THEN 1 ELSE 2 END")
            ->orderByRaw('needed_by_date IS NULL, needed_by_date ASC')
            ->latest()
            ->get();

        $pending = [];
        $pendingValue = 0.0;
        $criticalPending = 0;
        $overdue = 0;
        $oldest = 0;
        foreach ($pendingRaw as $pr) {
            $row = $this->decorate($pr, $pr->material);
            $pending[] = $row;
            $pendingValue += $row['estimate'];
            if ($row['urgency'] === 'critical') $criticalPending++;
            if (!is_null($row['days_to_need']) && $row['days_to_need'] < 0) $overdue++;
            $oldest = max($oldest, $row['age_days']);
        }

        $stats = [
            'total'     => (int) ($agg->total ?? 0),
            'pending'   => (int) ($agg->pending ?? 0),
            'approved'  => (int) ($agg->approved ?? 0) + (int) ($agg->ordered ?? 0),
            'received'  => (int) ($agg->received ?? 0),
            'cancelled' => (int) ($agg->cancelled ?? 0),
            'critical'  => $criticalPending,
            'value'     => round($pendingValue, 2),
            'overdue'   => $overdue,
            'oldest'    => $oldest,
        ];

        $term = trim($this->search);
        $ledgerQuery = PurchaseRequest::with(['material', 'requester', 'approver'])
            ->when($this->statusFilter !== 'all', function ($q) {
                if ($this->statusFilter === 'approved') $q->whereIn('status', ['approved', 'ordered']);
                else $q->where('status', $this->statusFilter);
            })
            ->when($this->urgencyFilter !== 'all', fn($q) => $q->where('urgency', $this->urgencyFilter))
            ->when($term !== '', function ($q) use ($term) {
                $like = '%' . mb_strtolower($term) . '%';
                $q->where(function ($w) use ($like) {
                    $w->whereRaw('LOWER(pr_number) LIKE ?', [$like])
                      ->orWhereHas('material', fn($m) => $m->whereRaw('LOWER(name) LIKE ?', [$like]))
                      ->orWhereHas('requester', fn($u) => $u->whereRaw('LOWER(name) LIKE ?', [$like]));
                });
            });

        match ($this->sort) {
            'newest' => $ledgerQuery->latest(),
            'oldest' => $ledgerQuery->oldest(),
            'value'  => $ledgerQuery->orderByDesc('estimated_cost'),
            'needed' => $ledgerQuery->orderByRaw('needed_by_date IS NULL, needed_by_date ASC'),
            default  => $ledgerQuery
                ->orderByRaw("CASE WHEN status='pending' THEN 0 WHEN status IN ('approved','ordered') THEN 1 ELSE 2 END")
                ->orderByRaw("CASE WHEN urgency='critical' THEN 0 WHEN urgency='warning' THEN 1 ELSE 2 END")
                ->orderByRaw('needed_by_date IS NULL, needed_by_date ASC')
                ->latest(),
        };

        $paginator = $ledgerQuery->paginate(10);
        $ledger = [];
        foreach ($paginator as $pr) $ledger[] = $this->decorate($pr, $pr->material);

        $detail = null;
        if ($this->detailId) {
            $pr = PurchaseRequest::with(['material', 'requester', 'approver'])->find($this->detailId);
            if ($pr) {
                $detail = $this->decorate($pr, $pr->material);
                $detail['receipt'] = null;
                if ($this->hasReceipts) {
                    $r = DB::table('purchase_receipts')->where('purchase_request_id', $pr->id)->orderByDesc('id')->first();
                    if ($r) {
                        $detail['receipt'] = [
                            'bpb'  => (string) ($r->bpb_number ?? '-'),
                            'date' => $r->receipt_date ? \Illuminate\Support\Carbon::parse($r->receipt_date)->format('d M Y') : null,
                            'cost' => (float) ($r->total_cost ?? 0),
                        ];
                    }
                }
            }
        }

        $rejectTarget = null;
        if ($this->rejectId) {
            $pr = PurchaseRequest::with('material')->find($this->rejectId);
            if ($pr) $rejectTarget = ['number' => $pr->pr_number, 'material' => $pr->material->name ?? '-', 'qty' => (float) $pr->requested_quantity, 'unit' => $pr->unit];
        }

        return view('livewire.admin.purchase-requests', [
            'pending'      => $pending,
            'ledger'       => $ledger,
            'paginator'    => $paginator,
            'stats'        => $stats,
            'detail'       => $detail,
            'rejectTarget' => $rejectTarget,
            'canApprove'   => $this->canApprove(),
            'hasFilter'    => $term !== '' || $this->statusFilter !== 'all' || $this->urgencyFilter !== 'all',
            'selectedValue' => round(collect($pending)->whereIn('id', $this->selected)->sum('estimate'), 2),
        ]);
    }
}