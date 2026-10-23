<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.admin')]
#[Title('Detail Pesanan')]
class OrderShow extends Component
{
    public $order;
    public $order_id;
    
    // Properties untuk Update Status
    public $new_status = '';
    public $status_notes = '';
    
    // Property untuk No. WhatsApp Customer
    public $customer_phone = '';

    // Daftar status yang valid
    public $available_statuses = [
        'waiting_invoice' => 'Menunggu Invoice',
        'unpaid' => 'Belum Dibayar (Invoice Terkirim)',
        'paid' => 'Sudah Dibayar',
        'processing' => 'Sedang Diproduksi',
        'qc' => 'Quality Control (Pengecekan)',
        'shipping' => 'Sedang Dikirim / Siap Diambil',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'rejected' => 'Ditolak (Desain Error)',
    ];

    // HANYA ADA SATU METHOD MOUNT DI SINI
    public function mount($order)
    {
        // Ambil data order beserta semua relasinya
        $this->order = Order::with(['user', 'items.service', 'items.material', 'items.size', 'invoice', 'histories.user'])->findOrFail($order);
        $this->order_id = $this->order->id;
        $this->new_status = $this->order->status;
        
        // Inisialisasi no hp customer
        $this->customer_phone = $this->order->user->phone ?? ''; 
    }

    // Method untuk Update Status & Catat History
    public function updateStatus()
    {
        $this->validate([
            'new_status' => 'required|string',
            'status_notes' => 'nullable|string|max:500',
        ]);

        $old_status = $this->order->status;
        
        // 1. Update Status di Tabel Orders
        $this->order->update(['status' => $this->new_status]);

        // RELEASE STOCK jika status berubah menjadi 'cancelled' atau 'rejected'
        if (in_array($this->new_status, ['cancelled', 'rejected'])) {
        $this->releaseStock($this->order);
    }

        // 2. Catat ke Tabel Order Histories (Untuk Timeline Order Track)
        OrderHistory::create([
            'order_id' => $this->order->id,
            'user_id' => auth()->id(),
            'old_status' => $old_status,
            'new_status' => $this->new_status,
            'notes' => $this->status_notes,
        ]);

        session()->flash('success', 'Status pesanan berhasil diperbarui!');
        $this->status_notes = ''; // Reset catatan
        
        // Refresh data order agar UI update
        $this->order = $this->order->fresh(['histories.user']); 
    }

    private function releaseStock($order)
    {
        foreach ($order->items as $item) {
            // Mengurangi stock_reserved, pastikan tidak minus
            \App\Models\Material::where('id', $item->material_id)
                ->where('stock_reserved', '>=', $item->quantity)
                ->decrement('stock_reserved', $item->quantity);
        }
    }
    
    // Method untuk Update No. WhatsApp Customer
    public function updateCustomerPhone()
    {
        $this->validate([
            'customer_phone' => 'required|string|min:10|max:20',
        ]);

        $this->order->user->update(['phone' => $this->customer_phone]);
        session()->flash('success', 'Nomor WhatsApp pelanggan berhasil diperbarui!');
    }

    // Helper untuk download file desain
    // public function getDesignFileUrl($path)
    // {
    //     return $path ? Storage::url($path) : '#';
    // }

    public function render()
    {
        return view('livewire.admin.order-show');
    }
}