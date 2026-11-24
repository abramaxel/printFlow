<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Detail Pesanan')]
class OrderShow extends Component
{
    public int $orderId = 0;

    public string $whatsapp = '';
    public string $paymentMethod = 'transfer';
    public string $paymentScheme = 'full';
    public string $shippingCost = '0';
    public string $dpPercent = '50';
    public string $dueDays = '7';

    public bool $payOpen = false;
    public string $payAmount = '';
    public string $payNote = '';
    public int $payTick = 0;
    public string $payCheckedAt = '';

    public array $oc = [];
    public array $ic = [];
    public array $pc = [];
    public array $itc = [];
    public bool $hasPayments = false;
    public bool $hasHistory = false;

    public function mount($order = null): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin', 'admin', 'cs', 'produksi', 'production'], true)) {
            abort(403, 'Detail pesanan hanya untuk staf internal.');
        }

        $id = 0;
        if ($order instanceof Order) {
            $id = (int) $order->id;
        } elseif (is_numeric($order)) {
            $id = (int) $order;
        } else {
            $rp = request()->route('order');
            if ($rp instanceof Order) $id = (int) $rp->id;
            elseif (is_numeric($rp)) $id = (int) $rp;
            else $id = (int) request('order', 0);
        }

        $o = Order::find($id);
        if (!$o) abort(404, 'Pesanan tidak ditemukan.');

        $this->orderId = (int) $o->id;

        $this->oc  = Schema::getColumnListing('orders');
        $this->ic  = Schema::hasTable('invoices') ? Schema::getColumnListing('invoices') : [];
        $this->itc = Schema::hasTable('order_items') ? Schema::getColumnListing('order_items') : [];
        $this->hasPayments = Schema::hasTable('payments');
        $this->pc  = $this->hasPayments ? Schema::getColumnListing('payments') : [];
        $this->hasHistory = Schema::hasTable('order_histories');

        $this->whatsapp = (string) ($this->has('orders', 'whatsapp') ? ($o->whatsapp ?? '') : ($o->user->phone ?? ''));
        $this->shippingCost = (string) (float) ($this->has('orders', 'shipping_cost') ? ($o->shipping_cost ?? 0) : 0);

        $inv = $o->invoice;
        if ($inv) {
            $this->paymentMethod = (string) ($inv->payment_method ?? 'transfer');
            $this->paymentScheme = (string) ($inv->payment_scheme ?? 'full');
            $dp = $this->dpPercentOf($inv);
            $this->dpPercent = (string) ($dp > 0 ? $dp : 50);
            if ($this->has('invoices', 'shipping_cost')) {
                $this->shippingCost = (string) (float) ($inv->shipping_cost ?? 0);
            }
        }
    }

    private function has(string $table, string $col): bool
    {
        return match ($table) {
            'orders' => in_array($col, $this->oc, true),
            'invoices' => in_array($col, $this->ic, true),
            'payments' => in_array($col, $this->pc, true),
            'order_items' => in_array($col, $this->itc, true),
            default => false,
        };
    }

    private function pick(string $table, array $cands): ?string
    {
        foreach ($cands as $c) {
            if ($this->has($table, $c)) return $c;
        }
        return null;
    }

    private function dpColumn(): ?string
    {
        return $this->pick('invoices', ['dp_percent', 'dp_percentage', 'down_payment_percent', 'dp_pct', 'dp']);
    }

    private function dpPercentOf($inv): float
    {
        if (!$inv) return 0.0;
        foreach (['dp_percent', 'dp_percentage', 'down_payment_percent', 'dp_pct', 'dp'] as $c) {
            if ($this->has('invoices', $c)) {
                $v = (float) ($inv->{$c} ?? 0);
                if ($v > 0) return $v;
            }
        }
        return 0.0;
    }

    private function order(): Order
    {
        return Order::with(['user', 'items.service', 'items.material', 'items.size', 'invoice'])->findOrFail($this->orderId);
    }

    private function canManage(): bool
    {
        return in_array((string) (auth()->user()->role ?? ''), ['owner', 'super_admin', 'admin', 'cs'], true);
    }

    private function waNorm(string $raw): string
    {
        $d = preg_replace('/\D+/', '', $raw) ?? '';
        if ($d === '') return '';
        if (str_starts_with($d, '0')) $d = '62' . substr($d, 1);
        elseif (str_starts_with($d, '8')) $d = '62' . $d;
        return $d;
    }

    private function invoiceDefaults(): array
    {
        try {
            $rows = DB::select("
                SELECT column_name, data_type, is_nullable, column_default
                FROM information_schema.columns
                WHERE table_name = 'invoices'
                  AND table_schema NOT IN ('information_schema', 'pg_catalog')
                ORDER BY ordinal_position
            ");
        } catch (\Throwable $e) {
            return [];
        }

        $out = [];
        foreach ($rows as $r) {
            $name = (string) $r->column_name;
            if ($name === 'id') continue;
            if (strtoupper((string) $r->is_nullable) === 'YES') continue;
            if ($r->column_default !== null) continue;

            $type = strtolower((string) $r->data_type);
            $out[$name] = match (true) {
                str_contains($type, 'bool') => false,
                str_contains($type, 'timestamp') => now(),
                str_contains($type, 'date') => now()->toDateString(),
                str_contains($type, 'int'),
                str_contains($type, 'numeric'),
                str_contains($type, 'decimal'),
                str_contains($type, 'double'),
                str_contains($type, 'real') => 0,
                str_contains($type, 'json') => '[]',
                default => '',
            };
        }
        return $out;
    }

    /* ================= AKSI ================= */

    public function saveWhatsapp(): void
    {
        if (!$this->canManage()) return;

        $this->validate(['whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s().]*$/']], [
            'whatsapp.regex' => 'Nomor hanya boleh angka, spasi, +, -, dan tanda kurung.',
        ]);

        $o = Order::find($this->orderId);
        if (!$o) return;

        $val = trim($this->whatsapp);
        if ($this->has('orders', 'whatsapp')) {
            $o->forceFill(['whatsapp' => $val])->save();
        } elseif ($o->user) {
            $o->user->forceFill(['phone' => $val])->save();
        }

        session()->flash('os_success', 'Nomor WhatsApp tujuan kirim link diperbarui.');
    }

    public function setMethod(string $v): void
    {
        if (in_array($v, ['transfer', 'cash'], true)) $this->paymentMethod = $v;
    }

    public function setScheme(string $v): void
    {
        if (in_array($v, ['full', 'installment'], true)) {
            $this->paymentScheme = $v;
            if ($v === 'installment' && (float) $this->dpPercent <= 0) $this->dpPercent = '50';
        }
    }

    public function generateInvoice(): void
    {
        if (!$this->canManage()) return;

        $o = $this->order();
        if ($o->invoice) {
            session()->flash('os_error', 'Invoice untuk pesanan ini sudah ada.');
            return;
        }

        $this->validate([
            'shippingCost' => 'required|numeric|min:0|max:100000000',
            'dpPercent' => 'required|numeric|min:1|max:99',
            'dueDays' => 'required|integer|min:1|max:120',
        ], [
            'shippingCost.required' => 'Ongkos kirim wajib diisi (isi 0 bila gratis).',
            'shippingCost.numeric' => 'Ongkos kirim harus berupa angka.',
            'shippingCost.min' => 'Ongkos kirim tidak boleh negatif.',
            'dpPercent.required' => 'Porsi DP wajib diisi bila memakai skema cicilan.',
            'dpPercent.min' => 'Porsi DP minimal 1%.',
            'dpPercent.max' => 'Porsi DP maksimal 99%.',
        ]);

        $items = $this->lineRows($o);
        $subtotal = 0.0;
        foreach ($items as $r) $subtotal += $r['final'];
        $ship = round((float) $this->shippingCost, 2);
        $grand = round($subtotal + $ship, 2);

        if ($grand <= 0) {
            session()->flash('os_error', 'Total tagihan nol. Periksa rincian item terlebih dahulu.');
            return;
        }

        $isInstallment = $this->paymentScheme === 'installment';
        $dpValue = $isInstallment ? max(1, min(99, round((float) $this->dpPercent, 2))) : 0;
        $dpCol = $this->dpColumn();

        if ($isInstallment && !$dpCol) {
            session()->flash('os_error', 'Kolom porsi DP belum ada di tabel invoices. Jalankan php artisan migrate lalu ulangi.');
            return;
        }

        try {
            DB::transaction(function () use ($o, $grand, $ship, $subtotal, $isInstallment, $dpValue, $dpCol) {
                $fill = [
                    'order_id' => $o->id,
                    'total_amount' => $grand,
                    'total_paid' => 0,
                    'status' => 'unpaid',
                ];
                if ($this->has('invoices', 'invoice_number')) $fill['invoice_number'] = (string) $o->invoice_number;
                if ($this->has('invoices', 'payment_token')) $fill['payment_token'] = Str::random(48);
                if ($this->has('invoices', 'payment_method')) $fill['payment_method'] = $this->paymentMethod;
                if ($this->has('invoices', 'payment_scheme')) $fill['payment_scheme'] = $isInstallment ? 'installment' : 'full';
                if ($dpCol) $fill[$dpCol] = $dpValue;
                if ($this->has('invoices', 'shipping_cost')) $fill['shipping_cost'] = $ship;
                if ($this->has('invoices', 'due_date')) $fill['due_date'] = now()->addDays((int) $this->dueDays)->toDateString();
                if ($this->has('invoices', 'subtotal')) $fill['subtotal'] = round($subtotal, 2);
                if ($this->has('invoices', 'discount')) $fill['discount'] = 0;
                if ($this->has('invoices', 'tax')) $fill['tax'] = 0;
                if ($this->has('invoices', 'tax_amount')) $fill['tax_amount'] = 0;

                $fill = array_merge($this->invoiceDefaults(), $fill);

                $inv = new Invoice();
                $inv->forceFill($fill)->save();

                $oFill = [];
                if ($this->has('orders', 'shipping_cost')) $oFill['shipping_cost'] = $ship;
                if ($o->status === 'waiting_invoice') $oFill['status'] = 'unpaid';
                if (!empty($oFill)) $o->forceFill($oFill)->save();

                $note = 'Invoice diterbitkan. Total Rp ' . number_format($grand, 0, ',', '.');
                if ($isInstallment) {
                    $note .= ' · Skema cicilan DP ' . rtrim(rtrim(number_format($dpValue, 2, '.', ''), '0'), '.') . '% = Rp ' . number_format(round($grand * $dpValue / 100, 2), 0, ',', '.');
                }
                $this->logHistory($o, 'unpaid', $note);
            });
        } catch (\Throwable $e) {
            session()->flash('os_error', 'Gagal membuat invoice: ' . $e->getMessage());
            return;
        }

        $this->dpPercent = (string) ($dpValue > 0 ? $dpValue : 50);

        session()->flash('os_success', $isInstallment
            ? 'Invoice cicilan diterbitkan. Pelanggan ditagih DP ' . rtrim(rtrim(number_format($dpValue, 2, '.', ''), '0'), '.') . '% lebih dulu.'
            : 'Invoice diterbitkan. Link pembayaran siap dikirim ke pelanggan.');
    }

    public function refreshPayments(): void
    {
        $this->payTick++;
        $this->payCheckedAt = now()->format('H:i:s');
    }

    public function openPay(): void
    {
        if (!$this->canManage()) return;
        $o = $this->order();
        $inv = $o->invoice;
        if (!$inv) return;

        if ((string) ($inv->payment_method ?? 'transfer') !== 'cash') {
            session()->flash('os_error', 'Pesanan ini dibayar non-tunai. Bukti dikirim pelanggan lewat tautan bayar — cukup verifikasi pada daftar bukti.');
            return;
        }

        $out = max(0, round((float) $inv->total_amount - (float) $inv->total_paid, 2));
        $dp = $this->dpPercentOf($inv);
        $suggest = ($dp > 0 && (float) $inv->total_paid <= 0)
            ? round((float) $inv->total_amount * $dp / 100, 2)
            : $out;

        $this->payAmount = $this->moneyInput(min($suggest, $out));
        $this->payNote = '';
        $this->payOpen = true;
        $this->resetErrorBag(['payAmount']);
    }

    private function moneyInput(float $v): string
    {
        $v = max(0, round($v, 2));
        return fmod($v, 1.0) === 0.0
            ? (string) (int) $v
            : rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.');
    }

    public function fillFull(): void
    {
        $inv = $this->order()->invoice;
        if (!$inv) return;
        $out = max(0, round((float) $inv->total_amount - (float) $inv->total_paid, 2));
        $this->payAmount = $this->moneyInput($out);
        $this->resetErrorBag('payAmount');
    }

    public function fillDp(): void
    {
        $inv = $this->order()->invoice;
        if (!$inv) return;
        $dp = $this->dpPercentOf($inv);
        if ($dp <= 0) return;
        $out = max(0, round((float) $inv->total_amount - (float) $inv->total_paid, 2));
        $this->payAmount = $this->moneyInput(min($out, round((float) $inv->total_amount * $dp / 100, 2)));
        $this->resetErrorBag('payAmount');
    }

    public function closePay(): void
    {
        $this->payOpen = false;
        $this->payNote = '';
        $this->resetErrorBag(['payAmount', 'payNote']);
    }

    public function savePayment(): void
    {
        if (!$this->canManage() || !$this->hasPayments) return;

        $o = $this->order();
        $inv = $o->invoice;
        if (!$inv) { $this->closePay(); return; }

        if ((string) ($inv->payment_method ?? 'transfer') !== 'cash') {
            $this->closePay();
            session()->flash('os_error', 'Pencatatan manual hanya untuk pembayaran tunai.');
            return;
        }

        $out = max(0, round((float) $inv->total_amount - (float) $inv->total_paid, 2));

        $this->validate([
            'payAmount' => 'required|numeric|min:0.01',
            'payNote' => 'nullable|string|max:255',
        ], [
            'payAmount.required' => 'Nominal pembayaran wajib diisi.',
            'payAmount.numeric' => 'Nominal harus berupa angka tanpa titik ribuan.',
            'payAmount.min' => 'Nominal minimal Rp 1.',
        ]);

        $amount = round((float) str_replace(',', '.', (string) $this->payAmount), 2);

        if ($amount <= 0) {
            $this->addError('payAmount', 'Nominal harus lebih dari nol.');
            return;
        }

        if ($amount > $out + 0.001) {
            $this->addError('payAmount', 'Nominal melebihi sisa tagihan. Maksimal Rp ' . number_format($out, 0, ',', '.') . '.');
            return;
        }

        if ($amount > $out) $amount = $out;

        try {
            DB::transaction(function () use ($inv, $o, $amount) {
                $fill = ['amount' => $amount];
                if ($this->has('payments', 'invoice_id')) $fill['invoice_id'] = $inv->id;
                if ($this->has('payments', 'order_id')) $fill['order_id'] = $o->id;
                if ($this->has('payments', 'user_id')) $fill['user_id'] = $o->user_id;
                if ($this->has('payments', 'payment_method')) $fill['payment_method'] = 'cash';
                if ($this->has('payments', 'method')) $fill['method'] = 'cash';
                if ($this->has('payments', 'status')) $fill['status'] = 'verified';
                if ($this->has('payments', 'notes')) $fill['notes'] = trim($this->payNote) ?: 'Penerimaan tunai oleh ' . (auth()->user()->name ?? 'staf');
                if ($this->has('payments', 'note')) $fill['note'] = trim($this->payNote) ?: 'Penerimaan tunai oleh ' . (auth()->user()->name ?? 'staf');
                if ($this->has('payments', 'verified_at')) $fill['verified_at'] = now();
                if ($this->has('payments', 'verified_by')) $fill['verified_by'] = auth()->id();
                if ($this->has('payments', 'created_at')) $fill['created_at'] = now();
                if ($this->has('payments', 'updated_at')) $fill['updated_at'] = now();

                DB::table('payments')->insert($fill);
                $this->recalcInvoice($inv->id);
            });
        } catch (\Throwable $e) {
            session()->flash('os_error', 'Gagal mencatat pembayaran: ' . $e->getMessage());
            return;
        }

        $this->closePay();
        $this->payTick++;
        session()->flash('os_success', 'Pembayaran tunai Rp ' . number_format($amount, 0, ',', '.') . ' tercatat dan langsung terverifikasi.');
    }

    public function verifyPayment($id): void
    {
        if (!$this->canManage() || !$this->hasPayments) return;

        $row = DB::table('payments')->find($id);
        if (!$row) return;

        $inv = Invoice::find((int) ($row->invoice_id ?? 0));
        if (!$inv || (int) $inv->order_id !== $this->orderId) return;

        $data = [];
        if ($this->has('payments', 'status')) $data['status'] = 'verified';
        if ($this->has('payments', 'verified_at')) $data['verified_at'] = now();
        if ($this->has('payments', 'verified_by')) $data['verified_by'] = auth()->id();
        if ($this->has('payments', 'updated_at')) $data['updated_at'] = now();

        DB::table('payments')->where('id', $id)->update($data);
        $this->recalcInvoice($inv->id);

        session()->flash('os_success', 'Pembayaran diverifikasi. Status tagihan diperbarui otomatis.');
    }

    public function rejectPayment($id): void
    {
        if (!$this->canManage() || !$this->hasPayments) return;

        $row = DB::table('payments')->find($id);
        if (!$row) return;
        $inv = Invoice::find((int) ($row->invoice_id ?? 0));
        if (!$inv || (int) $inv->order_id !== $this->orderId) return;

        $data = [];
        if ($this->has('payments', 'status')) $data['status'] = 'rejected';
        if ($this->has('payments', 'updated_at')) $data['updated_at'] = now();
        DB::table('payments')->where('id', $id)->update($data);

        $this->recalcInvoice($inv->id);
        session()->flash('os_success', 'Bukti pembayaran ditolak. Nominal tidak dihitung ke tagihan.');
    }

    private function recalcInvoice(int $invoiceId): void
    {
        $inv = Invoice::find($invoiceId);
        if (!$inv) return;

        $paid = 0.0;
        if ($this->hasPayments && $this->has('payments', 'invoice_id')) {
            $paid = (float) DB::table('payments')
                ->where('invoice_id', $invoiceId)
                ->where('status', 'verified')
                ->sum('amount');
        }

        $total = (float) $inv->total_amount;
        $status = $paid <= 0 ? 'unpaid' : ($paid + 0.01 >= $total ? 'paid' : 'partial');

        $fill = ['total_paid' => round($paid, 2)];
        if ($this->has('invoices', 'status')) $fill['status'] = $status;
        $inv->forceFill($fill)->save();

        $o = Order::find($inv->order_id);
        if ($o && $paid + 0.01 >= $total && in_array($o->status, ['waiting_invoice', 'unpaid'], true)) {
            $o->forceFill(['status' => 'paid'])->save();
            $this->logHistory($o, 'paid', 'Tagihan lunas. Pesanan siap masuk antrian produksi.');
        }
    }

    public function markCompleted(): void
    {
        if (!$this->canManage()) return;
        $o = Order::find($this->orderId);
        if (!$o || $o->status !== 'shipping') return;

        $o->forceFill(['status' => 'completed'])->save();
        $this->logHistory($o, 'completed', 'Pesanan diserahkan ke pelanggan dan dinyatakan selesai.');

        session()->flash('os_success', 'Pesanan ditandai selesai.');
    }

    public function cancelOrder(): void
    {
        if (!$this->canManage()) return;
        $o = Order::find($this->orderId);
        if (!$o || !in_array($o->status, ['waiting_invoice', 'unpaid'], true)) return;

        $o->forceFill(['status' => 'cancelled'])->save();
        $this->logHistory($o, 'cancelled', 'Pesanan dibatalkan sebelum produksi berjalan.');

        session()->flash('os_success', 'Pesanan dibatalkan. Tidak ada bahan yang terpakai.');
    }

    private function logHistory(Order $o, string $to, string $note): void
    {
        if (!$this->hasHistory) return;
        try {
            $cols = Schema::getColumnListing('order_histories');
            $map = [
                'order_id' => $o->id,
                'user_id' => auth()->id(),
                'changed_by' => auth()->id(),
                'to_status' => $to,
                'new_status' => $to,
                'status' => $to,
                'notes' => $note,
                'note' => $note,
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

    /* ================= DATA ================= */

    private function lineRows(Order $o): array
    {
        $cBase = $this->pick('order_items', ['base_price', 'base_subtotal', 'harga_dasar']);
        $cFee  = $this->pick('order_items', ['duration_fee', 'sla_fee', 'duration_cost', 'biaya_durasi']);
        $cMas  = $this->pick('order_items', ['master_price', 'master_subtotal', 'subtotal']);
        $cDisc = $this->pick('order_items', ['discount_amount', 'discount', 'diskon']);
        $cFin  = $this->pick('order_items', ['final_subtotal', 'final_price', 'total']);
        $cFile = $this->pick('order_items', ['design_file', 'design_files', 'design_path', 'file_design', 'files']);

        $rows = [];
        foreach ($o->items as $it) {
            $qty  = (int) ($it->quantity ?? 1);
            $area = (float) ($it->area_m2 ?? 0);
            $svc  = (float) ($it->service->price_per_m2 ?? 0);
            $mat  = 0.0;
            foreach (['price_per_m2', 'price', 'unit_price'] as $k) {
                $v = (float) ($it->material->{$k} ?? 0);
                if ($v > 0) { $mat = $v; break; }
            }
            $unit = $svc + $mat;
            $mult = (float) ($it->duration_multiplier ?? 1);
            if ($mult <= 0) $mult = 1;

            $base = $cBase ? (float) $it->{$cBase} : round($unit * $area, 2);
            $fee  = $cFee ? (float) $it->{$cFee} : round($base * ($mult - 1), 2);
            $master = $cMas ? (float) $it->{$cMas} : round($base + $fee, 2);
            $final  = $cFin ? (float) $it->{$cFin} : $master;
            $disc = $cDisc ? (float) $it->{$cDisc} : max(0, round($master - $final, 2));
            if ($cDisc && !$cFin) $final = round($master - $disc, 2);

            $rawFiles = $cFile ? ($it->{$cFile} ?? null) : null;
            $list = [];
            if (is_array($rawFiles)) {
                $list = $rawFiles;
            } elseif (is_string($rawFiles) && trim($rawFiles) !== '') {
                $decoded = json_decode($rawFiles, true);
                $list = is_array($decoded) ? $decoded : [$rawFiles];
            } elseif ($rawFiles instanceof \Illuminate\Support\Collection) {
                $list = $rawFiles->all();
            }

            $files = [];
            foreach ($list as $entry) {
                $path = '';
                $label = '';
                if (is_array($entry)) {
                    $path = (string) ($entry['path'] ?? $entry['file'] ?? $entry['url'] ?? $entry['filename'] ?? '');
                    $label = (string) ($entry['name'] ?? $entry['original_name'] ?? $entry['originalName'] ?? '');
                } elseif (is_string($entry)) {
                    $path = $entry;
                } elseif (is_object($entry)) {
                    $path = (string) ($entry->path ?? $entry->file ?? $entry->url ?? '');
                    $label = (string) ($entry->name ?? $entry->original_name ?? '');
                }
                $path = trim($path);
                if ($path === '') continue;

                $url = null;
                if (str_starts_with($path, 'http')) {
                    $url = $path;
                } else {
                    try {
                        if (Storage::disk('public')->exists($path)) $url = Storage::disk('public')->url($path);
                    } catch (\Throwable $e) {
                    }
                }
                $files[] = ['name' => $label !== '' ? $label : basename($path), 'url' => $url];
            }

            $rows[] = [
                'id' => (int) $it->id,
                'service' => (string) ($it->service->name ?? 'Layanan dihapus'),
                'material' => (string) ($it->material->name ?? 'Bahan dihapus'),
                'size' => (string) ($it->size->name ?? 'Custom'),
                'qty' => $qty,
                'area' => round($area, 2),
                'unit' => round($unit, 2),
                'svc_price' => round($svc, 2),
                'mat_price' => round($mat, 2),
                'sla_name' => (string) ($it->duration_name ?? 'Normal'),
                'sla_mult' => $mult,
                'sla_days' => (int) ($it->duration_days ?? 0),
                'base' => round($base, 2),
                'fee' => round($fee, 2),
                'master' => round($master, 2),
                'discount' => round($disc, 2),
                'final' => round($final, 2),
                'notes' => (string) ($it->notes ?? ''),
                'files' => $files,
            ];
        }

        return $rows;
    }

    private function paymentsFor(int $invoiceId): array
    {
        if (!$this->hasPayments || !$this->has('payments', 'invoice_id')) return [];

        $rows = DB::table('payments')->where('invoice_id', $invoiceId)->orderByDesc('id')->get();
        $out = [];
        foreach ($rows as $p) {
            $proof = '';
            foreach (['proof_path', 'proof_file', 'proof', 'receipt_path', 'bukti_transfer'] as $c) {
                if (isset($p->{$c}) && (string) $p->{$c} !== '') { $proof = (string) $p->{$c}; break; }
            }
            $url = null;
            if ($proof !== '') {
                if (str_starts_with($proof, 'http')) $url = $proof;
                else {
                    try {
                        if (Storage::disk('public')->exists($proof)) $url = Storage::disk('public')->url($proof);
                    } catch (\Throwable $e) {
                    }
                }
            }
            $out[] = [
                'id' => (int) $p->id,
                'amount' => (float) ($p->amount ?? 0),
                'status' => (string) ($p->status ?? 'pending'),
                'method' => (string) ($p->payment_method ?? 'transfer'),
                'notes' => (string) ($p->notes ?? ''),
                'proof' => $url,
                'created' => !empty($p->created_at) ? Carbon::parse($p->created_at)->format('d M Y, H:i') : '—',
                'verified' => !empty($p->verified_at) ? Carbon::parse($p->verified_at)->format('d M Y, H:i') : null,
            ];
        }
        return $out;
    }

    private function historyFor(int $orderId): array
    {
        if (!$this->hasHistory) return [];
        try {
            $rows = DB::table('order_histories')->where('order_id', $orderId)->orderByDesc('id')->limit(10)->get();
            $out = [];
            foreach ($rows as $h) {
                $to = (string) ($h->to_status ?? $h->new_status ?? $h->status ?? '');
                $note = (string) ($h->notes ?? $h->note ?? $h->description ?? '');
                $uid = (int) ($h->user_id ?? $h->changed_by ?? 0);
                $out[] = [
                    'to' => $to,
                    'note' => $note,
                    'by' => $uid ? (string) (DB::table('users')->where('id', $uid)->value('name') ?? 'Sistem') : 'Sistem',
                    'at' => !empty($h->created_at) ? Carbon::parse($h->created_at)->format('d M Y, H:i') : '—',
                ];
            }
            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function render()
    {
        $o = $this->order();
        $lines = $this->lineRows($o);

        $sum = ['base' => 0.0, 'fee' => 0.0, 'master' => 0.0, 'discount' => 0.0, 'final' => 0.0];
        foreach ($lines as $r) {
            $sum['base'] += $r['base'];
            $sum['fee'] += $r['fee'];
            $sum['master'] += $r['master'];
            $sum['discount'] += $r['discount'];
            $sum['final'] += $r['final'];
        }
        foreach ($sum as $k => $v) $sum[$k] = round($v, 2);

        $inv = $o->invoice;
        $ship = $inv && $this->has('invoices', 'shipping_cost')
            ? round((float) ($inv->shipping_cost ?? 0), 2)
            : round((float) ($this->shippingCost ?: 0), 2);
        $grand = round($sum['final'] + $ship, 2);

        $invoiceData = null;
        $payments = [];
        if ($inv) {
            $total = (float) $inv->total_amount;
            $paid = (float) $inv->total_paid;
            $out = max(0, round($total - $paid, 2));
            $token = (string) ($inv->payment_token ?? '');
            $payUrl = $token !== '' && Route::has('payment.show') ? route('payment.show', $token) : null;
            $due = $inv->due_date ? Carbon::parse($inv->due_date)->startOfDay() : null;
            $over = $due ? (int) floor(max(0, now()->timestamp - $due->copy()->endOfDay()->timestamp) / 86400) : 0;
            $dpPct = $this->dpPercentOf($inv);
            $scheme = (string) ($inv->payment_scheme ?? 'full');
            if ($scheme === 'installment' && $dpPct <= 0) $dpPct = 50;
            $dpAmount = $dpPct > 0 ? round($total * $dpPct / 100, 2) : 0;

            $invoiceData = [
                'id' => (int) $inv->id,
                'number' => (string) ($inv->invoice_number ?? $o->invoice_number),
                'total' => round($total, 2),
                'paid' => round($paid, 2),
                'outstanding' => $out,
                'percent' => $total > 0 ? min(100, (int) round($paid / $total * 100)) : 0,
                'status' => (string) ($inv->status ?? 'unpaid'),
                'method' => (string) ($inv->payment_method ?? 'transfer'),
                'scheme' => $scheme,
                'dp_percent' => $dpPct,
                'dp_amount' => $dpAmount,
                'dp_done' => $dpAmount > 0 ? $paid + 0.01 >= $dpAmount : true,
                'due' => $due?->format('d M Y'),
                'overdue' => $over,
                'url' => $payUrl,
                'created' => $inv->created_at?->format('d M Y, H:i'),
            ];
            $payments = $this->paymentsFor((int) $inv->id);
            $pendingCount = 0;
            foreach ($payments as $pp) {
                if ($pp['status'] === 'pending') $pendingCount++;
            }
        }

        $pdfRoute = null;
        foreach (['admin.invoices.pdf', 'admin.invoice.pdf', 'admin.orders.invoice.pdf'] as $r) {
            if (Route::has($r)) { $pdfRoute = $r; break; }
        }

        $wa = $this->waNorm($this->whatsapp);
        $brand = (string) config('app.brand.name', config('app.name', 'FC Murni Jaya'));
        $waMsg = 'Halo ' . ($o->user->name ?? 'Bapak/Ibu') . ', kami ' . $brand . '. Invoice ' . $o->invoice_number
            . ' sudah terbit dengan total Rp ' . number_format($invoiceData['total'] ?? $grand, 0, ',', '.') . '.'
            . (!empty($invoiceData['url']) ? ' Silakan selesaikan pembayaran melalui tautan berikut: ' . $invoiceData['url'] : '')
            . ' Terima kasih.';

        $statusMeta = [
            'waiting_invoice' => ['Menunggu Invoice', 'bg-amber-100 text-amber-800', 'amber', 0],
            'unpaid'          => ['Belum Dibayar', 'bg-red-100 text-red-700', 'red', 1],
            'paid'            => ['Siap Produksi', 'bg-signal-100 text-signal-700', 'signal', 2],
            'processing'      => ['Sedang Dicetak', 'bg-amber-100 text-amber-800', 'amber', 3],
            'qc'              => ['Quality Control', 'bg-violet-100 text-violet-700', 'violet', 4],
            'shipping'        => ['Siap Diserahkan', 'bg-sky-100 text-sky-700', 'sky', 5],
            'completed'       => ['Selesai', 'bg-brand-200 text-brand-900', 'brand', 6],
            'cancelled'       => ['Dibatalkan', 'bg-ice-300 text-gray-600', 'ice', -1],
        ];

        $deadline = $o->deadline_date ? Carbon::parse($o->deadline_date)->startOfDay() : null;
        $days = $deadline ? (int) now()->startOfDay()->diffInDays($deadline, false) : null;

        $previewGrand = round($sum['final'] + (float) ($this->shippingCost ?: 0), 2);
        $pendingCount = $pendingCount ?? 0;
        $isCashInvoice = $inv ? (string) ($inv->payment_method ?? 'transfer') === 'cash' : $this->paymentMethod === 'cash';

        return view('livewire.admin.order-show', [
            'o' => $o,
            'lines' => $lines,
            'sum' => $sum,
            'ship' => $ship,
            'grand' => $grand,
            'invoiceData' => $invoiceData,
            'payments' => $payments,
            'history' => $this->historyFor((int) $o->id),
            'statusMeta' => $statusMeta,
            'sm' => $statusMeta[$o->status] ?? ['Tidak Diketahui', 'bg-ice-200 text-brand-800', 'ice', 0],
            'canManage' => $this->canManage(),
            'hasPayments' => $this->hasPayments && $this->has('payments', 'invoice_id'),
            'pdfRoute' => $pdfRoute,
            'wa' => $wa,
            'waMsg' => $waMsg,
            'deadline' => $deadline?->format('d M Y'),
            'days' => $days,
            'shipLabel' => match ((string) ($o->shipping_method ?? 'pickup')) {
                'pickup' => 'Ambil di Toko',
                'instant' => 'Kurir Instan',
                default => 'Kurir Reguler',
            },
            'isPickup' => (string) ($o->shipping_method ?? 'pickup') === 'pickup',
            'previewGrand' => $previewGrand,
            'previewDp' => round($previewGrand * max(0, (float) $this->dpPercent) / 100, 2),
            'pendingCount' => $pendingCount,
            'isCashInvoice' => $isCashInvoice,
            'payCheckedAt' => $this->payCheckedAt !== '' ? $this->payCheckedAt : now()->format('H:i:s'),
            'payOutstanding' => $invoiceData['outstanding'] ?? 0,
            'payDpAmount' => $invoiceData['dp_amount'] ?? 0,
        ]);
    }
}