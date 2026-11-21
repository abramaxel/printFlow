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
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProcessingDuration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
#[Title('Buat Pesanan Baru')]
class OrderCreate extends Component
{
    use WithFileUploads;

    /* ---------- Pelanggan ---------- */
    public string $customer_mode = '';
    public string $phone_query = '';
    public array $matches = [];
    public bool $searched = false;
    public $user_id = '';
    public array $member = [];
    public string $guest_name = '';
    public string $guest_phone = '';
    public string $guest_email = '';

    /* ---------- Item & catatan ---------- */
    public array $items = [];
    public string $global_notes = '';

    /* ---------- Pengiriman ---------- */
    public string $shipping_method = 'pickup';
    public string $shipping_address = '';
    public string $shipping_detail = '';
    public bool $save_address_to_member = false;

    /* ---------- Kalkulator ---------- */
    public float $estimated_price = 0;
    public float $total_duration_fee = 0;
    public float $discount = 0;
    public float $discount_percentage = 0;
    public float $final_price_input = 0;

    /* ---------- Katalog master (dimuat sekali) ---------- */
    public array $categories = [];
    public array $durations = [];
    public array $services = [];
    public array $materials = [];
    public array $sizes = [];
    public array $servicesByCat = [];
    public array $materialsByCat = [];
    public array $sizesByCat = [];

    public string $shop_name = '';
    public string $shop_address = '';
    public string $shop_hours = '';

    public function mount(): void
    {
        $role = auth()->user()->role ?? '';
        if (!in_array($role, ['owner', 'super_admin', 'admin', 'cs'], true)) {
            abort(403, 'Anda tidak berhak membuat pesanan.');
        }

        $this->shop_name    = (string) config('app.brand.name', config('app.name', 'FC Murni Jaya'));
        $this->shop_address = (string) config('app.brand.address', '');
        $this->shop_hours   = (string) config('app.brand.hours', 'Senin–Sabtu, 08.00–17.00');

        $this->loadCatalog();

        if (empty($this->items)) $this->addItem();
    }

    private function loadCatalog(): void
    {
        $this->categories = Category::where('is_active', true)->orderBy('name')
            ->get(['id', 'name'])->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->all();

        $this->durations = ProcessingDuration::where('is_active', true)->orderBy('days')
            ->get(['id', 'name', 'days', 'multiplier', 'description'])
            ->map(fn($d) => [
                'id' => $d->id, 'name' => $d->name, 'days' => (int) $d->days,
                'multiplier' => (float) $d->multiplier, 'description' => $d->description,
            ])->all();

        $svc = Service::where('is_active', true)->orderBy('name')->get(['id', 'category_id', 'name', 'price_per_m2']);
        $this->services = $svc->mapWithKeys(fn($s) => [$s->id => ['name' => $s->name, 'price' => (float) ($s->price_per_m2 ?? 0)]])->all();
        $this->servicesByCat = $svc->groupBy('category_id')
            ->map(fn($g) => $g->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values()->all())->all();

        $mat = Material::where('is_active', true)->orderBy('name')->get(['id', 'category_id', 'name', 'price_per_m2']);
        $this->materials = $mat->mapWithKeys(fn($m) => [$m->id => ['name' => $m->name, 'price' => (float) ($m->price_per_m2 ?? 0)]])->all();
        $this->materialsByCat = $mat->groupBy('category_id')
            ->map(fn($g) => $g->map(fn($m) => ['id' => $m->id, 'name' => $m->name])->values()->all())->all();

        $sz = Size::where('is_active', true)->orderBy('name')->get(['id', 'category_id', 'name', 'area_m2']);
        $this->sizes = $sz->mapWithKeys(fn($s) => [$s->id => ['name' => $s->name, 'area' => (float) ($s->area_m2 ?? 0)]])->all();
        $globalSizes = $sz->whereNull('category_id')->values();
        foreach ($this->categories as $c) {
            $this->sizesByCat[$c['id']] = $sz->where('category_id', $c['id'])->concat($globalSizes)
                ->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values()->all();
        }
    }

    /* ================= PELANGGAN ================= */

