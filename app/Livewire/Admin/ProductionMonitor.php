<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\ProductionIncident;
use App\Models\OrderHistory;

#[Layout('components.layouts.admin')]
#[Title('Monitor Produksi & Laporan Delay')]
class ProductionMonitor extends Component
{
    public $editId = null;
    public $editValue = '';

    public function mount()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403); // hanya Owner
    }

    private function severity($order): int
    {
        $score = ['delayed' => 30, 'at_risk' => 20, 'stalled' => 10, 'on_track' => 0, 'done' => 0][$order->trace['state']] ?? 0;
        return $score + ($order->incidents->where('status', 'open')->count() * 5);
    }
    /**
     * Formatter durasi SATU-SATUNYA untuk seluruh Monitor.
     * Aturan: <1 jam -> menit | <24 jam -> jam (& menit) | >=24 jam -> hari & jam.
     * Satuan selalu ditulis eksplisit agar tidak ambigu dengan input kendala (hari).
     */
    public function formatDurasi(?float $hours): string
    {
        if ($hours === null) return '-';
        $abs = abs((float) $hours);

        if ($abs < 1) {
            return round($abs * 60) . ' menit';
        }

        if ($abs < 24) {
            $h = (int) floor($abs);
            $m = (int) round(($abs - $h) * 60);
            if ($m === 60) { $h++; $m = 0; }
            return $m > 0 ? "{$h} jam {$m} mnt" : "{$h} jam";
        }

        $d = (int) floor($abs / 24);
        $h = (int) round($abs - ($d * 24));
        if ($h === 24) { $d++; $h = 0; }
        return $h > 0 ? "{$d} hari {$h} jam" : "{$d} hari";
    }

    public function render()
    {
        $orders = Order::with(['user', 'incidents.reporter', 'histories'])
            ->whereNotIn('status', ['completed', 'cancelled', 'rejected'])
            ->get()
            ->each(fn($o) => $o->trace = $o->productionTrace())
            ->filter(fn($o) => in_array($o->trace['state'], ['delayed', 'at_risk', 'stalled'], true)
                            || $o->incidents->where('status', 'open')->count() > 0)
            ->each(fn($o) => $o->severity = $this->severity($o))
            ->sortByDesc('severity')
            ->values();

        $stats = [
            'delayed'  => $orders->where('trace.state', 'delayed')->count(),
            'at_risk'  => $orders->where('trace.state', 'at_risk')->count(),
            'stalled'  => $orders->where('trace.state', 'stalled')->count(),
            'incidents'=> ProductionIncident::where('status', 'open')->count(),
        ];

        return view('livewire.admin.production-monitor', compact('orders', 'stats'));
    }

    public function resolveIncident($incidentId)
    {
        $inc = ProductionIncident::findOrFail($incidentId);
        $inc->update(['status' => 'resolved', 'resolved_at' => now()]);
        OrderHistory::create([
            'order_id' => $inc->order_id, 'user_id' => auth()->id(),
            'old_status' => $inc->order->status, 'new_status' => $inc->order->status,
            'notes' => 'Kendala "' . $inc->type_label . '" dinyatakan SELESAI oleh Owner.',
        ]);
        session()->flash('success', 'Kendala ditandai selesai.');
    }

    public function startEdit($orderId)
    {
        $this->editId = $orderId;
        $this->editValue = Order::findOrFail($orderId)->deadline_date?->format('Y-m-d') ?? '';
    }

    public function saveDeadline($orderId)
    {
        $this->validate(['editValue' => 'required|date']);
        $order = Order::findOrFail($orderId);
        $old = $order->deadline_date?->format('d M Y') ?? '-';
        $order->update(['deadline_date' => $this->editValue]);
        OrderHistory::create([
            'order_id' => $orderId, 'user_id' => auth()->id(),
            'old_status' => $order->status, 'new_status' => $order->status,
            'notes' => "Deadline DIREVISI oleh Owner: {$old} -> " . now()->parse($this->editValue)->format('d M Y'),
        ]);
        $this->editId = null;
        session()->flash('success', 'Deadline direvisi dan tercatat di riwayat.');
    }
}