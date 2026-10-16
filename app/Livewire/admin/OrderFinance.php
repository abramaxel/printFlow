<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use Livewire\WithFileUploads;
use App\Mail\InvoiceEmail;


class OrderFinance extends Component
{
    public $order;
    public $order_id;
    
    // Properties untuk Invoice
    public $shipping_cost = 0;
    public $tax = 0;
    public $invoice_notes = '';
    
    // Properties untuk Verifikasi Payment
    public $payment_id;
    public $admin_notes = '';

    // Properties untuk Konfigurasi Pembayaran
    public $payment_method = 'transfer'; // cash atau transfer
    public $payment_scheme = 'full';     // full atau installment
    public $cash_amount = 0;
    public $cash_receipt; // Untuk upload file kuitansi
    public $dp_amount = 0;
    public $installment_current = 1;
    public $installment_total = 1;
    
    public $dp_percentage = 0;
    public $installment_count = 1;

    protected $rules = [
        'shipping_cost' => 'required|numeric|min:0',
        'tax' => 'required|numeric|min:0',
        'admin_notes' => 'nullable|string|max:500',
    ];

    public function mount($order)
    {
        $this->order = $order;
        $this->order_id = $order->id;
    }

    // ==========================================
    // LOGIKA INVOICE
    // ==========================================
    public function generateInvoice()
    {
        // Validasi dasar
        $this->validate([
            'shipping_cost' => 'required|numeric|min:0', 
            'tax' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer',
            'payment_scheme' => 'required|in:full,installment',
            'cash_receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Validasi Tunai: WAJIB isi nominal
        if ($this->payment_method === 'cash') {
            $this->validate([
                'cash_amount' => 'required|numeric|min:1',
            ]);
            
            $totalAmount = $this->order->items->sum('final_subtotal') + $this->shipping_cost + $this->tax;
            if ($this->cash_amount > $totalAmount) {
                $this->addError('cash_amount', 'Nominal tunai tidak boleh melebihi total tagihan.');
                return;
            }
        }

        // Validasi Cicilan: WAJIB isi DP dan jumlah cicilan
        if ($this->payment_scheme === 'installment') {
            $this->validate([
                'dp_percentage' => 'required|numeric|min:0|max:100',
                'installment_count' => 'required|integer|min:1|max:12',
            ]);

            // Cek apakah DP sudah diisi (tidak boleh 0 jika ingin cicilan)
            if ($this->dp_percentage == 0 && $this->installment_count == 1) {
                $this->addError('dp_percentage', 'Untuk skema cicilan, silakan isi DP minimal 1% atau ubah skema ke "Langsung Lunas".');
                return;
            }
        }

        $subtotal = $this->order->items->sum('final_subtotal');
        $total_amount = $subtotal + $this->shipping_cost + $this->tax;

        // Simpan File Kuitansi jika Tunai
        $receiptPath = null;
        if ($this->payment_method === 'cash' && $this->cash_receipt) {
            $receiptPath = $this->cash_receipt->store('receipts', 'public');
        }

        // Generate Payment Schedule jika Cicilan
        $paymentSchedule = null;
        $installmentNotes = null;
        
        if ($this->payment_scheme === 'installment') {
            $dpAmount = round($total_amount * ($this->dp_percentage / 100), 2);
            $remainingAmount = $total_amount - $dpAmount;
            $installmentAmount = round($remainingAmount / $this->installment_count, 2);

            // Buat array schedule
            $paymentSchedule = [];
            
            // DP (jika > 0)
            if ($dpAmount > 0) {
                $paymentSchedule[] = [
                    'type' => 'DP',
                    'amount' => $dpAmount,
                    'status' => 'pending',
                    'description' => 'Down Payment (' . $this->dp_percentage . '%)',
                ];
            }

            // Cicilan
            for ($i = 1; $i <= $this->installment_count; $i++) {
                $paymentSchedule[] = [
                    'type' => 'Cicilan',
                    'amount' => $installmentAmount,
                    'status' => 'pending',
                    'description' => 'Cicilan ke-' . $i . ' dari ' . $this->installment_count,
                ];
            }

            $installmentNotes = "DP: " . $this->dp_percentage . "% (Rp " . number_format($dpAmount, 0, ',', '.') . "), " .
                               $this->installment_count . "x Cicilan (Rp " . number_format($installmentAmount, 0, ',', '.') . "/cicilan)";
        }

        // Buat Invoice
        $invoice = $this->order->invoice()->updateOrCreate(
            ['order_id' => $this->order->id],
            [
                'subtotal' => $subtotal,
                'shipping_cost' => $this->shipping_cost,
                'tax' => $this->tax,
                'total_amount' => $total_amount,
                'total_paid' => 0,
                'status' => 'unpaid',
                'due_date' => now()->addDays(3),
                'payment_token' => \Illuminate\Support\Str::random(64),
                'payment_method' => $this->payment_method,
                'payment_scheme' => $this->payment_scheme,
                'cash_receipt' => $receiptPath,
                'installment_notes' => $installmentNotes,
                'payment_schedule' => $paymentSchedule,
            ]
        );

        // Logika Tunai: Langsung Verifikasi
        if ($this->payment_method === 'cash') {
            \App\Models\Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $this->order->user_id,
                'amount' => $this->cash_amount,
                'payment_method' => 'Tunai (Cash)',
                'proof_file' => [$receiptPath],
                'status' => 'verified',
                'verified_at' => now(),
            ]);

            $invoice->update([
                'total_paid' => $this->cash_amount,
                'status' => $this->cash_amount >= $total_amount ? 'paid' : 'unpaid',
                'paid_at' => $this->cash_amount >= $total_amount ? now() : null,
            ]);
            
            $this->order->update(['status' => $this->cash_amount >= $total_amount ? 'processing' : 'unpaid']);
            session()->flash('finance_success', 'Pembayaran Tunai sebesar Rp ' . number_format($this->cash_amount, 0, ',', '.') . ' berhasil dicatat!');
        } else {
            $this->order->update(['status' => 'unpaid']);
            session()->flash('finance_success', 'Invoice Transfer berhasil dibuat! Silakan kirim link ke customer.');
        }
    }