    private function phoneDigitsSql(): string
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            return "regexp_replace(coalesce(phone,''), '[^0-9]', '', 'g')";
        }
        $e = "coalesce(phone,'')";
        foreach ([' ', '-', '+', '(', ')', '.'] as $ch) $e = "REPLACE($e, '$ch', '')";
        return $e;
    }

    private function phoneKeySql(): string
    {
        $base = $this->phoneDigitsSql();
        if (DB::connection()->getDriverName() === 'pgsql') {
            return "regexp_replace($base, '^(62|0)', '')";
        }
        return $base;
    }

    private function subscriber(string $raw): string
    {
        $d = preg_replace('/\D+/', '', $raw) ?? '';
        if (str_starts_with($d, '62')) $d = substr($d, 2);
        elseif (str_starts_with($d, '0')) $d = substr($d, 1);
        return $d;
    }

    public function waNumber(string $raw): string
    {
        $d = $this->subscriber($raw);
        return $d === '' ? '' : '62' . $d;
    }

    public function setMode(string $mode): void
    {
        if (!in_array($mode, ['member', 'guest'], true)) return;

        if ($mode === 'guest' && $this->guest_phone === '' && $this->phone_query !== '') {
            $this->guest_phone = trim($this->phone_query);
        }
        if ($mode === 'guest') { $this->user_id = ''; $this->member = []; }

        $this->customer_mode = $mode;
        $this->matches = [];
        $this->searched = false;
        $this->resetErrorBag();
    }

    public function switchToGuest(): void
    {
        $phone = trim($this->phone_query);
        $this->setMode('guest');
        if ($phone !== '') $this->guest_phone = $phone;
    }

    public function updatedPhoneQuery($value): void
    {
        $this->user_id = '';
        $this->member = [];
        $this->matches = [];
        $this->searched = false;

        $q = trim((string) $value);
        if (mb_strlen($q) < 3) return;

        $query = User::where('role', 'customer');

        if (preg_match('/[A-Za-z]/', $q)) {
            $like = '%' . mb_strtolower($q) . '%';
            $query->where(fn($w) => $w->whereRaw('LOWER(name) LIKE ?', [$like])->orWhereRaw('LOWER(email) LIKE ?', [$like]));
        } else {
            $d = $this->subscriber($q);
            if (strlen($d) < 3) return;
            $query->whereRaw($this->phoneDigitsSql() . ' LIKE ?', ['%' . $d . '%']);
        }

        $this->matches = $query->orderBy('name')->limit(6)->get()
            ->map(fn($u) => [
                'id' => $u->id, 'name' => $u->name, 'phone' => (string) ($u->phone ?? ''),
                'email' => (string) $u->email, 'tier' => (string) ($u->tier ?? 'regular'),
            ])->all();
        $this->searched = true;
    }

    public function selectMember($id): void
    {
        $u = User::where('role', 'customer')->find($id);
        if (!$u) return;

        $this->user_id = $u->id;
        $this->member = [
            'id' => $u->id, 'name' => $u->name, 'phone' => (string) ($u->phone ?? ''),
            'email' => (string) $u->email, 'tier' => (string) ($u->tier ?? 'regular'),
            'address' => (string) ($u->address ?? ''), 'detail' => (string) ($u->address_detail ?? ''),
        ];
        $this->matches = [];
        $this->phone_query = '';
        $this->searched = false;
        $this->prefillAddress();
        $this->resetErrorBag(['user_id', 'phone_query']);
    }

    public function clearMember(): void
    {
        $this->user_id = '';
        $this->member = [];
        $this->phone_query = '';
        $this->matches = [];
        $this->searched = false;
    }

    private function prefillAddress(): void
    {
        if ($this->shipping_method === 'pickup') return;
        if (trim($this->shipping_address) === '' && !empty($this->member['address'])) {
            $this->shipping_address = $this->member['address'];
            $this->shipping_detail  = (string) ($this->member['detail'] ?? '');
        }
    }

    public function updatedShippingMethod($value): void
    {
        if ($value === 'pickup') {
            $this->resetErrorBag(['shipping_address', 'shipping_detail']);
            return;
        }
        $this->prefillAddress();
    }

    /* ================= ITEM ================= */

    public function addItem(): void
    {
        $normal = collect($this->durations)->firstWhere('name', 'Normal') ?? ($this->durations[0] ?? null);

        $this->items[] = [
            'category_id' => '', 'service_id' => '', 'material_id' => '', 'size_id' => '',
            'duration_id' => $normal['id'] ?? null, 'quantity' => 1, 'notes' => '', 'design_files' => [],
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
        if (!isset($files[$fileIndex])) return;

        $file = $files[$fileIndex];
        if (is_object($file) && method_exists($file, 'deleteTemporary')) {
            try { $file->deleteTemporary(); } catch (\Throwable $e) {}
        }
        unset($files[$fileIndex]);
        $this->items[$index]['design_files'] = array_values($files);
    }

    public function updated($property, $value = null): void
    {
        if (!str_starts_with($property, 'items.')) return;

        $parts = explode('.', $property);
        $index = (int) ($parts[1] ?? -1);
        $field = $parts[2] ?? null;
        if ($index < 0) return;

        if ($field === 'category_id') {
            $this->items[$index]['service_id']  = '';
            $this->items[$index]['material_id'] = '';
            $this->items[$index]['size_id']     = '';
            $this->calculateEstimatedPrice();
            return;
        }

        if (in_array($field, ['service_id', 'material_id', 'size_id', 'quantity', 'duration_id'], true)) {
            $this->calculateEstimatedPrice();
        }
    }

    public function cleanFileName($file): string
    {
        if (!is_object($file)) return 'File desain';
        $name = method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : '';
        if ($name === '' || preg_match('/^[a-f0-9]{24,}(\.[a-z0-9]+)?$/i', $name)) {
            $base = method_exists($file, 'getFilename') ? basename($file->getFilename()) : '';
            if (preg_match('/[a-f0-9]{20,}-(.+)$/i', $base, $m)) return str_replace('-', ' ', $m[1]);
            return 'File desain (temporary)';
        }
        return $name;
    }

    /* ================= HARGA ================= */

    private function multiplierFor($durationId): float
    {
        foreach ($this->durations as $d) if ((int) $d['id'] === (int) $durationId) return (float) $d['multiplier'];
        return 1.0;
    }

    private function lineBase(array $item): float
    {
        $s = $this->services[$item['service_id']] ?? null;
        $m = $this->materials[$item['material_id']] ?? null;
        $z = $this->sizes[$item['size_id']] ?? null;
        if (!$s || !$m || !$z) return 0.0;
        return ($s['price'] + $m['price']) * $z['area'] * max(1, (int) ($item['quantity'] ?? 1));
    }

    public function calculateEstimatedPrice(): void
    {
        $this->estimated_price = 0;
        $this->total_duration_fee = 0;

        foreach ($this->items as $item) {
            $base = $this->lineBase($item);
            if ($base <= 0) continue;
            $fee = round($base * ($this->multiplierFor($item['duration_id'] ?? null) - 1), 2);
            $this->estimated_price += $base + $fee;
            $this->total_duration_fee += $fee;
        }

        $this->estimated_price = round($this->estimated_price, 2);
        $this->total_duration_fee = round($this->total_duration_fee, 2);
        $this->final_price_input = $this->estimated_price;
        $this->discount = 0;
        $this->discount_percentage = 0;
        $this->refreshDiscountView();
    }

    private function refreshDiscountView(): void
    {
        if ($this->estimated_price <= 0) { $this->discount = 0; $this->discount_percentage = 0; return; }
        if ($this->final_price_input <= 0) $this->final_price_input = $this->estimated_price;
        if ($this->final_price_input > $this->estimated_price) $this->final_price_input = $this->estimated_price;
        $diff = $this->estimated_price - $this->final_price_input;
        $this->discount = $diff > 0 ? round($diff, 2) : 0;
        $this->discount_percentage = $this->discount > 0 ? round(($this->discount / $this->estimated_price) * 100, 2) : 0;
    }

    public function getDurationInfo($index): ?array
    {
        $id = $this->items[$index]['duration_id'] ?? null;
        if (!$id) return null;
        foreach ($this->durations as $d) if ((int) $d['id'] === (int) $id) return $d;
        return null;
    }

    public function getItemSubtotal($index): float
    {
        $item = $this->items[$index] ?? null;
        if (!$item) return 0;
        $base = $this->lineBase($item);
        if ($base <= 0) return 0;
        return round($base + round($base * ($this->multiplierFor($item['duration_id'] ?? null) - 1), 2), 2);
    }

    public function formatRupiah($angka): string
    {
        return number_format((float) $angka, 0, ',', '.');
    }

    public function parseInputAngka($input): float
    {
        if (is_null($input) || trim((string) $input) === '') return 0;
        $cleaned = str_replace(['.', ',', ' '], '', (string) $input);
        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    public function updatedFinalPriceInput($value): void
    {
        $clean = max(0, $this->parseInputAngka($value));
        $this->final_price_input = $clean;
        $this->resetErrorBag('final_price_input');

        if ($this->estimated_price <= 0) { $this->discount = 0; $this->discount_percentage = 0; return; }

        if ($clean > $this->estimated_price) {
            $this->addError('final_price_input', 'Harga deal tidak boleh melebihi harga master (' . $this->formatRupiah($this->estimated_price) . ').');
            $this->final_price_input = $this->estimated_price;
        }
        $this->discount = round($this->estimated_price - $this->final_price_input, 2);
        $this->discount_percentage = round(($this->discount / $this->estimated_price) * 100, 2);
    }

    public function updatedDiscountPercentage($value): void
    {
        $pct = max(0, min(100, (float) $value));
        $this->discount_percentage = $pct;
        if ($this->estimated_price > 0) {
            $this->final_price_input = round($this->estimated_price * (1 - $pct / 100), 2);
            $this->discount = round($this->estimated_price - $this->final_price_input, 2);
        }
    }

    public function useMasterPrice(): void
    {
        $this->final_price_input = $this->estimated_price;
        $this->discount = 0;
        $this->discount_percentage = 0;
        $this->resetErrorBag('final_price_input');
    }

    public function resetToMasterPrice(): void
    {
        $this->useMasterPrice();
    }

    /* ================= SIMPAN ================= */

    private function rules(): array
    {
        $r = [
            'items.*.category_id'    => 'required|exists:categories,id',
            'items.*.service_id'     => 'required|exists:services,id',
            'items.*.material_id'    => 'required|exists:materials,id',
            'items.*.size_id'        => 'required|exists:sizes,id',
            'items.*.duration_id'    => 'required|exists:processing_durations,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.notes'          => 'nullable|string|max:300',
            'items.*.design_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:20480',
            'global_notes'           => 'nullable|string|max:500',
            'shipping_method'        => 'required|in:pickup,regular,instant',
            'shipping_address'       => 'nullable|required_unless:shipping_method,pickup|string|max:1000',
            'shipping_detail'        => 'nullable|string|max:500',
        ];

        if ($this->customer_mode === 'member') {
            $r['user_id'] = 'required|exists:users,id';
        } elseif ($this->customer_mode === 'guest') {
            $r['guest_name']  = 'required|string|min:3|max:255';
            $r['guest_phone'] = ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s().]+$/'];
            $r['guest_email'] = 'nullable|email:rfc|max:255';
        } else {
            $r['customer_mode'] = 'required|in:member,guest';
        }

        return $r;
    }

    private function findCustomerByPhone(string $raw): ?User
    {
        $d = $this->subscriber($raw);
        if (strlen($d) < 6) return null;

        $q = User::where('role', 'customer');
        if (DB::connection()->getDriverName() === 'pgsql') {
            $q->whereRaw($this->phoneKeySql() . ' = ?', [$d]);
        } else {
            $q->whereRaw($this->phoneDigitsSql() . ' LIKE ?', ['%' . $d]);
        }
        return $q->first();
    }

    private function resolveCustomer(array $cols): User
    {
        if ($this->customer_mode === 'member') {
            $u = User::where('role', 'customer')->find($this->user_id);
            if (!$u) throw new \RuntimeException('Pelanggan member tidak ditemukan.');
            return $u;
        }

        $phone = preg_replace('/[\s\-\(\)\.]/', '', trim($this->guest_phone)) ?? '';
        $addr   = $this->shipping_method !== 'pickup' ? trim($this->shipping_address) : '';
        $detail = $this->shipping_method !== 'pickup' ? trim($this->shipping_detail) : '';

        $existing = $this->findCustomerByPhone($phone);
        if ($existing) {
            $fill = [];
            if (trim((string) $existing->name) === '') $fill['name'] = trim($this->guest_name);
            if ($addr !== '' && trim((string) $existing->address) === '') $fill['address'] = $addr;
            if ($detail !== '' && in_array('address_detail', $cols, true) && trim((string) $existing->address_detail) === '') {
                $fill['address_detail'] = $detail;
            }
            if ($fill) $existing->forceFill($fill)->save();
            session()->flash('info', 'Nomor sudah terdaftar. Pesanan ditautkan ke member: ' . $existing->name);
            return $existing;
        }

        $email = trim($this->guest_email);
        if ($email !== '') {
            $byEmail = User::where('email', $email)->first();
            if ($byEmail) {
                if ($byEmail->role !== 'customer') throw new \RuntimeException('Email tersebut dipakai akun staf. Gunakan email lain.');
                return $byEmail;
            }
        } else {
            $sub = $this->subscriber($phone) ?: Str::lower(Str::random(6));
            do { $email = 'guest.' . $sub . '.' . Str::lower(Str::random(4)) . '@guest.local'; }
            while (User::where('email', $email)->exists());
        }

        $data = [
            'name' => trim($this->guest_name), 'email' => $email,
            'password' => Hash::make(Str::random(32)), 'role' => 'customer',
            'phone' => $phone, 'address' => $addr !== '' ? $addr : null, 'status' => 'active',
        ];
        if (in_array('tier', $cols, true)) $data['tier'] = 'regular';
        if (in_array('is_guest', $cols, true)) $data['is_guest'] = true;
        if (in_array('address_detail', $cols, true)) $data['address_detail'] = $detail !== '' ? $detail : null;
        if (in_array('internal_notes', $cols, true)) {
            $data['internal_notes'] = 'Guest dibuat oleh ' . (auth()->user()->name ?? 'staf') . ' pada ' . now()->format('d M Y H:i');
        }

        $u = new User();
        $u->forceFill($data)->save();
        return $u;
    }

    public function submitOrder()
    {
        $this->validate($this->rules(), [
            'customer_mode.required' => 'Pilih dulu: Member Terdaftar atau Guest Baru.',
            'user_id.required' => 'Cari dan pilih pelanggan member berdasarkan nomor telepon.',
            'guest_name.required' => 'Nama guest wajib diisi.',
            'guest_name.min' => 'Nama guest minimal 3 karakter.',
            'guest_phone.required' => 'Nomor telepon / WhatsApp guest wajib diisi.',
            'guest_phone.regex' => 'Nomor hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
            'guest_email.email' => 'Format email tidak valid.',
            'items.*.category_id.required' => 'Divisi produksi setiap item wajib dipilih.',
            'items.*.service_id.required' => 'Layanan setiap item wajib dipilih.',
            'items.*.material_id.required' => 'Bahan setiap item wajib dipilih.',
            'items.*.size_id.required' => 'Ukuran setiap item wajib dipilih.',
            'items.*.duration_id.required' => 'Durasi pengerjaan setiap item wajib dipilih.',
            'items.*.quantity.required' => 'Jumlah (Qty) wajib diisi.',
            'items.*.quantity.min' => 'Jumlah minimal 1.',
            'shipping_address.required_unless' => 'Alamat pengiriman wajib diisi karena metode kirim bukan ambil di toko.',
        ]);

        try {
            $result = DB::transaction(function () {
                $cols = Schema::getColumnListing('users');
                $customer = $this->resolveCustomer($cols);

                $totalMaster = 0; $maxDays = 0; $rows = [];

                foreach ($this->items as $it) {
                    $service  = Service::find($it['service_id']);
                    $material = Material::find($it['material_id']);
                    $size     = Size::find($it['size_id']);
                    $duration = ProcessingDuration::find($it['duration_id'] ?? null);
                    if (!$service || !$material || !$size || !$duration) continue;

                    $qty  = max(1, (int) $it['quantity']);
                    $base = (($service->price_per_m2 ?? 0) + ($material->price_per_m2 ?? 0)) * ($size->area_m2 ?? 0) * $qty;
                    $mult = (float) $duration->multiplier;
                    $fee  = round($base * ($mult - 1), 2);
                    $sub  = round($base + $fee, 2);

                    $totalMaster += $sub;
                    $maxDays = max($maxDays, (int) $duration->days);

                    $paths = [];
                    foreach (($it['design_files'] ?? []) as $file) {
                        if (is_object($file)) $paths[] = $file->store('designs', 'public');
                    }

                    $rows[] = [
                        'service' => $service, 'material' => $material, 'size' => $size, 'duration' => $duration,
                        'quantity' => $qty, 'area_m2' => round(($size->area_m2 ?? 0) * $qty, 4),
                        'multiplier' => $mult, 'fee' => $fee, 'subtotal' => $sub,
                        'notes' => $it['notes'] ?? '', 'files' => $paths,
                    ];
                }

                if (empty($rows)) throw new \RuntimeException('Minimal satu item lengkap (divisi, layanan, bahan, ukuran, durasi) harus diisi.');

                $totalMaster = round($totalMaster, 2);
                $deal = (float) $this->final_price_input;
                if ($deal <= 0) $deal = $totalMaster;
                if ($deal > $totalMaster) throw new \RuntimeException('Harga deal tidak boleh melebihi total harga master.');

                $order = Order::create([
                    'user_id' => $customer->id,
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(4)),
                    'status' => 'waiting_invoice',
                    'shipping_method' => $this->shipping_method,
                    'shipping_address' => $this->shipping_method !== 'pickup'
                        ? trim($this->shipping_address . ($this->shipping_detail !== '' ? "\nPatokan: " . trim($this->shipping_detail) : ''))
                        : null,
                    'deadline_date' => $maxDays > 0 ? now()->addDays($maxDays)->toDateString() : null,
                    'notes' => $this->global_notes,
                ]);

                $savedTotal = 0; $last = null;
                foreach ($rows as $d) {
                    $ratio = $totalMaster > 0 ? $d['subtotal'] / $totalMaster : 0;
                    $final = round($deal * $ratio, 2);
                    $savedTotal += $final;

                    $last = OrderItem::create([
                        'order_id' => $order->id,
                        'service_id' => $d['service']->id,
                        'material_id' => $d['material']->id,
                        'size_id' => $d['size']->id,
                        'duration_id' => $d['duration']->id,
                        'duration_multiplier' => $d['multiplier'],
                        'duration_name' => $d['duration']->name,
                        'duration_days' => (int) $d['duration']->days,
                        'duration_fee' => $d['fee'],
                        'quantity' => $d['quantity'],
                        'area_m2' => $d['area_m2'],
                        'unit_price_service' => $d['service']->price_per_m2 ?? 0,
                        'unit_price_material' => $d['material']->price_per_m2 ?? 0,
                        'subtotal' => $d['subtotal'],
                        'discount' => round($d['subtotal'] - $final, 2),
                        'final_subtotal' => $final,
                        'design_file' => $d['files'],
                        'notes' => $d['notes'],
                    ]);
                }

                $diff = round($deal - $savedTotal, 2);
                if ($last && abs($diff) >= 0.01) {
                    $newFinal = round($last->final_subtotal + $diff, 2);
                    $last->update(['final_subtotal' => $newFinal, 'discount' => round($last->subtotal - $newFinal, 2)]);
                }

                if ($this->customer_mode === 'member' && $this->save_address_to_member && $this->shipping_method !== 'pickup') {
                    $fill = ['address' => trim($this->shipping_address)];
                    if (in_array('address_detail', $cols, true)) $fill['address_detail'] = trim($this->shipping_detail);
                    $customer->forceFill($fill)->save();
                }

                return [$order, $deal];
            });
        } catch (\Throwable $e) {
            $this->addError('submit', $e->getMessage());
            return;
        }

        [$order, $deal] = $result;
        session()->flash('success', 'Pesanan ' . $order->invoice_number . ' dibuat. Total deal: Rp ' . $this->formatRupiah($deal));
        return redirect()->route('admin.orders.show', $order->id);
    }

    public function render()
    {
        return view('livewire.admin.order-create');
    }
}