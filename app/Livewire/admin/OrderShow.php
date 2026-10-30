<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.admin')]
#[Title('Detail Pesanan')]
class OrderShow extends Component
{
    public $order;
    public $order_id;
    public $new_status;
    public $status_notes = '';
    public $customer_phone = '';
    public $tracking_number = '';
    public $allow_unpaid_shipping = false;
    public $redirect_to_next = false;

    public $available_statuses = [
        'waiting_invoice' => 'Menunggu Invoice',
        'unpaid' => 'Belum Dibayar',
        'paid' => 'SPK Terbit (Paid)',
        'processing' => 'Sedang Diproses',
        'qc' => 'Quality Control',
        'shipping' => 'Pengiriman',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'rejected' => 'Ditolak',
    ];

    protected $rules = [
        'new_status' => 'required|string',
        'status_notes' => 'nullable|string|max:500',
        'customer_phone' => 'nullable|string|max:20',
        'tracking_number' => 'nullable|string|max:100',
        'allow_unpaid_shipping' => 'boolean',
    ];

    public function mount($order)
    {
        // Hapus 'items.duration' dari array with() untuk mencegah crash
        $this->order = Order::with([
            'user', 
            'items.service', 
            'items.material', 
            'items.size', 
            // 'items.duration', // <-- Dihapus/dikomentari agar tidak error
            'invoice', 
            'histories.user'
        ])->findOrFail($order);
        
        $this->order_id = $this->order->id;
        $this->new_status = $this->order->status;
        $this->customer_phone = $this->order->user->phone ?? '';
        $this->tracking_number = $this->order->tracking_number ?? ''; // Pastikan ini ada untuk fitur resi
    }
    /**
     * GATEKEEPING: Validasi sebelum ubah status ke 'shipping'
     */
    private function validateShippingGate(): bool
    {
        if ($this->new_status !== 'shipping') {
            return true;
        }

        $invoice = $this->order->invoice;
        
        if (!$invoice) {
            $this->addError('new_status', 'Invoice belum dibuat. Buat invoice terlebih dahulu.');
            return false;
        }

        $isPaid = $invoice->status === 'paid' || 
                  (float) $invoice->total_paid >= (float) $invoice->total_amount;
        
        if (!$isPaid) {
            $userRole = auth()->user()->role;
            $isOwner = in_array($userRole, ['owner', 'super_admin']);
            
            if (!$isOwner || !$this->allow_unpaid_shipping) {
                $this->addError('new_status', 
                    '⚠️ PESANAN BELUM LUNAS! Pesanan tidak boleh dikirim sebelum lunas. ' .
                    'Owner dapat mencentang "Izinkan Kirim Tanpa Lunas" untuk override.');
                return false;
            }
            
            $this->status_notes = '[OVERRIDE OWNER] Dikirim tanpa lunas. ' . $this->status_notes;
        }

        // Validasi: harus ada nomor resi saat kirim
        if (empty($this->tracking_number)) {
            $this->addError('tracking_number', 
                'Nomor resi wajib diisi saat mengubah status ke Pengiriman.');
            return false;
        }

        return true;
    }

    public function updateStatus()
    {
        $this->validate();

        // 1. Gatekeeping (Validasi Pengiriman)
        if (!$this->validateShippingGate()) {
            return; // Early return: berhenti di sini jika validasi gagal (misal: belum lunas)
        }

        $oldStatus = $this->order->status;
        $newStatus = $this->new_status;

        // 2. Validasi transisi status yang logis
        $validTransitions = [
            'waiting_invoice' => ['unpaid', 'cancelled'],
            'unpaid' => ['paid', 'cancelled', 'rejected'],
            'paid' => ['processing', 'cancelled'],
            'processing' => ['qc', 'cancelled'],
            'qc' => ['shipping', 'cancelled'],
            'shipping' => ['completed'],
        ];

        if (!in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
            $this->addError('new_status', "Transisi dari '{$oldStatus}' ke '{$newStatus}' tidak valid.");
            return; // Early return jika transisi tidak valid
        }

        // 3. Siapkan data update
        $updateData = ['status' => $newStatus];
        
        // Jika status berubah ke shipping, simpan tracking number & timestamp
        if ($newStatus === 'shipping') {
            $updateData['tracking_number'] = $this->tracking_number;
            $updateData['shipped_at'] = now();
        }

        $this->order->update($updateData);

        // 4. Catat history
        $notes = $this->status_notes ?: "Status diubah dari {$oldStatus} ke {$newStatus}";
        if ($newStatus === 'shipping' && $this->tracking_number) {
            $notes .= ". Resi: {$this->tracking_number}";
        }

        OrderHistory::create([
            'order_id' => $this->order->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
        ]);

        // 5. Jika completed, kunci invoice permanen
        if ($newStatus === 'completed') {
            $this->order->invoice?->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            
            OrderHistory::create([
                'order_id' => $this->order->id,
                'user_id' => auth()->id(),
                'old_status' => $newStatus,
                'new_status' => 'completed',
                'notes' => '✅ PESANAN SELESAI. Invoice dikunci permanen.',
            ]);
        }

        // 6. Kirim WhatsApp jika status ke shipping
        if ($newStatus === 'shipping' && $this->tracking_number && $this->customer_phone) {
            $this->sendWhatsAppNotification($this->tracking_number);
        }

        // 7. Flash message sukses
        $successMessage = "Status pesanan berhasil diubah menjadi '{$newStatus}'.";
        if ($newStatus === 'shipping' && $this->tracking_number) {
            $successMessage .= ' Resi telah dikirim ke WhatsApp customer.';
        }
        session()->flash('success', $successMessage);
        
        // Reset form
        $this->status_notes = '';
        $this->allow_unpaid_shipping = false;
        
        // 8. ✅ INI RETURN YANG DIMAKSUD (Redirect ke Meja Kerja Produksi)
        // Setelah sukses update, lempar admin kembali ke daftar pesanan aktif
        return redirect()->route('admin.orders') // Ganti 'admin.orders' dengan 'admin.active-orders' jika sudah dibuat
            ->with('success', $successMessage);
    }

    /**
     * Kirim notifikasi WhatsApp ke customer
     */
    private function sendWhatsAppNotification($trackingNumber)
    {
        $phone = preg_replace('/[^0-9]/', '', $this->customer_phone);
        $order = $this->order;
        
        $message = "Halo {$order->user->name}!\n\n" .
            "Pesanan Anda {$order->invoice_number} telah dikirim! 🚚\n\n" .
            " Nomor Resi: *{$trackingNumber}*\n" .
            "🚚 Silakan lacak pengiriman Anda melalui website jasa pengiriman.\n\n" .
            "Terima kasih telah berorder di " . 
            config('app.brand.name', 'PrintOrder') . "!";

        try {
            // Contoh menggunakan Fonnte API (sesuaikan dengan provider Anda)
            Http::post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62'
            ])->throw();
            
            session()->flash('whatsapp_success', 'WhatsApp berhasil dikirim!');
        } catch (\Exception $e) {
            Log::error('WhatsApp failed: ' . $e->getMessage());
            session()->flash('whatsapp_error', 
                'Gagal mengirim WhatsApp. Silakan kirim manual ke ' . $this->customer_phone);
        }
    }

    public function updateCustomerPhone()
    {
        $this->validate(['customer_phone' => 'nullable|string|max:20']);
        $this->order->user->update(['phone' => $this->customer_phone]);
        session()->flash('success', 'Nomor WhatsApp customer berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.admin.order-show');
    }
}