<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\{Material, Supplier, PurchaseRequest, PurchaseReceipt, PurchaseReceiptItem};
use App\Services\MrpEngine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Penerimaan Barang')]
class GoodsReceipt extends Component
{
    use WithFileUploads;

    public $supplier_id = '';
    public $receipt_date = '';
    public $supplier_doc_number = '';
    public $purchase_request_id = '';
    public $notes = '';
    public $items = [];
    public $delivery_note_file;
    public $invoice_file;

    public string $historySearch = '';

    protected $rules = [
        'supplier_id'          => 'required|exists:suppliers,id',
        'receipt_date'         => 'required|date',
        'supplier_doc_number'  => 'nullable|string|max:100',
        'purchase_request_id'  => 'nullable|exists:purchase_requests,id',
        'notes'                => 'nullable|string|max:500',
        'delivery_note_file'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'invoice_file'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'items'                => 'required|array|min:1',
        'items.*.material_id'  => 'required|exists:materials,id',
        'items.*.quantity'     => 'required|numeric|min:0.01',
        'items.*.unit_cost'    => 'required|numeric|min:0',
        'items.*.expiry_date'  => 'nullable|date',
    ];

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'admin', 'produksi', 'production'], true)) {
            abort(403, 'Halaman penerimaan barang hanya untuk staf gudang/produksi dan owner.');
        }

        $this->receipt_date = now()->format('Y-m-d');
        $this->addItem();

        $prId = (int) (request()->query('pr_id') ?? request()->query('pr') ?? 0);
        if ($prId > 0) $this->fillFromPr($prId, false);
    }

    private function priceOf(?Material $m): float
    {
        if (!$m) return 0.0;
        return (float) ($m->price ?? $m->price_per_m2 ?? 0);
    }

    private function defaultExpiry(?Material $m): string
    {
        $days = (int) ($m->shelf_life_days ?? 0);
        return $days > 0 ? now()->addDays($days)->format('Y-m-d') : '';
    }

    public function addItem(): void
    {
        $this->items[] = ['material_id' => '', 'quantity' => 1, 'unit_cost' => 0, 'expiry_date' => ''];
    }

    public function removeItem($index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if (count($this->items) === 0) $this->addItem();
    }

    public function fillFromPr($id, bool $scroll = true): void
    {
        $pr = PurchaseRequest::with('material')->find($id);
        if (!$pr || !in_array($pr->status, ['pending', 'approved', 'ordered'], true)) {
            session()->flash('gr_error', 'PR tidak dapat diterima (status sudah berubah).');
            return;
        }

        $m = $pr->material;
        $this->purchase_request_id = $pr->id;
        $this->items = [[
            'material_id' => $pr->material_id,
            'quantity'    => (float) $pr->requested_quantity,
            'unit_cost'   => $this->priceOf($m),
            'expiry_date' => $this->defaultExpiry($m),
        ]];

        if (!$this->supplier_doc_number) {
            $this->supplier_doc_number = 'SJ-' . $pr->pr_number;
        }

        $this->resetErrorBag();
        if ($scroll) $this->dispatch('gr-scroll-to-form');
    }

    public function updatedPurchaseRequestId($value): void
    {
        if ($value) $this->fillFromPr($value);
    }

    public function updatedItems($value, $key): void
    {
        if (!str_ends_with((string) $key, '.material_id')) return;

        $idx = (int) explode('.', (string) $key)[1];
        $m = Material::find($this->items[$idx]['material_id'] ?? null);
        if (!$m) return;

        $this->items[$idx]['unit_cost'] = $this->priceOf($m);
        if (empty($this->items[$idx]['expiry_date'])) {
            $this->items[$idx]['expiry_date'] = $this->defaultExpiry($m);
        }
    }

    public function save()
    {
        $this->validate();

        $mrp = app(MrpEngine::class);

        try {
            $receipt = DB::transaction(function () use ($mrp) {
                $dnPath  = $this->delivery_note_file ? $this->delivery_note_file->store('purchase-receipts', 'public') : null;
                $invPath = $this->invoice_file ? $this->invoice_file->store('purchase-receipts', 'public') : null;

                $total = 0;
                foreach ($this->items as $it) {
                    $total += (float) $it['quantity'] * (float) $it['unit_cost'];
                }

                $receipt = PurchaseReceipt::create([
                    'bpb_number'          => PurchaseReceipt::generateBpbNumber(),
                    'supplier_id'         => $this->supplier_id,
                    'purchase_request_id' => $this->purchase_request_id ?: null,
                    'supplier_doc_number' => $this->supplier_doc_number ?: null,
                    'receipt_date'        => $this->receipt_date,
                    'delivery_note_file'  => $dnPath,
                    'invoice_file'        => $invPath,
                    'total_cost'          => round($total, 2),
                    'status'              => 'completed',
                    'received_by'         => auth()->id(),
                    'notes'               => $this->notes ?: null,
                ]);

                foreach ($this->items as $it) {
                    $material = Material::findOrFail($it['material_id']);

                    $movement = $mrp->recordStockIn(
                        material: $material,
                        quantity: (float) $it['quantity'],
                        unitCost: (float) $it['unit_cost'],
                        subType: 'purchase_receipt',
                        referenceType: 'PurchaseReceipt',
                        referenceId: $receipt->id,
                        batchNumber: null,
                        expiryDate: !empty($it['expiry_date']) ? Carbon::parse($it['expiry_date']) : null,
                        userId: auth()->id(),
                        notes: 'Penerimaan ' . $receipt->bpb_number,
                    );

                    PurchaseReceiptItem::create([
                        'purchase_receipt_id' => $receipt->id,
                        'material_id'         => $material->id,
                        'quantity'            => (float) $it['quantity'],
                        'unit'                => $material->unit,
                        'unit_cost'           => (float) $it['unit_cost'],
                        'total_cost'          => round((float) $it['quantity'] * (float) $it['unit_cost'], 2),
                        'batch_number'        => $movement->batch_number ?? null,
                        'expiry_date'         => !empty($it['expiry_date']) ? $it['expiry_date'] : null,
                    ]);
                }

                if ($this->purchase_request_id) {
                    PurchaseRequest::where('id', $this->purchase_request_id)
                        ->whereIn('status', ['pending', 'approved', 'ordered'])
                        ->update(['status' => 'received']);
                }

                return $receipt;
            });
        } catch (\Throwable $e) {
            session()->flash('gr_error', 'Gagal menyimpan penerimaan: ' . $e->getMessage());
            return;
        }

        session()->flash('gr_success', 'Penerimaan ' . $receipt->bpb_number . ' tercatat. Stok, batch FIFO, dan audit trail diperbarui.');
        return redirect()->route('admin.goods-receipt');
    }

    public function render()
    {
        $materials = Material::where('is_active', true)->orderBy('name')->get();
        $catalog = [];
        foreach ($materials as $m) {
            $catalog[$m->id] = [
                'name'  => (string) $m->name,
                'unit'  => (string) ($m->unit ?? 'm²'),
                'price' => $this->priceOf($m),
                'net'   => round((float) $m->stock - (float) $m->stock_reserved, 4),
                'min'   => (float) ($m->minimum_stock ?? 0),
                'shelf' => (int) ($m->shelf_life_days ?? 0),
                'batch' => (bool) ($m->is_batch_tracked ?? false),
            ];
        }

        $approvedRaw = PurchaseRequest::with(['material', 'approver', 'requester'])
            ->whereIn('status', ['approved', 'ordered'])
            ->orderByRaw('needed_by_date IS NULL, needed_by_date ASC')
            ->latest('approved_at')
            ->get();

        $approved = [];
        $approvedValue = 0.0;
        foreach ($approvedRaw as $pr) {
            $m = $pr->material;
            $net = $m ? round((float) $m->stock - (float) $m->stock_reserved, 4) : 0.0;
            $qty = (float) $pr->requested_quantity;
            $price = $this->priceOf($m);
            $est = (float) $pr->estimated_cost > 0 ? (float) $pr->estimated_cost : round($qty * $price, 2);
            $approvedValue += $est;
            $lead = max(0, (int) ($m->lead_time_days ?? 3));

            $approved[] = [
                'id'        => $pr->id,
                'number'    => (string) $pr->pr_number,
                'material'  => $m->name ?? 'Bahan dihapus',
                'unit'      => (string) ($pr->unit ?: ($m->unit ?? 'm²')),
                'qty'       => $qty,
                'price'     => $price,
                'estimate'  => $est,
                'net'       => $net,
                'min'       => (float) ($m->minimum_stock ?? 0),
                'after'     => round($net + $qty, 4),
                'urgency'   => (string) $pr->urgency,
                'approver'  => $pr->approver?->name ?? 'Owner',
                'approved'  => $pr->approved_at?->format('d M Y, H:i'),
                'needed'    => $pr->needed_by_date?->format('d M Y'),
                'overdue'   => $pr->needed_by_date ? $pr->needed_by_date->isPast() : false,
                'lead'      => $lead,
                'requester' => $pr->requester?->name ?? 'Sistem / MRP',
            ];
        }

        $monthCount = (int) PurchaseReceipt::whereYear('receipt_date', now()->year)->whereMonth('receipt_date', now()->month)->count();
        $monthValue = (float) PurchaseReceipt::whereYear('receipt_date', now()->year)->whereMonth('receipt_date', now()->month)->sum('total_cost');

        $expiring = 0;
        if (Schema::hasTable('material_batches')) {
            $expiring = (int) DB::table('material_batches')
                ->where('status', 'active')
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<=', now()->addDays(30)->toDateString())
                ->count();
        }

        $lines = [];
        $grand = 0.0;
        foreach ($this->items as $i => $it) {
            $c = $catalog[$it['material_id']] ?? null;
            $qty = (float) ($it['quantity'] ?? 0);
            $cost = (float) ($it['unit_cost'] ?? 0);
            $sub = round($qty * $cost, 2);
            $grand += $sub;
            $lines[] = [
                'index' => $i,
                'name'  => $c['name'] ?? null,
                'unit'  => $c['unit'] ?? 'unit',
                'qty'   => $qty,
                'cost'  => $cost,
                'sub'   => $sub,
                'net'   => $c['net'] ?? 0,
                'min'   => $c['min'] ?? 0,
                'after' => round(($c['net'] ?? 0) + $qty, 4),
                'batch' => $c['batch'] ?? false,
            ];
        }

        $term = trim($this->historySearch);
        $receipts = PurchaseReceipt::with(['supplier', 'items.material', 'receiver'])
            ->when($term !== '', function ($q) use ($term) {
                $like = '%' . mb_strtolower($term) . '%';
                $q->where(function ($w) use ($like) {
                    $w->whereRaw('LOWER(bpb_number) LIKE ?', [$like])
                      ->orWhereRaw('LOWER(coalesce(supplier_doc_number,\'\')) LIKE ?', [$like])
                      ->orWhereHas('supplier', fn($s) => $s->whereRaw('LOWER(name) LIKE ?', [$like]));
                });
            })
            ->latest('receipt_date')->latest('id')->limit(12)->get();

        $history = [];
        foreach ($receipts as $r) {
            $history[] = [
                'id'       => $r->id,
                'bpb'      => (string) $r->bpb_number,
                'date'     => $r->receipt_date ? Carbon::parse($r->receipt_date)->format('d M Y') : '-',
                'supplier' => $r->supplier->name ?? '-',
                'doc'      => (string) ($r->supplier_doc_number ?? ''),
                'total'    => (float) $r->total_cost,
                'receiver' => $r->receiver->name ?? '-',
                'items'    => $r->items->map(fn($it) => [
                    'name' => $it->material->name ?? '-',
                    'qty'  => (float) $it->quantity,
                    'unit' => (string) $it->unit,
                    'batch' => (string) ($it->batch_number ?? ''),
                ])->all(),
            ];
        }

        return view('livewire.admin.goods-receipt', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name', 'phone']),
            'catalog'   => $catalog,
            'approved'  => $approved,
            'history'   => $history,
            'lines'     => $lines,
            'grand'     => round($grand, 2),
            'stats'     => [
                'approved' => count($approved),
                'approved_value' => round($approvedValue, 2),
                'month_count' => $monthCount,
                'month_value' => round($monthValue, 2),
                'expiring' => $expiring,
            ],
        ]);
    }
}