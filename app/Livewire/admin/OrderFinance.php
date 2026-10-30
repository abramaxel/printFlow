<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Material;
use App\Models\OrderHistory;
use App\Mail\InvoiceEmail;

class OrderFinance extends Component
{
    use WithFileUploads;

    public $order;
    public $order_id;

    // Invoice
    public $shipping_cost = 0;
    public $payment_method = 'transfer';   // cash | transfer
    public $payment_scheme = 'full';       // full | installment

    // Tunai
    public $cash_amount = 0;
    public $cash_receipt;

    // Cicilan
    public $dp_percentage = 0;
    public $installment_count = 1;

    // Verifikasi & Pembatalan
    public $admin_notes = '';
    public $cancel_reason = '';
    public $reopen_reason = '';

    protected $rules = [
        'shipping_cost'     => 'required|numeric|min:0',
        'payment_method'    => 'required|in:cash,transfer',
        'payment_scheme'    => 'required|in:full,installment',
        'cash_amount'       => 'nullable|numeric|min:0',
        'cash_receipt'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'dp_percentage'     => 'nullable|numeric|min:0|max:100',
        'installment_count' => 'nullable|integer|min:1|max:12',
        'admin_notes'       => 'nullable|string|max:500',
        'cancel_reason'     => 'nullable|string|min:10|max:500',
        'reopen_reason'     => 'nullable|string|min:10|max:500',
    ];

