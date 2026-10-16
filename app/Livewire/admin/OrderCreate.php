<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Service;
use App\Models\Material;
use App\Models\Size;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
#[Title('Buat Pesanan Baru')]
class OrderCreate extends Component
{
    use WithFileUploads;

    public $user_id = '';
    public $items = []; 
    public $global_notes = '';
    
    public $estimated_price = 0;
    public $discount = 0;
    public $discount_percentage = 0;
    public $final_price_input = 0;

    public $customers = [];
    public $services = [];
    public $materials = [];
    public $sizes = [];

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'items.*.service_id' => 'required|exists:services,id',
        'items.*.material_id' => 'required|exists:materials,id',
        'items.*.size_id' => 'required|exists:sizes,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.design_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:20480',
        'global_notes' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->customers = User::where('role', 'customer')->get()->map(fn($u) => ['value' => $u->id, 'label' => $u->name . ' (' . $u->email . ')'])->toArray();
        $this->services = Service::where('is_active', true)->get()->map(fn($s) => ['value' => $s->id, 'label' => $s->name])->toArray();
        $this->materials = Material::where('is_active', true)->get()->map(fn($m) => ['value' => $m->id, 'label' => $m->name])->toArray();
        $this->sizes = Size::where('is_active', true)->get()->map(fn($sz) => ['value' => $sz->id, 'label' => $sz->name . ' (' . $sz->area_m2 . ' m2)'])->toArray();
        
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'service_id' => '', 
            'material_id' => '', 
            'size_id' => '', 
            'quantity' => 1, 
            'notes' => '',
            'design_files' => [], // File desain spesifik per item
        ];
        $this->calculateEstimatedPrice();
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // Re-index array
            $this->calculateEstimatedPrice();
        }
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'items.')) {
            $this->calculateEstimatedPrice();
        }
    }

    public function calculateEstimatedPrice()
    {
        $this->estimated_price = 0;
        $this->discount = 0;
        $this->discount_percentage = 0;

        foreach ($this->items as $item) {
            if (!empty($item['service_id']) && !empty($item['material_id']) && !empty($item['size_id'])) {
                $service = Service::find($item['service_id']);
                $material = Material::find($item['material_id']);
                $size = Size::find($item['size_id']);

                if ($service && $material && $size) {
                    $price_per_m2 = ($service->price_per_m2 ?? 0) + ($material->price_per_m2 ?? 0);
                    $total_area = ($size->area_m2 ?? 0) * $item['quantity'];
                    $this->estimated_price += ($price_per_m2 * $total_area);
                }
            }
        }
        
        // Otomatis isi Harga Deal dengan Harga Master jika masih 0
        if ($this->estimated_price > 0 && $this->final_price_input == 0) {
            $this->final_price_input = $this->estimated_price;
        }
    }

    // Helper format angka
    public function formatRupiah($angka)
    {
        return number_format($angka, 0, ',', '.');
    }

    // Helper parse input angka (hapus titik/koma)
    public function parseInputAngka($input)
    {
        $cleaned = str_replace(['.', ',', ' '], '', (string)$input);
        return is_numeric($cleaned) ? (float)$cleaned : 0;
    }

    public function updatedFinalPriceInput($value)
    {
        $cleanValue = $this->parseInputAngka($value);
        $this->final_price_input = $cleanValue;
        $this->resetErrorBag('final_price_input');

        if ($this->estimated_price <= 0) return;

        if ($this->final_price_input > $this->estimated_price) {
            $this->addError('final_price_input', 'Harga Deal tidak boleh melebihi harga master (' . $this->formatRupiah($this->estimated_price) . ').');
            $this->final_price_input = $this->estimated_price;
            return;
        }

        $this->discount = $this->estimated_price - $this->final_price_input;
        if ($this->estimated_price > 0) {
            $this->discount_percentage = round(($this->discount / $this->estimated_price) * 100, 2);
        }
    }

    public function useMasterPrice()
    {
        $this->final_price_input = $this->estimated_price;
        $this->discount = 0;
        $this->discount_percentage = 0;
    }

    public function getItemSubtotal($index)
    {
        $item = $this->items[$index] ?? null;
        if (!$item || !$item['service_id'] || !$item['material_id'] || !$item['size_id']) return 0;

        $service = Service::find($item['service_id']);
        $material = Material::find($item['material_id']);
        $size = Size::find($item['size_id']);

        if ($service && $material && $size) {
            $pricePerM2 = ($service->price_per_m2 ?? 0) + ($material->price_per_m2 ?? 0);
            $totalArea = ($size->area_m2 ?? 0) * $item['quantity'];
            return $pricePerM2 * $totalArea;
        }
        return 0;
    }

    public function submitOrder()
    {
        $this->validate();

        // 1. Buat Order (Header)
        $order = Order::create([
            'user_id' => $this->user_id,
            'invoice_number' => 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(4)),
            'status' => 'waiting_invoice',
            'delivery_method' => 'pickup',
            'notes' => $this->global_notes,
        ]);

        // 2. Hitung Total Master Price & Proses File Per Item
        $totalMasterPrice = 0;
        $itemsToSave = [];

        foreach ($this->items as $itemData) {
            $service = Service::find($itemData['service_id']);
            $material = Material::find($itemData['material_id']);
            $size = Size::find($itemData['size_id']);

            if (!$service || !$material || !$size) continue;

            $pricePerM2 = ($service->price_per_m2 ?? 0) + ($material->price_per_m2 ?? 0);
            $totalArea = ($size->area_m2 ?? 0) * $itemData['quantity'];
            $itemMasterPrice = $pricePerM2 * $totalArea;

            $totalMasterPrice += $itemMasterPrice;

            // Simpan file desain spesifik untuk item ini
            $itemFilePaths = [];
            if (!empty($itemData['design_files'])) {
                foreach ($itemData['design_files'] as $file) {
                    $itemFilePaths[] = $file->store('designs', 'public');
                }
            }

            $itemsToSave[] = [
                'service' => $service,
                'material' => $material,
                'size' => $size,
                'quantity' => $itemData['quantity'],
                'totalArea' => $totalArea,
                'itemMasterPrice' => $itemMasterPrice,
                'notes' => $itemData['notes'] ?? '',
                'design_files' => $itemFilePaths,
            ];
        }

        // 3. Tentukan Harga Deal Global
        $globalFinalPrice = (float) $this->final_price_input;
        if ($globalFinalPrice <= 0) {
            $globalFinalPrice = (float) $totalMasterPrice;
        }

        // 4. Simpan Order Items
        foreach ($itemsToSave as $itemData) {
            $ratio = $totalMasterPrice > 0 ? ($itemData['itemMasterPrice'] / $totalMasterPrice) : 1;
            $itemFinalPrice = round($globalFinalPrice * $ratio, 2);
            $itemDiscount = round($itemData['itemMasterPrice'] - $itemFinalPrice, 2);

            OrderItem::create([
                'order_id' => $order->id,
                'service_id' => $itemData['service']->id,
                'material_id' => $itemData['material']->id,
                'size_id' => $itemData['size']->id,
                'quantity' => $itemData['quantity'],
                'area_m2' => $itemData['totalArea'],
                'unit_price_service' => $itemData['service']->price_per_m2 ?? 0,
                'unit_price_material' => $itemData['material']->price_per_m2 ?? 0,
                'subtotal' => $itemData['itemMasterPrice'],
                'discount' => $itemDiscount,
                'final_subtotal' => $itemFinalPrice,
                'design_file' => $itemData['design_files'], // Disimpan sebagai JSON array
                'notes' => $itemData['notes'],
            ]);
        }

        session()->flash('success', 'Pesanan berhasil dibuat! Total: Rp ' . number_format($globalFinalPrice, 0, ',', '.'));
        return redirect()->route('admin.orders'); 
    }

    public function render()
    {
        return view('livewire.admin.order-create');
    }
}