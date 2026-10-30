<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
#[Title('Antrian Produksi')]
class ProductionQueue extends Component
{
    public $search = '';
    public $statusFilter = '';   // '' | awaiting | onprocess | ready | shortage
    public $incidentFor = null;
    public $incidentType = 'machine_breakdown';
    public $incidentDesc = '';
    public $incidentImpact = 1;

    /** Batas aman: order dianggap "siap" bila semua item tersedia stoknya. */
    private function itemAvailable($item): bool
    {
        $stock    = (float) ($item->material->stock ?? 0);
        $reserved = (float) ($item->material->stock_reserved ?? 0);
        $need     = (float) $item->area_m2;
        return ($stock - $reserved) >= $need;
    }

    private function orderReady($order): bool
    {
        return $order->items->every(fn($i) => $this->itemAvailable($i));
    }

    public function render()
    {
        // HANYA status sah: paid (antre konfirmasi) & processing (on process).
        // TANPA 'printing' (tidak ada di $allowedStatuses) & TANPA items.duration (hindari RelationNotFound).
        $query = Order::with(['user', 'items.material', 'items.service', 'items.size'])
            ->whereIn('status', ['paid', 'processing']);

        if ($this->search !== '') {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }

        // Urutan kerja: antre-konfirmasi dulu, lalu deadline terdekat (Express/Prioritas naik), lalu terbaru.
        $orders = $query
            ->orderByRaw("CASE WHEN status = 'paid' THEN 0 ELSE 1 END")
            ->orderByRaw("deadline_date IS NULL, deadline_date ASC")
            ->latest('id')
            ->get();

        // Statistik (dihitung dari seluruh pipeline, bukan hanya hasil filter)
        $all = Order::with('items.material')->whereIn('status', ['paid', 'processing'])->get();
        $total     = $all->count();
        $awaiting  = $all->where('status', 'paid')->count();
        $onProcess = $all->where('status', 'processing')->count();
        $ready     = $all->filter(fn($o) => $this->orderReady($o))->count();
        $shortage  = $total - $ready;

        // Filter koleksi (di level collection karena butuh kalkulasi relasi material)
        $filtered = $orders;
        if ($this->statusFilter === 'awaiting')  $filtered = $orders->where('status', 'paid');
        if ($this->statusFilter === 'onprocess') $filtered = $orders->where('status', 'processing');
        if ($this->statusFilter === 'ready')     $filtered = $orders->filter(fn($o) => $this->orderReady($o));
        if ($this->statusFilter === 'shortage')  $filtered = $orders->reject(fn($o) => $this->orderReady($o));

        return view('livewire.admin.production-queue', [
            'orders'    => $filtered->values(),
            'total'     => $total,
            'awaiting'  => $awaiting,
            'onProcess' => $onProcess,
            'ready'     => $ready,
            'shortage'  => $shortage,
        ]);
    }

    /** paid -> processing : bahan fisik diambil dari gudang, stok permanen berkurang. */
    public function confirmMaterialTakeout($orderId)
    {
        $order = Order::with('items.material')->findOrFail($orderId);

        if ($order->status !== 'paid') {
            session()->flash('error', "Order {$order->invoice_number} bukan berstatus 'menunggu konfirmasi'.");
            return;
        }

        // Guard stok: semua item harus cukup (stock - reserved >= area_m2)
        foreach ($order->items as $item) {
            if (!$this->itemAvailable($item)) {
                $m = $item->material->name ?? '?';
                session()->flash('error', "Stok tidak cukup untuk '{$m}'. Tidak bisa konfirmasi.");
                return;
            }
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $need = (float) $item->area_m2;
                // Potong stok fisik (kenyataan lapangan)
                Material::where('id', $item->material_id)->decrement('stock', $need);
                // Lepas kunci reserve SEAMAN: jangan sampai negatif walau reserve sumbernya miring
                Material::where('id', $item->material_id)
                    ->where('stock_reserved', '>=', $need)
                    ->decrement('stock_reserved', $need);
            }

            $old = $order->status;
            $order->update(['status' => 'processing']);

            OrderHistory::create([
                'order_id'   => $order->id,
                'user_id'    => auth()->id(),
                'old_status' => $old,
                'new_status' => 'processing',
                'notes'      => 'Bahan dikonfirmasi & diambil dari gudang. Produksi dimulai (On Process).',
            ]);
        });

        session()->flash('success', "Bahan {$order->invoice_number} ditarik. Status: ON PROCESS.");
    }

    /** processing -> qc : cetak selesai, masuk meja quality control. */
    public function moveToQC($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->status !== 'processing') {
            session()->flash('error', "Hanya order 'On Process' yang bisa dilanjutkan ke QC.");
            return;
        }

        DB::transaction(function () use ($order) {
            $old = $order->status;
            $order->update(['status' => 'qc']);
            OrderHistory::create([
                'order_id'   => $order->id,
                'user_id'    => auth()->id(),
                'old_status' => $old,
                'new_status' => 'qc',
                'notes'      => 'Proses cetak selesai, masuk Quality Control (QC).',
            ]);
        });

        session()->flash('success', "{$order->invoice_number} selesai cetak → QC.");
    }

    public function openIncident($orderId)
    {
        $this->incidentFor = $orderId;
        $this->incidentType = 'machine_breakdown';
        $this->incidentDesc = '';
        $this->incidentImpact = 1;
    }


    public function cancelIncident() { $this->incidentFor = null; }

    public function submitIncident($orderId)
    {
        $this->validate([
            'incidentType'   => 'required|in:machine_breakdown,material_shortage,design_revision,human_error,utility_outage,other',
            'incidentDesc'   => 'required|string|max:500',
            'incidentImpact' => 'required|integer|min:0|max:30',
        ]);

        \App\Models\ProductionIncident::create([
            'order_id'    => $orderId,
            'reported_by' => auth()->id(),
            'type'        => $this->incidentType,
            'description' => $this->incidentDesc,
            'impact_days' => $this->incidentImpact,
            'status'      => 'open',
        ]);

        \App\Models\OrderHistory::create([
            'order_id'   => $orderId,
            'user_id'    => auth()->id(),
            'old_status' => \App\Models\Order::find($orderId)->status,
            'new_status' => \App\Models\Order::find($orderId)->status,
            'notes'      => 'Kendala produksi dilaporkan: ' . \App\Models\ProductionIncident::TYPES[$this->incidentType]
                            . ' (estimasi +' . $this->incidentImpact . ' hari). Diteruskan ke Owner.',
        ]);

        $this->incidentFor = null;
        session()->flash('success', 'Kendala tercatat dan masuk laporan Owner.');
    }

    public function mount()
    {
        $userRole = auth()->user()->role;
        $allowedRoles = ['produksi', 'super_admin', 'owner'];
        
        // Debug: lihat role user
        logger("User role: {$userRole}, Allowed: " . implode(', ', $allowedRoles));
        
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, "Akses ditolak. Role Anda: {$userRole}. Diperlukan: produksi, super_admin, atau owner.");
        }
    }
}