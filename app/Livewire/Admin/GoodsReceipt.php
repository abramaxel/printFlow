<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\{Material, Supplier, PurchaseRequest, PurchaseReceipt, PurchaseReceiptItem};
use App\Services\MrpEngine;
use Illuminate\Support\Facades\DB;
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

    protected $rules = [
        'supplier_id' => 'required|exists:suppliers,id',
        'receipt_date' => 'required|date',
        'supplier_doc_number' => 'nullable|string|max:100',
        'purchase_request_id' => 'nullable|exists:purchase_requests,id',
        'notes' => 'nullable|string|max:500',
        'delivery_note_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'invoice_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'items' => 'required|array|min:1',
        'items.*.material_id' => 'required|exists:materials,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.unit_cost' => 'required|numeric|min:0',
        'items.*.expiry_date' => 'nullable|date',
    ];

    public function mount()
    {
        $this->receipt_date = now()->format('Y-m-d');
        $this->addItem();

        // Pre-fill otomatis jika datang dari tombol "Terima Barang" di halaman PR
        if ($prId = request()->query('pr_id')) {
            $pr = PurchaseRequest::with('material')->find($prId);
            if ($pr && in_array($pr->status, ['pending', 'approved'])) {
                $this->purchase_request_id = $pr->id;
                $this->items[0]['material_id'] = $pr->material_id;
                $this->items[0]['quantity'] = (float) $pr->requested_quantity;
                $this->items[0]['unit_cost'] = $pr->material ? (float) $pr->material->price : 0;
            }
        }
    }

    public function addItem()
    {
        $this->items[] = ['material_id' => '', 'quantity' => 1, 'unit_cost' => 0, 'expiry_date' => ''];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if (count($this->items) === 0) $this->addItem();
    }

    // Auto-isi harga saat material dipilih
    public function updatedItems($value, $key)
    {
        if (str_ends_with($key, '.material_id')) {
            $idx = (int) explode('.', $key)[1];
            $mat = Material::find($this->items[$idx]['material_id'] ?? null);
            if ($mat) $this->items[$idx]['unit_cost'] = (float) $mat->price;
        }
    }

    public function save()
    {
        $this->validate();

        $mrp = app(MrpEngine::class);

        $receipt = DB::transaction(function () use ($mrp) {
            $dnPath = $this->delivery_note_file ? $this->delivery_note_file->store('purchase-receipts', 'public') : null;
            $invPath = $this->invoice_file ? $this->invoice_file->store('purchase-receipts', 'public') : null;

            $total = 0;
            foreach ($this->items as $it) {
                $total += (float) $it['quantity'] * (float) $it['unit_cost'];
            }

            $receipt = PurchaseReceipt::create([
                'bpb_number' => PurchaseReceipt::generateBpbNumber(),
                'supplier_id' => $this->supplier_id,
                'purchase_request_id' => $this->purchase_request_id ?: null,
                'supplier_doc_number' => $this->supplier_doc_number ?: null,
                'receipt_date' => $this->receipt_date,
                'delivery_note_file' => $dnPath,
                'invoice_file' => $invPath,
                'total_cost' => $total,
                'status' => 'completed',
                'received_by' => auth()->id(),
                'notes' => $this->notes ?: null,
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
                    'material_id' => $material->id,
                    'quantity' => (float) $it['quantity'],
                    'unit' => $material->unit,
                    'unit_cost' => (float) $it['unit_cost'],
                    'total_cost' => (float) $it['quantity'] * (float) $it['unit_cost'],
                    'batch_number' => $movement->batch_number,
                    'expiry_date' => !empty($it['expiry_date']) ? $it['expiry_date'] : null,
                ]);
            }

            // Tutup loop MRP: PR otomatis jadi received
            if ($this->purchase_request_id) {
                PurchaseRequest::where('id', $this->purchase_request_id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->update(['status' => 'received', 'approved_by' => auth()->id(), 'approved_at' => now()]);
            }

            return $receipt;
        });

        session()->flash('success', "✅ Penerimaan {$receipt->bpb_number} tercatat. Stok, batch, dan audit trail otomatis terupdate.");
        return redirect()->route('admin.goods-receipt');
    }

    public function render()
    {
        return view('livewire.admin.goods-receipt', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'materials' => Material::where('is_active', true)->orderBy('name')->get(),
            'openPrs' => PurchaseRequest::with('material')
                ->where('status', 'approved') // ✅ Hanya yang sudah disetujui Owner
                ->latest()
                ->get(),
            'receipts' => PurchaseReceipt::with(['supplier', 'items.material', 'receiver'])->latest()->limit(10)->get(),
        ]);
    }
}