    public function sendInvoiceEmail()
    {
        if (!$this->order->invoice) return;

        Mail::to($this->order->user->email)->send(new InvoiceEmail($this->order->invoice));
        session()->flash('finance_success', 'Softcopy Invoice berhasil dikirim ke email customer!');
    }

    // ==========================================
    // LOGIKA VERIFIKASI PEMBAYARAN (SMART DP LOGIC)
    // ==========================================
    public function verifyPayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $invoice = $payment->invoice;

        // 1. Update Status Payment menjadi Verified
        $payment->update([
            'status' => 'verified',
            'admin_notes' => $this->admin_notes,
            'verified_at' => now(),
        ]);

        // 2. Hitung Total Uang yang Sudah Masuk
        $totalPaid = $invoice->payments()->where('status', 'verified')->sum('amount');

        // 3. Update Invoice dengan Total yang Sudah Dibayar
        $invoice->update(['total_paid' => $totalPaid]);

        // 4. 🔥 UPDATE STATUS DI PAYMENT_SCHEDULE
        if ($invoice->payment_schedule) {
            $schedule = $invoice->payment_schedule;
            
            // Cari pembayaran yang nominalnya sama dengan payment yang baru diverifikasi
            // dan statusnya masih pending, lalu ubah jadi verified
            foreach ($schedule as $index => $item) {
                if ($item['status'] === 'pending' && abs($item['amount'] - $payment->amount) < 0.01) {
                    $schedule[$index]['status'] = 'verified';
                    break; // Hanya update yang pertama yang cocok
                }
            }
            
            // Simpan kembali schedule yang sudah diupdate
            $invoice->update(['payment_schedule' => $schedule]);
        }

        // 5. Logika Pintar: DP atau Lunas?
        if ($totalPaid >= $invoice->total_amount) {
            // LUNAS
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            $invoice->order->update(['status' => 'processing']); 
            session()->flash('finance_success', 'Pembayaran LUNAS diverifikasi! Pesanan siap diproses/dikirim.');
        } else {
            // DP / CICILAN
            $invoice->update(['status' => 'unpaid']); 
            $invoice->order->update(['status' => 'processing']); 
            session()->flash('finance_success', 'DP/Cicilan diverifikasi! Sisa tagihan: Rp ' . number_format($invoice->total_amount - $totalPaid, 0, ',', '.'));
        }

        $this->admin_notes = '';
    }

    public function rejectPayment($paymentId)
    {
        $this->validate(['admin_notes' => 'required|string']);
        
        $payment = Payment::findOrFail($paymentId);
        $payment->update([
            'status' => 'rejected',
            'admin_notes' => $this->admin_notes,
        ]);

        $payment->invoice->update(['status' => 'unpaid']);
        $payment->invoice->order->update(['status' => 'unpaid']);

        session()->flash('finance_success', 'Pembayaran ditolak. Pelanggan diminta upload ulang.');
        $this->admin_notes = '';
    }

    public function render()
    {
        // Refresh relasi invoice dan payment terbaru
        $this->order = $this->order->fresh(['invoice.payments']);
        return view('livewire.admin.order-finance');
    }

        public function updatedPaymentScheme()
    {
        $this->dp_amount = 0;
        $this->installment_current = 1;
        $this->installment_total = 1;
    }
    
    public function updatedPaymentMethod()
    {
        $this->cash_amount = 0;
        $this->cash_receipt = null;
    }
}