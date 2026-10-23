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
use App\Models\ProcessingDuration;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
#[Title('Buat Pesanan Baru')]
class OrderCreate extends Component
{
    use WithFileUploads;

    /* ---------- Form state ---------- */
    public $user_id = '';
    public $items = [];
    public $global_notes = '';

    /* ---------- Shipping (per order) ---------- */
    public $shipping_method = 'pickup';   // pickup | regular | instant
    public $shipping_address = '';

    /* ---------- Price calculator ---------- */
    public $estimated_price = 0;
    public $total_duration_fee = 0;
    public $discount = 0;
    public $discount_percentage = 0;
    public $final_price_input = 0;

    /* ---------- Dropdown data ---------- */
    public $customers = [];
    public $services = [];
    public $materials = [];
    public $sizes = [];
    public $durations = [];

    protected $rules = [
        'user_id'                => 'required|exists:users,id',
        'items.*.service_id'     => 'required|exists:services,id',
        'items.*.material_id'    => 'required|exists:materials,id',
        'items.*.size_id'        => 'required|exists:sizes,id',
        'items.*.duration_id'    => 'required|exists:processing_durations,id',
        'items.*.quantity'       => 'required|integer|min:1',
        'items.*.notes'          => 'nullable|string|max:300',
        'items.*.design_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:20480',
        'global_notes'           => 'nullable|string|max:500',
        'shipping_method'        => 'required|in:pickup,regular,instant',
        'shipping_address'       => 'nullable|required_unless:shipping_method,pickup|string|max:500',
    ];

    public function mount(): void
    {
        $this->customers = User::where('role', 'customer')->get()
            ->map(fn($u) => ['value' => $u->id, 'label' => $u->name . ' (' . $u->email . ')'])->toArray();
        $this->services = Service::where('is_active', true)->get()
            ->map(fn($s) => ['value' => $s->id, 'label' => $s->name])->toArray();
        $this->materials = Material::where('is_active', true)->get()
            ->map(fn($m) => ['value' => $m->id, 'label' => $m->name])->toArray();
        $this->sizes = Size::where('is_active', true)->get()
            ->map(fn($sz) => ['value' => $sz->id, 'label' => $sz->name . ' (' . $sz->area_m2 . ' m2)'])->toArray();
        $this->durations = ProcessingDuration::where('is_active', true)->orderBy('days', 'desc')->get()
            ->map(function ($d) {
                $m = rtrim(rtrim(number_format((float)$d->multiplier, 2, '.', ''), '0'), '.');
                return ['value' => $d->id, 'label' => $d->name . ' (' . $d->days . ' hari) · ×' . $m];
            })->toArray();

        $this->addItem();
    }

    public function addItem(): void
    {
        $normal = ProcessingDuration::where('slug', 'normal')->first();
        $this->items[] = [
            'service_id'   => '',
            'material_id'  => '',
            'size_id'      => '',
            'duration_id'  => $normal->id ?? null,
            'quantity'     => 1,
            'notes'        => '',
            'design_files' => [],
        ];
        $this->calculateEstimatedPrice();
    }

