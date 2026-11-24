<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PaymentController extends Controller
{
    private array $pc = [];
    private array $ic = [];

    private function bootSchema(): void
    {
        $this->pc = Schema::hasTable('payments') ? Schema::getColumnListing('payments') : [];
        $this->ic = Schema::hasTable('invoices') ? Schema::getColumnListing('invoices') : [];
    }

    private function has(string $c): bool
    {
        return in_array($c, $this->pc, true);
    }

    private function canUpload(): bool
    {
        return !empty($this->pc) && $this->has('invoice_id');
    }

    private function findByToken(string $token): Invoice
    {
        $inv = Invoice::where('payment_token', $token)->first();
        if (!$inv) abort(404, 'Tautan pembayaran tidak ditemukan atau sudah kedaluwarsa.');
        return $inv;
    }

    private function dpPercentOf(Invoice $inv): float
    {
        foreach (['dp_percent', 'dp_percentage', 'down_payment_percent', 'dp_pct', 'dp'] as $c) {
            if (in_array($c, $this->ic, true)) {
                $v = (float) ($inv->{$c} ?? 0);
                if ($v > 0) return $v;
            }
        }

        // Skema cicilan tanpa porsi tercatat: pakai bawaan sistem 50%.
        // Lebih aman menagih DP daripada menagih penuh di luar kesepakatan.
        if ((string) ($inv->payment_scheme ?? 'full') === 'installment') {
            return 50.0;
        }

        return 0.0;
    }

    private function paymentDefaults(): array
    {
        try {
            $rows = DB::select("
                SELECT column_name, data_type, is_nullable, column_default
                FROM information_schema.columns
                WHERE table_name = 'payments'
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

    private function shop(): array
    {
        $d = [
            'shop_name' => (string) config('app.brand.name', config('app.name', 'FC Murni Jaya')),
            'shop_phone' => (string) config('app.brand.whatsapp', ''),
            'shop_email' => (string) config('app.brand.email', ''),
            'shop_address' => (string) config('app.brand.address', ''),
            'shop_hours' => (string) config('app.brand.hours', ''),
            'bank_name' => '',
            'bank_account' => '',
            'bank_holder' => '',
            'qris_image' => '',
            'qris_merchant' => '',
            'ewallet_provider' => '',
            'ewallet_number' => '',
            'ewallet_holder' => '',
            'invoice_footer' => 'Terima kasih atas kepercayaan Anda!',
            'payment_instructions' => 'Transfer sesuai nominal yang tertera agar verifikasi lebih cepat.',
        ];

        if (class_exists(\App\Support\ShopSettings::class)) {
            try {
                $s = \App\Support\ShopSettings::all();
                foreach ($d as $k => $v) {
                    if (isset($s[$k]) && (string) $s[$k] !== '') $d[$k] = (string) $s[$k];
                }
            } catch (\Throwable $e) {
            }
        }

        try {
            if (Schema::hasTable('shop_settings')) {
                $cols = Schema::getColumnListing('shop_settings');
                if (in_array('key', $cols, true) && in_array('value', $cols, true)) {
                    foreach (DB::table('shop_settings')->pluck('value', 'key') as $k => $v) {
                        if (array_key_exists($k, $d) && (string) $v !== '') $d[$k] = (string) $v;
                    }
                } else {
                    $row = DB::table('shop_settings')->orderBy('id')->first();
                    if ($row) {
                        foreach ((array) $row as $k => $v) {
                            if (array_key_exists($k, $d) && (string) $v !== '') $d[$k] = (string) $v;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        return $d;
    }

    private function publicUrl(string $path): ?string
    {
        if ($path === '') return null;
        if (str_starts_with($path, 'http')) return $path;
        try {
            if (Storage::disk('public')->exists($path)) return Storage::disk('public')->url($path);
        } catch (\Throwable $e) {
        }
        return null;
    }

    private function waNorm(string $raw): string
    {
        $d = preg_replace('/\D+/', '', $raw) ?? '';
        if ($d === '') return '';
        if (str_starts_with($d, '0')) $d = '62' . substr($d, 1);
        elseif (str_starts_with($d, '8')) $d = '62' . $d;
        return $d;
    }

    private function payments(int $invoiceId): array
    {
        if (!$this->canUpload()) return [];

        $rows = DB::table('payments')->where('invoice_id', $invoiceId)->orderByDesc('id')->limit(10)->get();
        $out = [];
        foreach ($rows as $p) {
            $proof = '';
            foreach (['proof_path', 'proof_file', 'proof', 'receipt_path', 'bukti_transfer'] as $c) {
                if (isset($p->{$c}) && (string) $p->{$c} !== '') { $proof = (string) $p->{$c}; break; }
            }
            $out[] = [
                'id' => (int) $p->id,
                'amount' => (float) ($p->amount ?? 0),
                'status' => (string) ($p->status ?? 'pending'),
                'method' => (string) ($p->payment_method ?? $p->method ?? 'transfer'),
                'note' => (string) ($p->notes ?? $p->note ?? ''),
                'proof' => $this->publicUrl($proof),
                'created' => !empty($p->created_at) ? Carbon::parse($p->created_at)->format('d M Y, H:i') : '—',
                'verified' => !empty($p->verified_at) ? Carbon::parse($p->verified_at)->format('d M Y, H:i') : null,
            ];
        }
        return $out;
    }

    private function stage(Invoice $inv): array
    {
        $total = (float) $inv->total_amount;
        $paid = (float) $inv->total_paid;
        $outstanding = max(0, round($total - $paid, 2));
        $scheme = (string) ($inv->payment_scheme ?? 'full');
        $dpPct = $this->dpPercentOf($inv);

        if ($scheme !== 'installment' || $dpPct <= 0) {
            return [
                'installment' => false, 'index' => 1, 'of' => 1,
                'label' => 'Pelunasan Penuh', 'short' => 'Lunas',
                'due' => $outstanding,
                'dp_percent' => 0, 'dp_amount' => 0, 'dp_paid' => 0, 'dp_done' => true,
                'rest_amount' => $outstanding,
            ];
        }

        $dpAmount = round($total * $dpPct / 100, 2);
        $dpPaid = min($paid, $dpAmount);
        $dpDone = $paid + 0.01 >= $dpAmount;

        if (!$dpDone) {
            return [
                'installment' => true, 'index' => 1, 'of' => 2,
                'label' => 'Uang Muka (DP)', 'short' => 'DP ' . rtrim(rtrim(number_format($dpPct, 1, '.', ''), '0'), '.') . '%',
                'due' => max(0, round($dpAmount - $dpPaid, 2)),
                'dp_percent' => $dpPct, 'dp_amount' => $dpAmount, 'dp_paid' => round($dpPaid, 2),
                'dp_done' => false, 'rest_amount' => max(0, round($total - $dpAmount, 2)),
            ];
        }

        return [
            'installment' => true, 'index' => 2, 'of' => 2,
            'label' => 'Pelunasan Sisa', 'short' => 'Pelunasan',
            'due' => $outstanding,
            'dp_percent' => $dpPct, 'dp_amount' => $dpAmount, 'dp_paid' => round($dpPaid, 2),
            'dp_done' => true, 'rest_amount' => $outstanding,
        ];
    }

    private function channel(Invoice $inv, array $shop): array
    {
        $method = (string) ($inv->payment_method ?? 'transfer');
        if (!in_array($method, ['transfer', 'qris', 'ewallet', 'cash'], true)) $method = 'transfer';

        $bankReady = trim($shop['bank_account']) !== '';
        $qrisUrl = $this->publicUrl((string) $shop['qris_image']);
        $walletReady = trim($shop['ewallet_number']) !== '';

        $base = [
            'method' => $method,
            'label' => match ($method) {
                'qris' => 'QRIS', 'ewallet' => 'E-Wallet', 'cash' => 'Tunai di Toko', default => 'Transfer Bank',
            },
            'bank_ready' => $bankReady,
            'qris_url' => $qrisUrl,
            'wallet_ready' => $walletReady,
            'ready' => false,
            'fallback' => false,
            'hint' => '',
        ];

        if ($method === 'cash') { $base['ready'] = true; return $base; }

        if ($method === 'qris' && $qrisUrl) {
            $base['ready'] = true;
            $base['hint'] = 'Scan pakai aplikasi bank / e-wallet, ketik nominal, lalu screenshot bukti.';
            return $base;
        }

        if ($method === 'ewallet' && $walletReady) {
            $base['ready'] = true;
            $base['hint'] = 'Kirim saldo ke nomor toko, lalu screenshot bukti transfer.';
            return $base;
        }

        if ($bankReady) {
            if ($method !== 'transfer') $base['fallback'] = true;
            $base['method'] = 'transfer';
            $base['label'] = 'Transfer Bank';
            $base['ready'] = true;
            $base['hint'] = 'Transfer persis nominal di atas, lalu unggah bukti transfer.';
            return $base;
        }

        return $base;
    }

    private function payload(Invoice $inv): array
    {
        $order = Order::with('user')->find($inv->order_id);
        $shop = $this->shop();

        $total = (float) $inv->total_amount;
        $paid = (float) $inv->total_paid;
        $out = max(0, round($total - $paid, 2));
        $ship = (float) ($inv->shipping_cost ?? 0);
        $disc = (float) ($inv->discount ?? 0);
        $subtotal = (float) ($inv->subtotal ?? max(0, $total - $ship));

        $payments = $this->payments((int) $inv->id);
        $pending = 0.0;
        $anyPending = false;
        $lastPending = null;
        foreach ($payments as $p) {
            if ($p['status'] === 'pending') {
                $pending += $p['amount'];
                $anyPending = true;
                if ($lastPending === null) $lastPending = $p;
            }
        }

        $due = $inv->due_date ? Carbon::parse($inv->due_date)->startOfDay() : null;
        $overdue = $due ? (int) floor(max(0, now()->timestamp - $due->copy()->endOfDay()->timestamp) / 86400) : 0;
        $leftDays = $due ? (int) ceil(max(0, $due->copy()->endOfDay()->timestamp - now()->timestamp) / 86400) : null;

        $status = (string) ($order->status ?? 'unpaid');
        $cancelled = in_array($status, ['cancelled', 'rejected'], true);
        $settled = $out <= 0;

        $stage = $this->stage($inv);
        $channel = $this->channel($inv, $shop);
        $isCash = $channel['method'] === 'cash';

        return [
            'inv' => $inv,
            'shop' => $shop,
            'token' => (string) $inv->payment_token,
            'numbers' => [
                'total' => round($total, 2),
                'paid' => round($paid, 2),
                'outstanding' => $out,
                'subtotal' => round($subtotal, 2),
                'shipping' => round($ship, 2),
                'discount' => round($disc, 2),
                'pending' => round($pending, 2),
                'percent' => $total > 0 ? min(100, (int) round($paid / $total * 100)) : 0,
            ],
            'stage' => $stage,
            'channel' => $channel,
            'payments' => $payments,
            'lastPending' => $lastPending,
            'anyPending' => $anyPending,
            'due' => $due?->format('d M Y'),
            'overdue' => $overdue,
            'leftDays' => $leftDays,
            'cancelled' => $cancelled,
            'settled' => $settled,
            'isCash' => $isCash,
            'formReady' => $this->canUpload() && !$cancelled && !$settled && !$isCash && $channel['ready'] && !$anyPending,
            'schemaReady' => $this->canUpload(),
            'wa' => $this->waNorm((string) $shop['shop_phone']),
            'methodLabel' => ['transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'ewallet' => 'E-Wallet', 'cash' => 'Tunai di Toko'],
            'invoiceNumber' => (string) ($inv->invoice_number ?? $order->invoice_number ?? 'INV-' . $inv->id),
            'customer' => (string) ($order->user->name ?? 'Pelanggan'),
            'issued' => $inv->created_at?->format('d M Y'),
        ];
    }

    public function show(string $token)
    {
        $this->bootSchema();
        return view('payment.magic', $this->payload($this->findByToken($token)));
    }

    public function upload(Request $request, string $token)
    {
        $this->bootSchema();
        $inv = $this->findByToken($token);
        $order = Order::find($inv->order_id);
        $back = redirect()->route('payment.show', $token);

        if (!$order || in_array($order->status, ['cancelled', 'rejected'], true)) {
            return $back->with('pl_error', 'Pesanan ini sudah dibatalkan.');
        }

        $data = $this->payload($inv);
        if (!$data['formReady']) {
            return $back->with('pl_error', 'Bukti untuk tahap ini sudah diterima atau tagihan sudah selesai. Tidak perlu mengirim ulang.');
        }

        $validated = $request->validate([
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'note' => 'nullable|string|max:255',
        ], [
            'proof.required' => 'Unggah foto atau scan bukti pembayaran.',
            'proof.file' => 'Bukti harus berupa file.',
            'proof.mimes' => 'Format yang diperbolehkan: JPG, PNG, atau PDF.',
            'proof.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $stage = $data['stage'];
        $channel = $data['channel'];
        $amount = round((float) $stage['due'], 2);

        try {
            $path = $request->file('proof')->store('payment-proofs', 'public');

            $fill = ['invoice_id' => $inv->id, 'amount' => $amount];
            if ($this->has('order_id')) $fill['order_id'] = $order->id;
            if ($this->has('user_id')) $fill['user_id'] = $order->user_id;
            if ($this->has('payment_method')) $fill['payment_method'] = $channel['method'];
            if ($this->has('method')) $fill['method'] = $channel['method'];
            if ($this->has('status')) $fill['status'] = 'pending';
            $note = trim((string) ($validated['note'] ?? ''));
            $tag = '[Tahap ' . $stage['index'] . '/' . $stage['of'] . ' · ' . $stage['label'] . ']';
            $note = trim($tag . ($note !== '' ? ' ' . $note : ''));
            if ($this->has('notes')) $fill['notes'] = $note;
            if ($this->has('note')) $fill['note'] = $note;
            foreach (['proof_path', 'proof_file', 'proof', 'receipt_path', 'bukti_transfer'] as $c) {
                if ($this->has($c)) { $fill[$c] = $path; break; }
            }
            if ($this->has('created_at')) $fill['created_at'] = now();
            if ($this->has('updated_at')) $fill['updated_at'] = now();

            DB::table('payments')->insert(array_merge($this->paymentDefaults(), $fill));
        } catch (\Throwable $e) {
            return $back->with('pl_error', 'Gagal mengunggah bukti: ' . $e->getMessage());
        }

        return $back->with('pl_success', 'Bukti ' . $stage['label'] . ' Rp ' . number_format($amount, 0, ',', '.') . ' terkirim. Menunggu verifikasi admin.');
    }
}