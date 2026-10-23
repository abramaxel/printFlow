<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Material;
use App\Mail\InvoiceEmail;

class OrderFinance extends Component
{
    use WithFileUploads;

    public $order;
    public $order_id;

    // Invoice (PAJAK DIHAPUS — harga sudah include PPN)
    public $shipping_cost = 0;
    public $payment_method = 'transfer';   // cash | transfer
    public $payment_scheme = 'full';       // full | installment

    // Tunai
    public $cash_amount = 0;
    public $cash_receipt;

    // Cicilan
    public $dp_percentage = 0;
    public $installment_count = 1;

    // Verifikasi
    public $admin_notes = '';

    protected $rules = [
        'shipping_cost'     => 'required|numeric|min:0',
        'payment_method'    => 'required|in:cash,transfer',
        'payment_scheme'    => 'required|in:full,installment',
        'cash_amount'       => 'nullable|numeric|min:0',
        'cash_receipt'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'dp_percentage'     => 'nullable|numeric|min:0|max:100',
        'installment_count' => 'nullable|integer|min:1|max:12',
        'admin_notes'       => 'nullable|string|max:500',
    ];

    public function mount($order): void
    {
        $this->order = $order;
        $this->order_id = $order->id;

        if ($order->invoice) {
            $this->shipping_cost = (float) $order->invoice->shipping_cost;
        }
        if ($this->isPickup()) {
            $this->shipping_cost = 0;
        }
    }

    /* ============ HELPERS ============ */

    public function isPickup(): bool
    {
        return ($this->order->shipping_method ?? 'pickup') === 'pickup';
    }

    public function breakdown(): array
    {
        return Invoice::costBreakdown($this->order);
    }

    public function liveShipping(): float
    {
        return $this->isPickup() ? 0.0 : round((float) $this->shipping_cost, 2);
    }

    public function liveGrand(): float
    {
        return round($this->breakdown()['total_final'] + $this->liveShipping(), 2);
    }

    /** Invoice terkunci bila sudah ada uang masuk / sudah lunas. */
    public function isLocked(): bool
    {
        $inv = $this->order->invoice;
        return $inv && ((float) $inv->total_paid > 0 || $inv->status === 'paid');
    }

    public function updatedPaymentScheme(): void
    {
        $this->dp_percentage = 0;
        $this->installment_count = 1;
    }

    public function updatedPaymentMethod(): void
    {
        $this->cash_amount = 0;
        $this->cash_receipt = null;
    }

    /* ============ GENERATE INVOICE ============ */

