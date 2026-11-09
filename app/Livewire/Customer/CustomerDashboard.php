<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.customer')]
#[Title('Dashboard Saya')]
class CustomerDashboard extends Component
{
    use WithFileUploads;

    public $uploadPaymentId;
    public $uploadAmount;
    public $uploadProof;
    public $uploadNotes = '';

    protected $rules = [
        'uploadProof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'uploadNotes' => 'nullable|string|max:200',
    ];

    public function mount()
    {
        // Pastikan hanya customer yang bisa akses
        if (auth()->user()->role !== 'customer') {
            abort(403, 'Akses ditolak.');
        }
    }

    public function openUploadModal($paymentId, $amount)
    {
        $this->uploadPaymentId = $paymentId;
        $this->uploadAmount = $amount;
        $this->uploadProof = null;
        $this->uploadNotes = '';
        $this->dispatch('open-upload-modal');
    }

    public function submitUpload()
    {
        $this->validate();

        $payment = Payment::findOrFail($this->uploadPaymentId);
        
        // Validasi: payment harus milik invoice customer ini
        if ($payment->invoice->order->user_id !== auth()->id()) {
            $this->addError('uploadProof', 'Anda tidak memiliki akses ke pembayaran ini.');
            return;
        }

        // Simpan file bukti
        $proofPath = $this->uploadProof->store('payment_proofs', 'public');

        // Update payment
        $payment->update([
            'proof_file' => [$proofPath],
            'payment_method' => 'Transfer Bank',
            'notes' => $this->uploadNotes,
            'status' => 'pending',
        ]);

        // Update invoice status
        $payment->invoice->update(['status' => 'unpaid']);
        $payment->invoice->order->update(['status' => 'unpaid']);

        session()->flash('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
        
        $this->uploadProof = null;
        $this->uploadNotes = '';
        $this->dispatch('close-upload-modal');
    }

    public function render()
    {
        $userId = auth()->id();

        // Riwayat pesanan customer
        $orders = Order::where('user_id', $userId)
            ->with(['invoice.payments', 'items.service'])
            ->latest()
            ->get();

        // Statistik
        $totalOrders = $orders->count();
        $activeOrders = $orders->whereIn('status', ['waiting_invoice', 'unpaid', 'paid', 'processing', 'qc', 'shipping'])->count();
        $completedOrders = $orders->where('status', 'completed')->count();
        $totalSpent = Payment::whereHas('invoice.order', fn($q) => $q->where('user_id', $userId))
            ->where('status', 'verified')
            ->sum('amount');

        // Pembayaran pending (perlu upload bukti)
        $pendingPayments = Payment::whereHas('invoice.order', fn($q) => $q->where('user_id', $userId))
            ->where('status', 'pending')
            ->with('invoice.order')
            ->latest()
            ->get();

        return view('livewire.customer.customer-dashboard', compact(
            'orders', 'totalOrders', 'activeOrders', 'completedOrders', 
            'totalSpent', 'pendingPayments'
        ));
    }
}