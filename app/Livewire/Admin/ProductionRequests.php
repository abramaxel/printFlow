<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use App\Models\Order;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
#[Title('Ajukan Purchase Request')]
class ProductionRequests extends Component
{
    use WithPagination;

    public string $materialQuery = '';
    public array $materialMatches = [];
    public ?int $materialId = null;
    public array $picked = [];

    public string $quantity = '';
    public string $urgency = 'warning';
    public string $neededBy = '';
    public string $reason = 'Kebutuhan produksi';
    public string $notes = '';

    public string $statusFilter = 'all';
    public array $orderContext = [];

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'produksi', 'production'], true)) {
            abort(403, 'Halaman pengajuan PR hanya untuk tim produksi/gudang dan owner.');
        }

        $this->neededBy = now()->addDays(3)->toDateString();

        if ($oid = (int) request('order', 0)) {
            $this->loadOrderContext($oid);
        }
        if ($mid = (int) request('material', 0)) {
            $this->selectMaterial($mid);
        }
    }

    private function net(Material $m): float
    {
        return round((float) $m->stock - (float) $m->stock_reserved, 4);
    }

    private function suggest(Material $m): float
    {
        $net = $this->net($m);
        $min = (float) ($m->minimum_stock ?? 0);
        $max = (float) ($m->maximum_stock ?? 0);

        $target = $max > 0 ? $max : max($min * 2, $min + 1);
        $qty = max(0, $target - $net);
        if ($qty <= 0) $qty = max($min, 1);

        return round($qty, 4);
    }

    private function urgencyFor(Material $m): string
    {
        $net = $this->net($m);
        if ($net <= 0) return 'critical';
        if ($net <= (float) ($m->minimum_stock ?? 0)) return 'warning';
        return 'normal';
    }

    private function pendingMap()
    {
        return PurchaseRequest::where('status', 'pending')
            ->get(['id', 'material_id', 'pr_number', 'requested_by', 'created_at'])
            ->keyBy('material_id');
    }

    private function hasPending(int $materialId): bool
    {
        return PurchaseRequest::where('material_id', $materialId)->where('status', 'pending')->exists();
    }

    private function loadOrderContext(int $orderId): void
    {
        $order = Order::with('items')->find($orderId);
        if (!$order) return;

        $ids = $order->items->pluck('material_id')->filter()->unique()->values()->all();
        if (empty($ids)) return;

        $materials = Material::whereIn('id', $ids)->get()->keyBy('id');
        $pending = $this->pendingMap();

        $need = [];
        foreach ($order->items as $it) {
            $mid = (int) $it->material_id;
            if ($mid <= 0) continue;
            $need[$mid] = ($need[$mid] ?? 0) + (float) ($it->area_m2 ?? 0);
        }

        $rows = [];
        $lowCount = 0;
        $actionable = 0;

        foreach ($need as $mid => $qty) {
            $m = $materials[$mid] ?? null;
            if (!$m) continue;

            $net = $this->net($m);
            $min = (float) ($m->minimum_stock ?? 0);
            $low = $net <= $min || $qty > $net;
            if ($low) $lowCount++;

            $pr = $pending[$mid] ?? null;
            if ($low && !$pr) $actionable++;

            $rows[] = [
                'id' => $m->id,
                'name' => (string) $m->name,
                'unit' => (string) ($m->unit ?? 'm²'),
                'need' => round($qty, 4),
                'net' => $net,
                'min' => $min,
                'gap' => round(max(0, $qty - $net), 4),
                'suggest' => $this->suggest($m),
                'urgency' => $this->urgencyFor($m),
                'low' => $low,
                'pending' => (bool) $pr,
                'pr_id' => $pr?->id,
                'pr_number' => $pr?->pr_number,
                'pr_by_me' => $pr ? (int) $pr->requested_by === (int) auth()->id() : false,
            ];
        }

        usort($rows, fn($a, $b) => [$b['low'] ? 1 : 0, $b['gap']] <=> [$a['low'] ? 1 : 0, $a['gap']]);

        $this->orderContext = [
            'id' => $order->id,
            'invoice' => (string) $order->invoice_number,
            'status' => (string) $order->status,
            'deadline' => $order->deadline_date?->format('d M Y'),
            'rows' => $rows,
            'low_count' => $lowCount,
            'actionable' => $actionable,
        ];
    }

    public function updatedMaterialQuery($value): void
    {
        $this->materialMatches = [];
        $q = trim((string) $value);
        if (mb_strlen($q) < 2) return;

        $like = '%' . mb_strtolower($q) . '%';
        $this->materialMatches = Material::where('is_active', true)
            ->whereRaw('LOWER(name) LIKE ?', [$like])
            ->orderBy('name')->limit(6)->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'name' => (string) $m->name,
                'unit' => (string) ($m->unit ?? 'm²'),
                'net' => $this->net($m),
                'min' => (float) ($m->minimum_stock ?? 0),
            ])->all();
    }

    public function selectMaterial($id): void
    {
        $m = Material::find($id);
        if (!$m) return;

        if ($this->hasPending($m->id)) {
            session()->flash('pr_warning', 'Bahan ' . $m->name . ' sudah punya PR pending. Batalkan dulu bila ingin mengubah jumlah.');
            $this->dispatch('pr-scroll-to-status');
            return;
        }

        $this->materialId = $m->id;
        $this->picked = [
            'id' => $m->id,
            'name' => (string) $m->name,
            'unit' => (string) ($m->unit ?? 'm²'),
            'stock' => (float) $m->stock,
            'reserved' => (float) $m->stock_reserved,
            'net' => $this->net($m),
            'min' => (float) ($m->minimum_stock ?? 0),
            'max' => (float) ($m->maximum_stock ?? 0),
            'price' => (float) ($m->price_per_m2 ?? 0),
            'lead' => (int) ($m->lead_time_days ?? 3),
        ];

        $this->quantity = (string) $this->suggest($m);
        $this->urgency = $this->urgencyFor($m);
        $this->neededBy = now()->addDays(max(1, (int) ($m->lead_time_days ?? 3)))->toDateString();
        $this->reason = $this->net($m) <= 0 ? 'Stok habis setelah produksi' : 'ROP trigger';

        if (!empty($this->orderContext['invoice'])) {
            $this->notes = 'Kebutuhan untuk pesanan ' . $this->orderContext['invoice'] . '.';
        }

        $this->materialQuery = '';
        $this->materialMatches = [];
        $this->resetErrorBag(['materialId', 'quantity']);

        $this->dispatch('pr-scroll-to-form');
    }

    public function clearMaterial(): void
    {
        $this->materialId = null;
        $this->picked = [];
        $this->quantity = '';
        $this->materialQuery = '';
        $this->materialMatches = [];
    }

    public function useSuggested(): void
    {
        $m = Material::find($this->materialId);
        if ($m) $this->quantity = (string) $this->suggest($m);
        $this->dispatch('pr-scroll-to-form');
    }

    private function store(Material $m, float $qty, string $urgency, string $reason, ?string $neededBy, string $notes): PurchaseRequest
    {
        return PurchaseRequest::create([
            'pr_number' => PurchaseRequest::generatePrNumber(),
            'material_id' => $m->id,
            'requested_quantity' => $qty,
            'unit' => (string) ($m->unit ?? 'm²'),
            'estimated_cost' => round($qty * (float) ($m->price_per_m2 ?? 0), 2),
            'urgency' => $urgency,
            'reason' => mb_substr($reason, 0, 255),
            'status' => 'pending',
            'requested_by' => auth()->id(),
            'needed_by_date' => $neededBy ?: null,
            'notes' => $notes !== '' ? $notes : null,
        ]);
    }

    private function refreshContext(): void
    {
        if (!empty($this->orderContext['id'])) {
            $this->loadOrderContext((int) $this->orderContext['id']);
        }
    }

    public function createRequest(): void
    {
        $this->validate([
            'materialId' => 'required|integer|exists:materials,id',
            'quantity' => 'required|numeric|min:0.0001',
            'urgency' => 'required|in:critical,warning,normal',
            'neededBy' => 'nullable|date',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ], [
            'materialId.required' => 'Pilih bahan baku yang ingin diajukan.',
            'quantity.required' => 'Jumlah yang diminta wajib diisi.',
            'quantity.numeric' => 'Jumlah harus berupa angka.',
            'quantity.min' => 'Jumlah harus lebih dari 0.',
        ]);

        $m = Material::find($this->materialId);
        if (!$m) {
            $this->addError('materialId', 'Bahan tidak ditemukan.');
            return;
        }

        if ($this->hasPending($m->id)) {
            $this->addError('materialId', 'Bahan ini sudah punya PR berstatus pending. Tunggu keputusan owner.');
            return;
        }

        $pr = $this->store($m, round((float) $this->quantity, 4), $this->urgency, (string) $this->reason, $this->neededBy, trim($this->notes));

        $this->clearMaterial();
        $this->notes = '';
        $this->reason = 'Kebutuhan produksi';
        $this->urgency = 'warning';
        $this->neededBy = now()->addDays(3)->toDateString();

        $this->refreshContext();
        session()->flash('pr_success', 'PR ' . $pr->pr_number . ' diajukan. Menunggu persetujuan owner.');
        $this->resetPage();
    }

    public function quickRequest($materialId): void
    {
        $m = Material::find($materialId);
        if (!$m) return;

        if ($this->hasPending($m->id)) {
            session()->flash('pr_warning', 'Bahan ' . $m->name . ' sudah punya PR pending.');
            return;
        }

        $pr = $this->store(
            $m,
            $this->suggest($m),
            $this->urgencyFor($m),
            $this->net($m) <= 0 ? 'Stok habis setelah produksi' : 'ROP trigger',
            now()->addDays(max(1, (int) ($m->lead_time_days ?? 3)))->toDateString(),
            'Diajukan otomatis dari panel alert produksi.'
        );

        $this->refreshContext();
        session()->flash('pr_success', 'PR ' . $pr->pr_number . ' untuk ' . $m->name . ' diajukan.');
        $this->resetPage();
    }

    public function requestAllCritical(): void
    {
        $created = 0;
        $skipped = 0;

        DB::transaction(function () use (&$created, &$skipped) {
            foreach (Material::where('is_active', true)->get() as $m) {
                if ($this->net($m) > (float) ($m->minimum_stock ?? 0)) continue;
                if ($this->hasPending($m->id)) { $skipped++; continue; }

                $this->store(
                    $m,
                    $this->suggest($m),
                    $this->urgencyFor($m),
                    $this->net($m) <= 0 ? 'Stok habis setelah produksi' : 'ROP trigger',
                    now()->addDays(max(1, (int) ($m->lead_time_days ?? 3)))->toDateString(),
                    'Pengajuan massal dari panel alert produksi.'
                );
                $created++;
            }
        });

        $this->refreshContext();

        session()->flash(
            $created > 0 ? 'pr_success' : 'pr_warning',
            $created > 0
                ? $created . ' PR diajukan ke owner.' . ($skipped > 0 ? ' ' . $skipped . ' bahan dilewati (sudah pending).' : '')
                : 'Semua bahan kritis sudah diajukan. Tidak ada pengajuan baru.'
        );
        $this->resetPage();
    }

    public function cancelRequest($id): void
    {
        $pr = PurchaseRequest::where('status', 'pending')->where('requested_by', auth()->id())->find($id);
        if (!$pr) {
            session()->flash('pr_warning', 'PR tidak dapat dibatalkan (sudah diproses atau bukan pengajuan Anda).');
            return;
        }
        $pr->update(['status' => 'cancelled']);

        $this->refreshContext();
        session()->flash('pr_success', 'PR ' . $pr->pr_number . ' dibatalkan. Bahan bisa diajukan ulang.');
    }

    public function setStatusFilter(string $v): void
    {
        $this->statusFilter = $v;
        $this->resetPage();
    }

    public function render()
    {
        $pending = $this->pendingMap();
        $myId = (int) auth()->id();

        $alerts = [];
        $actionable = 0;
        $pendingAlerts = 0;

        foreach (Material::where('is_active', true)->orderBy('name')->get() as $m) {
            $net = $this->net($m);
            $min = (float) ($m->minimum_stock ?? 0);
            if ($net > $min) continue;

            $pr = $pending[$m->id] ?? null;
            if ($pr) { $pendingAlerts++; } else { $actionable++; }

            $alerts[] = [
                'id' => $m->id,
                'name' => (string) $m->name,
                'unit' => (string) ($m->unit ?? 'm²'),
                'net' => $net,
                'min' => $min,
                'stock' => (float) $m->stock,
                'reserved' => (float) $m->stock_reserved,
                'suggest' => $this->suggest($m),
                'urgency' => $this->urgencyFor($m),
                'price' => (float) ($m->price_per_m2 ?? 0),
                'lead' => (int) ($m->lead_time_days ?? 3),
                'pending' => (bool) $pr,
                'pr_id' => $pr?->id,
                'pr_number' => $pr?->pr_number,
                'pr_by_me' => $pr ? (int) $pr->requested_by === $myId : false,
                'pr_since' => $pr?->created_at?->format('d M Y, H:i'),
                'ratio' => $min > 0 ? max(0, min(100, (int) round(max(0, $net) / $min * 100))) : 0,
            ];
        }

        usort($alerts, function ($a, $b) {
            $ra = $a['pending'] ? 1 : 0;
            $rb = $b['pending'] ? 1 : 0;
            if ($ra !== $rb) return $ra <=> $rb;
            return [$a['net'] <= 0 ? 0 : 1, $a['net']] <=> [$b['net'] <= 0 ? 0 : 1, $b['net']];
        });

        $agg = PurchaseRequest::selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved")
            ->selectRaw("SUM(CASE WHEN status='received' THEN 1 ELSE 0 END) as received")
            ->selectRaw("SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as cancelled")
            ->selectRaw("SUM(CASE WHEN status='pending' THEN estimated_cost ELSE 0 END) as pending_value")
            ->first();

        $approvedRaw = PurchaseRequest::with(['material', 'approver', 'requester'])
            ->whereIn('status', ['approved', 'ordered'])
            ->orderByRaw('needed_by_date IS NULL, needed_by_date ASC')
            ->latest('approved_at')
            ->get();

        $incoming = [];
        $incomingValue = 0.0;

        foreach ($approvedRaw as $pr) {
            $m = $pr->material;
            $net = $m ? $this->net($m) : 0.0;
            $qty = (float) $pr->requested_quantity;
            $price = (float) ($m->price_per_m2 ?? 0);
            $est = (float) $pr->estimated_cost > 0 ? (float) $pr->estimated_cost : round($qty * $price, 2);
            $incomingValue += $est;
            $lead = max(0, (int) ($m->lead_time_days ?? 3));

            $incoming[] = [
                'id' => $pr->id,
                'number' => (string) $pr->pr_number,
                'material' => $m->name ?? 'Bahan dihapus',
                'unit' => (string) ($pr->unit ?: ($m->unit ?? 'm²')),
                'qty' => $qty,
                'estimate' => $est,
                'net' => $net,
                'min' => (float) ($m->minimum_stock ?? 0),
                'after' => round($net + $qty, 4),
                'urgency' => (string) $pr->urgency,
                'approver' => $pr->approver?->name ?? 'Owner',
                'approved' => $pr->approved_at?->format('d M Y, H:i'),
                'needed' => $pr->needed_by_date?->format('d M Y'),
                'overdue' => $pr->needed_by_date ? $pr->needed_by_date->isPast() : false,
                'lead' => $lead,
                'eta' => $pr->approved_at ? $pr->approved_at->copy()->addDays($lead)->format('d M Y') : null,
            ];
        }

        $stats = [
            'alerts' => count($alerts),
            'actionable' => $actionable,
            'pending_alerts' => $pendingAlerts,
            'empty' => count(array_filter($alerts, fn($a) => $a['net'] <= 0)),
            'pending' => (int) ($agg->pending ?? 0),
            'approved' => (int) ($agg->approved ?? 0),
            'received' => (int) ($agg->received ?? 0),
            'cancelled' => (int) ($agg->cancelled ?? 0),
            'pending_value' => (float) ($agg->pending_value ?? 0),
            'incoming' => count($incoming),
            'incoming_value' => round($incomingValue, 2),
        ];

        $prs = PurchaseRequest::with(['material', 'requester', 'approver'])
            ->when($this->statusFilter !== 'all', fn($q) => $q->where('status', $this->statusFilter))
            ->orderByRaw("CASE WHEN status='pending' THEN 0 WHEN status='approved' THEN 1 ELSE 2 END")
            ->orderByRaw("CASE WHEN urgency='critical' THEN 0 WHEN urgency='warning' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(10);

        $estimate = 0;
        if ($this->materialId && is_numeric($this->quantity)) {
            $estimate = round((float) $this->quantity * (float) ($this->picked['price'] ?? 0), 2);
        }

        return view('livewire.admin.production-requests', [
            'alerts' => $alerts,
            'stats' => $stats,
            'prs' => $prs,
            'estimate' => $estimate,
            'myId' => $myId,
            'incoming' => $incoming,
        ]);
    }
}