    public function removeItem($index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->calculateEstimatedPrice();
        }
    }

    public function removeFile($index, $fileIndex): void
    {
        $files = $this->items[$index]['design_files'] ?? [];

        if (!isset($files[$fileIndex])) {
            return;
        }

        $file = $files[$fileIndex];

        // Bersihkan file temporary di storage agar tidak menumpuk jadi sampah
        if (is_object($file) && method_exists($file, 'deleteTemporary')) {
            try {
                $file->deleteTemporary();
            } catch (\Throwable $e) {
                // Abaikan jika file temp sudah dibersihkan otomatis oleh Livewire
            }
        }

        unset($files[$fileIndex]);
        $this->items[$index]['design_files'] = array_values($files); // re-index agar tidak bolong
    }

    /**
     * HOOK GENERIK (pengganti updatedItems yang bermasalah di Livewire v3).
     * $property selalu berbentuk "items.0.service_id" (path lengkap) -> parsing tidak ambigu.
     * Signature defensif ($value = null) agar aman baik Livewire mengirim 1 maupun 2 argumen.
     */
    public function updated($property, $value = null): void
    {
        // Reset error kontekstual (membersihkan pesan "stale" saat user berinteraksi ulang)
        if ($property === 'user_id' || $property === 'shipping_address') {
            $this->resetErrorBag($property);
        }
        if ($property === 'shipping_method' && $value === 'pickup') {
            $this->resetErrorBag('shipping_address');
        }

        // Hanya recalc bila field yang mempengaruhi harga berubah
        if (!str_starts_with($property, 'items.')) {
            return;
        }
        $parts = explode('.', $property);      // ["items","0","service_id"]
        $field = $parts[2] ?? null;
        if (in_array($field, ['service_id', 'material_id', 'size_id', 'quantity', 'duration_id'], true)) {
            $this->calculateEstimatedPrice();
        }
    }

    public function calculateEstimatedPrice(): void
    {
        $this->estimated_price    = 0;
        $this->total_duration_fee = 0;

        foreach ($this->items as $item) {
            if (empty($item['service_id']) || empty($item['material_id']) || empty($item['size_id'])) continue;
            $service  = Service::find($item['service_id']);
            $material = Material::find($item['material_id']);
            $size     = Size::find($item['size_id']);
            if (!$service || !$material || !$size) continue;

            $base = (($service->price_per_m2 ?? 0) + ($material->price_per_m2 ?? 0))
                  * ($size->area_m2 ?? 0) * (int)($item['quantity'] ?? 1);
            $mult = $this->multiplierFor($item['duration_id'] ?? null);
            $fee  = round($base * ($mult - 1), 2);

            $this->estimated_price    += ($base + $fee);
            $this->total_duration_fee += $fee;
        }

        $this->estimated_price    = round($this->estimated_price, 2);
        $this->total_duration_fee = round($this->total_duration_fee, 2);

        $this->refreshDiscountView();
    }

    /**
     * Sinkronkan tampilan diskon dgn master terkini:
     * - deal belum diisi  -> default = master
     * - deal > master     -> auto-clamp (cegah state invalid / rugi diam-diam)
     */
    private function refreshDiscountView(): void
    {
        if ($this->estimated_price <= 0) {
            $this->discount = 0;
            $this->discount_percentage = 0;
            return;
        }
        if ($this->final_price_input <= 0) {
            $this->final_price_input = $this->estimated_price;
        }
        if ($this->final_price_input > $this->estimated_price) {
            $this->final_price_input = $this->estimated_price;
        }
        $diff = $this->estimated_price - $this->final_price_input;
        $this->discount = $diff > 0 ? round($diff, 2) : 0;
        $this->discount_percentage = $this->discount > 0
            ? round(($this->discount / $this->estimated_price) * 100, 2) : 0;
    }

    private function multiplierFor($durationId): float
    {
        if (!$durationId) return 1.0;
        $d = ProcessingDuration::find($durationId);
        return $d ? (float)$d->multiplier : 1.0;
    }

    public function getDurationInfo($index): ?array
    {
        $id = $this->items[$index]['duration_id'] ?? null;
        if (!$id) return null;
        $d = ProcessingDuration::find($id);
        if (!$d) return null;
        return [
            'name'        => $d->name,
            'days'        => (int)$d->days,
            'multiplier'  => (float)$d->multiplier,
            'description' => $d->description,
        ];
    }

    public function getItemSubtotal($index): float
    {
        $item = $this->items[$index] ?? null;
        if (!$item || empty($item['service_id']) || empty($item['material_id']) || empty($item['size_id'])) return 0;
        $service  = Service::find($item['service_id']);
        $material = Material::find($item['material_id']);
        $size     = Size::find($item['size_id']);
        if (!$service || !$material || !$size) return 0;
        $base = (($service->price_per_m2 ?? 0) + ($material->price_per_m2 ?? 0))
              * ($size->area_m2 ?? 0) * (int)($item['quantity'] ?? 1);
        $mult = $this->multiplierFor($item['duration_id'] ?? null);
        return round($base + round($base * ($mult - 1), 2), 2);
    }

    public function formatRupiah($angka): string
    {
        return number_format((float)$angka, 0, ',', '.');
    }

    public function parseInputAngka($input): float
    {
        if (is_null($input) || trim((string)$input) === '') return 0;
        $cleaned = str_replace(['.', ',', ' '], '', (string)$input);
        return is_numeric($cleaned) ? (float)$cleaned : 0;
    }

    public function updatedFinalPriceInput($value): void
    {
        $clean = max(0, $this->parseInputAngka($value));
        $this->final_price_input = $clean;
        $this->resetErrorBag('final_price_input');

        if ($this->estimated_price <= 0) { $this->discount = 0; $this->discount_percentage = 0; return; }

        if ($clean > $this->estimated_price) {
            $this->addError('final_price_input', 'Harga Deal tidak boleh melebihi harga master (' . $this->formatRupiah($this->estimated_price) . ').');
            $this->final_price_input = $this->estimated_price;
        }
        $this->discount = round($this->estimated_price - $this->final_price_input, 2);
        $this->discount_percentage = $this->estimated_price > 0
            ? round(($this->discount / $this->estimated_price) * 100, 2) : 0;
    }

    public function updatedDiscountPercentage($value): void
    {
        $pct = max(0, min(100, (float)$value));
        $this->discount_percentage = $pct;
        if ($this->estimated_price > 0) {
            $this->final_price_input = round($this->estimated_price * (1 - $pct / 100), 2);
            $this->discount = round($this->estimated_price - $this->final_price_input, 2);
        }
    }

    public function useMasterPrice(): void
    {
        $this->final_price_input   = $this->estimated_price;
        $this->discount            = 0;
        $this->discount_percentage = 0;
        $this->resetErrorBag('final_price_input');
    }

    public function submitOrder()
    {
        $messages = [
            'user_id.required'           => 'Pelanggan wajib dipilih.',
            'items.*.service_id.required'=> 'Layanan pada setiap item wajib dipilih.',
            'items.*.material_id.required'=> 'Bahan pada setiap item wajib dipilih.',
            'items.*.size_id.required'   => 'Ukuran pada setiap item wajib dipilih.',
            'items.*.duration_id.required'=> 'Durasi pengerjaan pada setiap item wajib dipilih.',
            'items.*.quantity.required'  => 'Jumlah (Qty) wajib diisi.',
            'items.*.quantity.min'       => 'Jumlah minimal 1.',
            'shipping_address.required_unless' => 'Alamat pengiriman wajib diisi karena metode kirim bukan Pickup.',
        ];
        $attributes = [
            'user_id' => 'pelanggan',
            'shipping_address' => 'alamat pengiriman',
        ];
        $this->validate($this->rules, $messages, $attributes);

        // 1) Bangun item + hitung master (incl. durasi) + deadline (max hari)
        $totalMasterPrice = 0;
        $maxDays = 0;
        $itemsToSave = [];

        foreach ($this->items as $itemData) {
            $service  = Service::find($itemData['service_id']);
            $material = Material::find($itemData['material_id']);
            $size     = Size::find($itemData['size_id']);
            $duration = ProcessingDuration::find($itemData['duration_id'] ?? null);
            if (!$service || !$material || !$size || !$duration) continue;

            $base   = (($service->price_per_m2 ?? 0) + ($material->price_per_m2 ?? 0))
                    * ($size->area_m2 ?? 0) * (int)$itemData['quantity'];
            $mult   = (float)$duration->multiplier;
            $fee    = round($base * ($mult - 1), 2);
            $subDur = round($base + $fee, 2);

            $totalMasterPrice += $subDur;
            $maxDays = max($maxDays, (int)$duration->days);

            $paths = [];
            if (!empty($itemData['design_files'])) {
                foreach ($itemData['design_files'] as $file) $paths[] = $file->store('designs', 'public');
            }

            $itemsToSave[] = [
                'service' => $service, 'material' => $material, 'size' => $size, 'duration' => $duration,
                'quantity' => (int)$itemData['quantity'],
                'area_m2'  => round(($size->area_m2 ?? 0) * (int)$itemData['quantity'], 4),
                'multiplier' => $mult,
                'duration_name' => $duration->name,        //  BARU: snapshot nama
                'duration_days' => (int) $duration->days,  //  BARU: snapshot hari
                'fee' => $fee, 'subtotal' => $subDur,
                'notes' => $itemData['notes'] ?? '', 'files' => $paths,
            ];
        }

        if (empty($itemsToSave)) {
            $this->addError('items', 'Minimal satu item lengkap (Layanan, Bahan, Ukuran, Durasi) harus diisi.');
            return;
        }
        $totalMasterPrice = round($totalMasterPrice, 2);

        // 2) Harga deal global + guard keuangan
        $globalFinalPrice = (float)$this->final_price_input;
        if ($globalFinalPrice <= 0) $globalFinalPrice = $totalMasterPrice;
        if ($globalFinalPrice > $totalMasterPrice) {
            $this->addError('final_price_input', 'Harga deal tidak boleh melebihi total harga master (termasuk durasi).');
            return;
        }

        // 3) Create order header (atomik, tanpa update terpisah)
        $order = Order::create([
            'user_id'          => $this->user_id,
            'invoice_number'   => 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(4)),
            'status'           => 'waiting_invoice',
            'shipping_method'  => $this->shipping_method,
            'shipping_address' => $this->shipping_method !== 'pickup' ? $this->shipping_address : null,
            'deadline_date'    => $maxDays > 0 ? now()->addDays($maxDays)->toDateString() : null,
            'notes'            => $this->global_notes,
        ]);

        // 4) Simpan item + distribusi diskon proporsional thd subtotal (incl. durasi)
        $savedFinalTotal = 0;
        $lastItem = null;
        foreach ($itemsToSave as $d) {
            $ratio        = $totalMasterPrice > 0 ? ($d['subtotal'] / $totalMasterPrice) : 0;
            $itemFinal    = round($globalFinalPrice * $ratio, 2);
            $itemDiscount = round($d['subtotal'] - $itemFinal, 2);
            $savedFinalTotal += $itemFinal;

            $lastItem = OrderItem::create([
                'order_id'            => $order->id,
                'service_id'          => $d['service']->id,
                'material_id'         => $d['material']->id,
                'size_id'             => $d['size']->id,
                'duration_id'         => $d['duration']->id,
                'duration_multiplier' => $d['multiplier'],
                'duration_name'       => $d['duration_name'],   //  BARU
                'duration_days'       => $d['duration_days'],   //  BARU
                'duration_fee'        => $d['fee'],
                'quantity'            => $d['quantity'],
                'area_m2'             => $d['area_m2'],
                'unit_price_service'  => $d['service']->price_per_m2 ?? 0,
                'unit_price_material' => $d['material']->price_per_m2 ?? 0,
                'subtotal'            => $d['subtotal'],
                'discount'            => $itemDiscount,
                'final_subtotal'      => $itemFinal,
                'design_file'         => $d['files'],
                'notes'               => $d['notes'],
            ]);
        }

        // 5) Koreksi pembulatan ke item terakhir (SUM final == deal persis)
        $diff = round($globalFinalPrice - $savedFinalTotal, 2);
        if ($lastItem && abs($diff) >= 0.01) {
            $newFinal = round($lastItem->final_subtotal + $diff, 2);
            $lastItem->update([
                'final_subtotal' => $newFinal,
                'discount'       => round($lastItem->subtotal - $newFinal, 2),
            ]);
        }

        session()->flash('success', 'Pesanan ' . $order->invoice_number . ' dibuat. Total Deal: Rp ' . $this->formatRupiah($globalFinalPrice));
        return redirect()->route('admin.orders');
    }

    public function render()
    {
        return view('livewire.admin.order-create');
    }
}