    public function mount($order): void
    {
        $this->order = $order;
        $this->order_id = $order->id;

        if ($order->invoice) {
            // Pre-fill form dengan data invoice yang sudah ada
            $this->shipping_cost = (float) $order->invoice->shipping_cost;
            $this->payment_method = $order->invoice->payment_method ?? 'transfer';
            $this->payment_scheme = $order->invoice->payment_scheme ?? 'full';
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
   /* ============ GENERATE / UPDATE INVOICE ============ */
    public function generateInvoice()
    {
        $this->validate([
            'shipping_cost'  => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer',
            'payment_scheme' => 'required|in:full,installment',
            'cash_receipt'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // 1. CEK KUNCI: Tidak boleh ubah jika sudah ada uang masuk
        if ($this->isLocked()) {
            $this->addError('shipping_cost', 'Invoice terkunci karena sudah ada pembayaran masuk. Tolak pembayaran tersebut terlebih dahulu untuk mengubah skema.');
            return;
        }

        // 2. CEK PENDING: Tidak boleh ubah skema jika ada pembayaran yang masih menunggu verifikasi
        if ($this->order->invoice && $this->order->invoice->payments()->where('status', 'pending')->count() > 0) {
            $this->addError('payment_scheme', 'Terdapat pembayaran yang masih status "Pending". Harap Verifikasi atau Tolak terlebih dahulu sebelum mengubah skema.');
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

        // 3. UPDATE OR CREATE: Ini akan meng-update invoice yang sudah ada, atau membuat baru jika belum ada
        $invoice = $this->order->invoice()->updateOrCreate(
            ['order_id' => $this->order->id],
            [
                'subtotal'          => $subtotal,
                'shipping_cost'     => $this->liveShipping(),
                'total_amount'      => $total_amount,
                'total_paid'        => 0, // Reset karena kita ubah skema sebelum ada uang masuk
                'discount'          => $bd['total_discount'],
                'status'            => 'unpaid',
                'due_date'          => now()->addDays(3),
                'payment_token'     => $this->order->invoice ? $this->order->invoice->payment_token : Str::random(64), // Pertahankan token lama jika ada
                'payment_method'    => $this->payment_method,
                'payment_scheme'    => $this->payment_scheme,
                'cash_receipt'      => $receiptPath,
                'installment_notes' => $installmentNotes,
                'payment_schedule'  => $paymentSchedule,
            ]
        );

        // Jika metode tunai, langsung proses pembayaran
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
            
            $this->order->update(['status' => 'paid']);
            $this->reserveStock($this->order);
            session()->flash('finance_success', 'Skema pembayaran diperbarui & Pembayaran tunai tercatat.');
        } else {
            $this->order->update(['status' => 'unpaid']);
            session()->flash('finance_success', 'Skema pembayaran berhasil diperbarui! Silakan kirim link baru ke customer.');
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

        // Cek apakah ini pembayaran pertama yang diverifikasi (untuk kunci stok)
        $priorVerified = $invoice->payments()
            ->where('status', 'verified')
            ->where('id', '!=', $payment->id)
            ->count();

        // Update status payment
        $payment->update([
            'status'      => 'verified',
            'admin_notes' => $this->admin_notes,
            'verified_at' => now(),
        ]);

        // Hitung ulang total yang sudah dibayar
        $totalPaid = $invoice->payments()->where('status', 'verified')->sum('amount');
        $remaining = $invoice->total_amount - $totalPaid;

        $invoice->update(['total_paid' => $totalPaid]);

        // SMART LOGIC: Update Payment Schedule
        if ($invoice->payment_schedule) {
            $schedule = $invoice->payment_schedule;
            $paymentAmount = (float) $payment->amount;
            
            foreach ($schedule as $i => $row) {
                if ($row['status'] === 'pending') {
                    // Jika nominal pembayaran cocok dengan jadwal, tandai lunas
                    if (abs((float) $row['amount'] - $paymentAmount) < 1) {
                        $schedule[$i]['status'] = 'verified';
                        break; 
                    }
                    // EARLY PAYOFF DETECTION: Jika customer bayar lebih besar dari jadwal (misal langsung lunasi sisa)
                    elseif ($paymentAmount >= (float) $row['amount'] && $remaining <= 0) {
                        $schedule[$i]['status'] = 'verified';
                        // Tandai semua sisa jadwal berikutnya sebagai verified juga karena sudah lunas
                        for ($j = $i + 1; $j < count($schedule); $j++) {
                            $schedule[$j]['status'] = 'verified';
                            $schedule[$j]['description'] .= ' (Dilunasi Dipercepat)';
                        }
                        break;
                    }
                }
            }
            $invoice->update(['payment_schedule' => $schedule]);
        }

        // Kunci stok hanya sekali saat uang pertama masuk
        if ($priorVerified === 0) {
            $this->reserveStock($invoice->order);
        }

        // TENTUKAN STATUS AKHIR
        if ($remaining <= 0) {
            // LUNAS SEPENUHNYA (Termasuk jika customer langsung melunasi sisa cicilan)
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);
            $invoice->order->update(['status' => 'paid']);
            session()->flash('finance_success', '✅ Pembayaran LUNAS diverifikasi! (Termasuk pelunasan dipercepat). SPK terbit, pesanan masuk antrean produksi.');
        } else {
            // MASIH ADA SISA
            $invoice->update(['status' => 'unpaid']);
            $invoice->order->update(['status' => 'paid']); // DP aman => SPK terbit
            session()->flash('finance_success', '✅ Cicilan/DP diverifikasi! Sisa tagihan: Rp ' . number_format($remaining, 0, ',', '.'));
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

        if ((float) $totalPaid <= 0) {
            $this->releaseStock($invoice->order);
            $invoice->order->update(['status' => 'unpaid']);
        }

        session()->flash('finance_success', 'Pembayaran ditolak. Pelanggan diminta upload ulang.');
        $this->admin_notes = '';
    }

    /* ============ STOK ============ */
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

    /* ============ PEMBATALAN INVOICE ============ */
    public function cancelInvoice()
    {
        $this->validate([
            'cancel_reason' => 'required|string|min:10|max:500',
        ]);

        $invoice = $this->order->invoice;
        
        if (!$invoice) {
            $this->addError('cancel_reason', 'Invoice belum dibuat.');
            return;
        }

        if ($this->order->status === 'completed') {
            $this->addError('cancel_reason', 'Pesanan sudah selesai dan tidak bisa dibatalkan.');
            return;
        }

        $hasPayment = (float) $invoice->total_paid > 0;
        $isOwner = in_array(auth()->user()->role, ['owner', 'super_admin']);
        
        if ($hasPayment && !$isOwner) {
            $this->addError('cancel_reason', 'Invoice sudah ada pembayaran masuk. Hanya Owner yang dapat membatalkan.');
            return;
        }

        $invoice->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
            'cancelled_reason' => $this->cancel_reason,
            'is_locked' => true,
        ]);

        $this->releaseStock($this->order);
        $this->order->update(['status' => 'cancelled']);

        OrderHistory::create([
            'order_id' => $this->order->id,
            'user_id' => auth()->id(),
            'old_status' => $this->order->getOriginal('status'),
            'new_status' => 'cancelled',
            'notes' => "Invoice dibatalkan. Alasan: {$this->cancel_reason}",
        ]);

        $this->cancel_reason = '';
        session()->flash('finance_success', 'Invoice berhasil dibatalkan. Stok telah dilepas.');
    }

    public function reopenInvoice()
    {
        $this->validate([
            'reopen_reason' => 'required|string|min:10|max:500',
        ]);

        $invoice = $this->order->invoice;
        
        if (!$invoice || $invoice->status !== 'cancelled') {
            $this->addError('reopen_reason', 'Invoice tidak dalam status dibatalkan.');
            return;
        }

        if (!in_array(auth()->user()->role, ['owner', 'super_admin'])) {
            $this->addError('reopen_reason', 'Hanya Owner yang dapat membuka kembali invoice.');
            return;
        }

        $invoice->update([
            'status' => 'unpaid',
            'cancelled_at' => null,
            'cancelled_by' => null,
            'cancelled_reason' => null,
            'is_locked' => false,
        ]);

        $this->order->update(['status' => 'unpaid']);

        OrderHistory::create([
            'order_id' => $this->order->id,
            'user_id' => auth()->id(),
            'old_status' => 'cancelled',
            'new_status' => 'unpaid',
            'notes' => "Invoice dibuka kembali oleh Owner. Alasan: {$this->reopen_reason}",
        ]);

        $this->reopen_reason = '';
        session()->flash('finance_success', 'Invoice berhasil dibuka kembali.');
    }

    public function render()
    {
        $this->order = $this->order->fresh([
            'items.service', 'items.material', 'items.size', 'invoice.payments', 'user', 'invoice.cancelledBy'
        ]);
        return view('livewire.admin.order-finance');
    }
}