    public function generateInvoice()
    {
        $this->validate([
            'shipping_cost'  => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer',
            'payment_scheme' => 'required|in:full,installment',
            'cash_receipt'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // KUNCI INVOICE: jangan timpa invoice yang sudah ber-uang-masuk
        if ($this->isLocked()) {
            $this->addError('shipping_cost', 'Invoice terkunci karena sudah ada pembayaran masuk. Tolak/batalkan pembayaran dulu untuk mengubah invoice.');
            return;
        }

        $bd = $this->breakdown();
        $subtotal = round($bd['total_final'], 2);
        $total_amount = round($subtotal + $this->liveShipping(), 2);

        if ($this->payment_method === 'cash') {
            $this->validate(['cash_amount' => 'required|numeric|min:1']);
            if ((float) $this->cash_amount > $total_amount) {
                $this->addError('cash_amount', 'Nominal tunai tidak boleh melebihi total tagihan.');
                return;
            }
        }

        if ($this->payment_scheme === 'installment') {
            $this->validate([
                'dp_percentage'     => 'required|numeric|min:1|max:100',
                'installment_count' => 'required|integer|min:1|max:12',
            ]);
        }

        $receiptPath = null;
        if ($this->payment_method === 'cash' && $this->cash_receipt) {
            $receiptPath = $this->cash_receipt->store('receipts', 'public');
        }

        $paymentSchedule = null;
        $installmentNotes = null;
        if ($this->payment_scheme === 'installment') {
            $dpAmount = round($total_amount * ($this->dp_percentage / 100), 2);
            $remaining = round($total_amount - $dpAmount, 2);
            $perInstallment = round($remaining / $this->installment_count, 2);

            $paymentSchedule = [];
            if ($dpAmount > 0) {
                $paymentSchedule[] = [
                    'type' => 'DP', 'amount' => $dpAmount, 'status' => 'pending',
                    'description' => 'Down Payment (' . rtrim(rtrim(number_format($this->dp_percentage, 1, '.', ''), '0'), '.') . '%)',
                ];
            }
            for ($i = 1; $i <= $this->installment_count; $i++) {
                $paymentSchedule[] = [
                    'type' => 'Cicilan', 'amount' => $perInstallment, 'status' => 'pending',
                    'description' => 'Cicilan ke-' . $i . ' dari ' . $this->installment_count,
                ];
            }
            $installmentNotes = 'DP ' . $this->dp_percentage . '% (Rp ' . number_format($dpAmount, 0, ',', '.') . '), '
                . $this->installment_count . 'x cicilan @ Rp ' . number_format($perInstallment, 0, ',', '.');
        }

        $invoice = $this->order->invoice()->updateOrCreate(
            ['order_id' => $this->order->id],
            [
                'subtotal'          => $subtotal,
                'shipping_cost'     => $this->liveShipping(),
                'total_amount'      => $total_amount,
                'total_paid'        => 0,
                'discount'          => $bd['total_discount'],   // diskon per-item, bukan 0
                'status'            => 'unpaid',
                'due_date'          => now()->addDays(3),
                'payment_token'     => Str::random(64),
                'payment_method'    => $this->payment_method,
                'payment_scheme'    => $this->payment_scheme,
                'cash_receipt'      => $receiptPath,
                'installment_notes' => $installmentNotes,
                'payment_schedule'  => $paymentSchedule,
            ]
        );

        if ($this->payment_method === 'cash') {
            Payment::create([
                'invoice_id'     => $invoice->id,
                'user_id'        => $this->order->user_id,
                'amount'         => (float) $this->cash_amount,
                'payment_method' => 'Tunai (Cash)',
                'proof_file'     => [$receiptPath],
                'status'         => 'verified',
                'verified_at'    => now(),
            ]);

            $lunas = (float) $this->cash_amount >= $total_amount;
            $invoice->update([
                'total_paid' => (float) $this->cash_amount,
                'status'     => $lunas ? 'paid' : 'unpaid',
                'paid_at'    => $lunas ? now() : null,
            ]);
            // Uang masuk => SPK terbit => antre produksi (BUKAN langsung processing)
            $this->order->update(['status' => 'paid']);
            $this->reserveStock($this->order);   // reserve sekali, saat uang pertama masuk

            session()->flash('finance_success', 'Pembayaran tunai Rp ' . number_format($this->cash_amount, 0, ',', '.') . ' tercatat. ' . ($lunas ? 'LUNAS.' : 'Sisa tagihan menunggu pelunasan.'));
        } else {
            $this->order->update(['status' => 'unpaid']);
            session()->flash('finance_success', 'Invoice transfer berhasil dibuat. Kirim link ke customer.');
        }
    }

    public function sendInvoiceEmail()
    {
        if (!$this->order->invoice) return;
        Mail::to($this->order->user->email)->send(new InvoiceEmail($this->order->invoice));
        session()->flash('finance_success', 'Softcopy Invoice berhasil dikirim ke email customer!');
    }

    /* ============ VERIFIKASI PEMBAYARAN ============ */

    public function verifyPayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $invoice = $payment->invoice;

        // Uang verified SEBELUM transaksi ini => kunci stok hanya sekali (tambal double-lock)
        $priorVerified = $invoice->payments()
            ->where('status', 'verified')
            ->where('id', '!=', $payment->id)
            ->count();

        $payment->update([
            'status'      => 'verified',
            'admin_notes' => $this->admin_notes,
            'verified_at' => now(),
        ]);

        $totalPaid = $invoice->payments()->where('status', 'verified')->sum('amount');
        $invoice->update(['total_paid' => $totalPaid]);

        if ($invoice->payment_schedule) {
            $schedule = $invoice->payment_schedule;
            foreach ($schedule as $i => $row) {
                if ($row['status'] === 'pending' && abs((float) $row['amount'] - (float) $payment->amount) < 1) {
                    $schedule[$i]['status'] = 'verified';
                    break;
                }
            }
            $invoice->update(['payment_schedule' => $schedule]);
        }

        if ($priorVerified === 0) {
            $this->reserveStock($invoice->order);
        }

        if ($totalPaid >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);
            $invoice->order->update(['status' => 'paid']);
            session()->flash('finance_success', 'Pembayaran LUNAS diverifikasi! SPK terbit, pesanan masuk antrean produksi.');
        } else {
            $invoice->update(['status' => 'unpaid']);
            $invoice->order->update(['status' => 'paid']);   // DP aman => SPK terbit (bukan processing)
            session()->flash('finance_success', 'DP/Cicilan diverifikasi! Sisa tagihan: Rp ' . number_format($invoice->total_amount - $totalPaid, 0, ',', '.'));
        }

        $this->admin_notes = '';
    }

    public function rejectPayment($paymentId)
    {
        $this->validate(['admin_notes' => 'required|string']);
        $payment = Payment::findOrFail($paymentId);
        $invoice = $payment->invoice;

        $payment->update(['status' => 'rejected', 'admin_notes' => $this->admin_notes]);

        $totalPaid = $invoice->payments()->where('status', 'verified')->sum('amount');
        $invoice->update([
            'total_paid' => $totalPaid,
            'status'     => $totalPaid >= $invoice->total_amount ? 'paid' : 'unpaid',
        ]);

        // Bila tak ada lagi uang verified => lepas kunci stok & kembalikan order ke unpaid
        if ((float) $totalPaid <= 0) {
            $this->releaseStock($invoice->order);
            $invoice->order->update(['status' => 'unpaid']);
        }

        session()->flash('finance_success', 'Pembayaran ditolak. Pelanggan diminta upload ulang.');
        $this->admin_notes = '';
    }

    /* ============ STOK (konsisten dgn produksi: pakai area_m2) ============ */

    private function reserveStock($order): void
    {
        foreach ($order->items as $item) {
            Material::where('id', $item->material_id)->increment('stock_reserved', $item->area_m2);
        }
    }

    private function releaseStock($order): void
    {
        foreach ($order->items as $item) {
            Material::where('id', $item->material_id)
                ->where('stock_reserved', '>=', $item->area_m2)
                ->decrement('stock_reserved', $item->area_m2);
        }
    }

    public function render()
    {
        // TANPA 'items.duration' => hindari RelationNotFound; snapshot cols ikut baris item
        $this->order = $this->order->fresh([
            'items.service', 'items.material', 'items.size', 'invoice.payments', 'user',
        ]);
        return view('livewire.admin.order-finance');
    }
}