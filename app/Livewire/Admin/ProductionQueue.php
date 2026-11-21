<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\Material;
use App\Models\Service;
use App\Models\Size;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Layout('components.layouts.admin')]
#[Title('Antrian Produksi')]
class ProductionQueue extends Component
{
    public string $search = '';
    public string $stage = 'all';
    public string $sort = 'priority';

    public ?int $incidentOrderId = null;
    public string $incidentReason = 'material_short';
    public string $incidentNote = '';

    private const QUEUE = ['paid', 'processing', 'qc'];

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'produksi', 'production'], true)) {
            abort(403, 'Halaman ini hanya untuk tim produksi dan owner.');
        }
        $this->stage = (string) request('stage', $this->stage);
    }

    public function reasons(): array
    {
        return [
            'material_short'  => 'Bahan baku kurang / habis',
            'material_defect' => 'Bahan cacat atau tidak sesuai',
            'machine'         => 'Mesin bermasalah / maintenance',
            'design'          => 'File desain rusak / resolusi rendah',
            'customer'        => 'Menunggu konfirmasi pelanggan',
            'other'           => 'Kendala lain',
        ];
    }

    public function setStage(string $value): void { $this->stage = $value; }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->stage = 'all';
        $this->sort = 'priority';
    }

    public function openIncident(int $orderId): void
    {
        $order = Order::whereIn('status', self::QUEUE)->find($orderId);
        if (!$order) return;

        $this->incidentOrderId = $order->id;
        $this->incidentReason = 'material_short';
        $this->incidentNote = '';
        $this->resetErrorBag(['incidentNote']);
    }

    public function closeIncident(): void
    {
        $this->incidentOrderId = null;
        $this->incidentNote = '';
        $this->resetErrorBag(['incidentNote']);
    }

    public function submitIncident(): void
    {
        $this->validate([
            'incidentReason' => 'required|string',
            'incidentNote'   => 'required|string|min:5|max:500',
        ], [
            'incidentNote.required' => 'Jelaskan kendalanya agar tim lain bisa menindaklanjuti.',
            'incidentNote.min'      => 'Penjelasan minimal 5 karakter.',
            'incidentNote.max'      => 'Penjelasan maksimal 500 karakter.',
        ]);

        $order = Order::whereIn('status', self::QUEUE)->find($this->incidentOrderId);
        if (!$order) { $this->closeIncident(); return; }

        $label = $this->reasons()[$this->incidentReason] ?? 'Kendala lain';
        $stamp = now()->format('d M Y H:i');
        $actor = (string) (auth()->user()->name ?? 'Staf');
        $line  = "[KENDALA {$stamp}] {$label} — " . trim($this->incidentNote) . " (oleh {$actor})";

        DB::transaction(function () use ($order, $label, $line) {
            $order->priority = 'high';
            $order->notes = trim((string) $order->notes) === '' ? $line : trim((string) $order->notes) . "\n" . $line;
            $order->save();

            if (Schema::hasTable('production_incidents')) {
                try {
                    $cols = Schema::getColumnListing('production_incidents');
                    $map = [
                        'order_id'    => $order->id,
                        'reported_by' => auth()->id(),
                        'user_id'     => auth()->id(),
                        'type'        => $this->incidentReason,
                        'category'    => $this->incidentReason,
                        'reason'      => $label,
                        'title'       => $label,
                        'description' => trim($this->incidentNote),
                        'notes'       => trim($this->incidentNote),
                        'note'        => trim($this->incidentNote),
                        'status'      => 'open',
                        'severity'    => 'high',
                    ];
                    $data = [];
                    foreach ($map as $k => $v) {
                        if (in_array($k, $cols, true) && !is_null($v)) $data[$k] = $v;
                    }
                    if (in_array('created_at', $cols, true)) $data['created_at'] = now();
                    if (in_array('updated_at', $cols, true)) $data['updated_at'] = now();
                    if (isset($data['order_id'])) DB::table('production_incidents')->insert($data);
                } catch (\Throwable $e) {
                }
            }
        });

        $this->closeIncident();
        session()->flash('queue_success', 'Kendala dilaporkan. Pesanan ' . $order->invoice_number . ' ditandai prioritas tinggi.');
    }

    public function confirmPrint(int $orderId): void
    {
        $order = Order::where('status', 'paid')->find($orderId);
        if (!$order) {
            session()->flash('queue_error', 'Pesanan tidak berada pada tahap antre konfirmasi.');
            return;
        }

        try {
            DB::transaction(function () use ($order) {
                $items = $order->items()->get();
                if ($items->isEmpty()) throw new \RuntimeException('Pesanan belum memiliki item.');

                $need = [];
                foreach ($items as $it) {
                    $mid = (int) $it->material_id;
                    if ($mid <= 0) continue;
                    $need[$mid] = ($need[$mid] ?? 0) + $this->itemNeed($it);
                }
                if (empty($need)) throw new \RuntimeException('Tidak ada kebutuhan bahan yang bisa dihitung.');

                $locked = [];
                foreach ($need as $mid => $qty) {
                    $m = Material::whereKey($mid)->lockForUpdate()->first();
                    if (!$m) throw new \RuntimeException('Bahan baku tidak ditemukan.');
                    $net = (float) $m->stock - (float) $m->stock_reserved;
                    if ($qty > $net + 0.0001) {
                        throw new \RuntimeException('Stok ' . $m->name . ' kurang. Butuh ' . number_format($qty, 2) . ', tersedia ' . number_format(max(0, $net), 2) . ' ' . ($m->unit ?? '') . '.');
                    }
                    $locked[$mid] = $m;
                }

                $cols = Schema::hasTable('material_movements') ? Schema::getColumnListing('material_movements') : [];

                foreach ($need as $mid => $qty) {
                    $m = $locked[$mid];
                    $m->stock = max(0, round((float) $m->stock - $qty, 4));
                    $m->stock_reserved = max(0, round((float) $m->stock_reserved - $qty, 4));
                    $m->save();

                    if (!empty($cols)) {
                        $unitCost = (float) ($m->price_per_m2 ?? 0);
                        $map = [
                            'material_id'    => $m->id,
                            'movement_type'  => 'out',
                            'sub_type'       => 'production',
                            'quantity'       => $qty,
                            'unit'           => $m->unit ?? 'm2',
                            'unit_cost'      => $unitCost,
                            'total_cost'     => round($unitCost * $qty, 2),
                            'movement_date'  => now()->toDateString(),
                            'reference_type' => 'order',
                            'reference_id'   => $order->id,
                            'notes'          => 'Ambil bahan untuk produksi ' . $order->invoice_number,
                            'user_id'        => auth()->id(),
                        ];
                        $data = [];
                        foreach ($map as $k => $v) {
                            if (in_array($k, $cols, true) && !is_null($v)) $data[$k] = $v;
                        }
                        if (in_array('created_at', $cols, true)) $data['created_at'] = now();
                        if (in_array('updated_at', $cols, true)) $data['updated_at'] = now();
                        if (isset($data['material_id'])) {
                            try { DB::table('material_movements')->insert($data); } catch (\Throwable $e) {}
                        }
                    }
                }

                $from = $order->status;
                $order->status = 'processing';
                $order->save();
                $this->logHistory($order, $from, 'processing', 'Konfirmasi ambil bahan & mulai cetak.');
            });
        } catch (\Throwable $e) {
            session()->flash('queue_error', $e->getMessage());
            return;
        }

        session()->flash('queue_success', 'Bahan diambil dari gudang. Pesanan ' . $order->invoice_number . ' masuk tahap cetak.');
    }

    public function sendToQc(int $orderId): void
    {
        $order = Order::where('status', 'processing')->find($orderId);
        if (!$order) return;

        DB::transaction(function () use ($order) {
            $from = $order->status;
            $order->status = 'qc';
            $order->save();
            $this->logHistory($order, $from, 'qc', 'Cetak selesai, dikirim ke Quality Control.');
        });

        session()->flash('queue_success', 'Pesanan ' . $order->invoice_number . ' dikirim ke Quality Control.');
    }

    public function passQc(int $orderId): void
    {
        $order = Order::where('status', 'qc')->find($orderId);
        if (!$order) return;

        DB::transaction(function () use ($order) {
            $from = $order->status;
            $order->status = 'shipping';
            $order->save();
            $this->logHistory($order, $from, 'shipping', 'Lulus QC, siap dikirim / diambil pelanggan.');
        });

        session()->flash('queue_success', 'Pesanan ' . $order->invoice_number . ' lulus QC dan siap dikirim.');
    }

    private function logHistory(Order $order, string $from, string $to, string $note): void
    {
        if (!Schema::hasTable('order_histories')) return;

        try {
            $cols = Schema::getColumnListing('order_histories');
            $map = [
                'order_id'    => $order->id,
                'user_id'     => auth()->id(),
                'changed_by'  => auth()->id(),
                'from_status' => $from,
                'old_status'  => $from,
                'to_status'   => $to,
                'new_status'  => $to,
                'status'      => $to,
                'notes'       => $note,
                'note'        => $note,
                'description' => $note,
            ];
            $data = [];
            foreach ($map as $k => $v) {
                if (in_array($k, $cols, true) && !is_null($v)) $data[$k] = $v;
            }
            if (in_array('created_at', $cols, true)) $data['created_at'] = now();
            if (in_array('updated_at', $cols, true)) $data['updated_at'] = now();
            if (isset($data['order_id'])) DB::table('order_histories')->insert($data);
        } catch (\Throwable $e) {
        }
    }

    private function itemNeed($item): float
    {
        return round((float) ($item->area_m2 ?? 0), 4);
    }

    public function render()
    {
        $orders = Order::with(['user', 'items', 'incidents'])
            ->whereIn('status', self::QUEUE)
            ->get();

        $materialIds = [];
        $serviceIds = [];
        $sizeIds = [];
        foreach ($orders as $o) {
            foreach ($o->items as $it) {
                if ($it->material_id) $materialIds[] = (int) $it->material_id;
                if ($it->service_id)  $serviceIds[]  = (int) $it->service_id;
                if ($it->size_id)     $sizeIds[]     = (int) $it->size_id;
            }
        }

        $materials = Material::whereIn('id', array_unique($materialIds))->get()->keyBy('id');
        $services  = Service::whereIn('id', array_unique($serviceIds))->get()->keyBy('id');
        $sizes     = Size::whereIn('id', array_unique($sizeIds))->get()->keyBy('id');

        $approvedPrs = PurchaseRequest::whereIn('status', ['approved', 'ordered'])
            ->get(['id', 'material_id', 'pr_number', 'requested_quantity', 'unit'])
            ->keyBy('material_id');

        $rows = [];
        $stats = ['total' => 0, 'awaiting' => 0, 'processing' => 0, 'qc' => 0, 'blocked' => 0, 'incoming' => 0];

        foreach ($orders as $o) {
            $needByMaterial = [];
            $items = [];

            foreach ($o->items as $it) {
                $m = $materials[$it->material_id] ?? null;
                $s = $services[$it->service_id] ?? null;
                $z = $sizes[$it->size_id] ?? null;

                $need = $this->itemNeed($it);
                if ($need <= 0 && $z) {
                    $need = round((float) ($z->area_m2 ?? 0) * max(1, (int) $it->quantity), 4);
                }

                $stock    = (float) ($m->stock ?? 0);
                $reserved = (float) ($m->stock_reserved ?? 0);
                $net      = $stock - $reserved;

                if ($m) {
                    $mid = (int) $m->id;
                    $needByMaterial[$mid] = ($needByMaterial[$mid] ?? 0) + $need;
                }

                $items[] = [
                    'material_id' => (int) ($it->material_id ?? 0),
                    'service'     => (string) ($s->name ?? 'Layanan dihapus'),
                    'material'    => (string) ($m->name ?? 'Bahan dihapus'),
                    'unit'        => (string) ($m->unit ?? 'm²'),
                    'size'        => (string) ($z->name ?? '-'),
                    'quantity'    => (int) ($it->quantity ?? 1),
                    'need'        => $need,
                    'stock'       => $stock,
                    'reserved'    => $reserved,
                    'net'         => $net,
                    'sla_name'    => (string) ($it->duration_name ?? 'Normal'),
                    'sla_days'    => (int) ($it->duration_days ?? 0),
                    'sla_mult'    => (float) ($it->duration_multiplier ?? 1),
                    'notes'       => (string) ($it->notes ?? ''),
                ];
            }

            $blocked = false;
            foreach ($needByMaterial as $mid => $qty) {
                $m = $materials[$mid] ?? null;
                $net = $m ? ((float) $m->stock - (float) $m->stock_reserved) : 0;
                if ($qty > $net + 0.0001) { $blocked = true; break; }
            }

            $incomingRows = [];
            foreach ($items as $i => $row) {
                $items[$i]['short'] = $row['need'] > $row['net'] + 0.0001;
                $items[$i]['ratio'] = $row['need'] > 0 ? min(100, (int) round(max(0, $row['net']) / $row['need'] * 100)) : 100;

                $apr = $approvedPrs[$row['material_id']] ?? null;
                $items[$i]['pr_id'] = $apr?->id;
                $items[$i]['pr_number'] = $apr?->pr_number;

                if ($apr && $items[$i]['short']) {
                    $incomingRows[] = [
                        'pr_id'    => $apr->id,
                        'number'   => (string) $apr->pr_number,
                        'material' => $row['material'],
                        'qty'      => (float) $apr->requested_quantity,
                        'unit'     => (string) ($apr->unit ?: $row['unit']),
                    ];
                }
            }

            $deadline = $o->deadline_date;
            $daysLeft = $deadline
                ? (int) round(now()->startOfDay()->diffInDays($deadline->copy()->startOfDay(), false))
                : null;

            $stageKey = $o->status === 'paid' ? 'awaiting' : $o->status;

            $stats['total']++;
            $stats[$stageKey] = ($stats[$stageKey] ?? 0) + 1;
            $stats['incoming'] += count($incomingRows);
            if ($o->status === 'paid' && $blocked) $stats['blocked']++;

            $rows[] = [
                'id'             => $o->id,
                'invoice'        => (string) $o->invoice_number,
                'status'         => (string) $o->status,
                'stage'          => $stageKey,
                'priority'       => (string) ($o->priority ?? 'normal'),
                'customer'       => (string) ($o->user->name ?? 'User dihapus'),
                'phone'          => (string) ($o->user->phone ?? ''),
                'user'           => $o->user,
                'created'        => $o->created_at?->format('d M Y, H:i'),
                'deadline'       => $deadline?->format('d M Y'),
                'days_left'      => $daysLeft,
                'blocked'        => $blocked,
                'items'          => $items,
                'incidents'      => $o->incidents ? $o->incidents->count() : 0,
                'incoming'       => $incomingRows,
                'incoming_count' => count($incomingRows),
                'shipping'       => (string) ($o->shipping_method ?? 'pickup'),
                'sort_dead'      => $deadline?->timestamp ?? PHP_INT_MAX,
                'sort_new'       => $o->created_at?->timestamp ?? 0,
            ];
        }

        $term = mb_strtolower(trim($this->search));
        $rows = array_values(array_filter($rows, function ($r) use ($term) {
            if ($this->stage === 'blocked' && !($r['blocked'] && $r['stage'] === 'awaiting')) return false;
            if ($this->stage !== 'all' && $this->stage !== 'blocked' && $r['stage'] !== $this->stage) return false;
            if ($term === '') return true;

            if (str_contains(mb_strtolower($r['invoice']), $term)) return true;
            if (str_contains(mb_strtolower($r['customer']), $term)) return true;
            if ($r['phone'] !== '' && str_contains(preg_replace('/\D+/', '', $r['phone']), preg_replace('/\D+/', '', $term))) return true;
            foreach ($r['items'] as $it) {
                if (str_contains(mb_strtolower($it['service']), $term)) return true;
                if (str_contains(mb_strtolower($it['material']), $term)) return true;
            }
            return false;
        }));

        usort($rows, function ($a, $b) {
            if ($this->sort === 'deadline') return $a['sort_dead'] <=> $b['sort_dead'];
            if ($this->sort === 'newest') return $b['sort_new'] <=> $a['sort_new'];

            $rank = fn($r) => $r['stage'] === 'awaiting' ? ($r['blocked'] ? 0 : 1) : ($r['stage'] === 'processing' ? 2 : 3);
            $ra = $rank($a); $rb = $rank($b);
            if ($ra !== $rb) return $ra <=> $rb;
            if ($a['sort_dead'] !== $b['sort_dead']) return $a['sort_dead'] <=> $b['sort_dead'];
            return $a['sort_new'] <=> $b['sort_new'];
        });

        return view('livewire.admin.production-queue', [
            'queue'     => $rows,
            'stats'     => $stats,
            'reasons'   => $this->reasons(),
            'hasFilter' => trim($this->search) !== '' || $this->stage !== 'all',
        ]);
    }
}