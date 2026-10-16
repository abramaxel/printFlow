<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;

#[Layout('components.layouts.admin')]
#[Title('Daftar Pesanan')]
class Orders extends Component
{
    public $search = '';
    public $statusFilter = '';

    public function render()
    {
        // Ambil data pesanan beserta relasi User dan Invoice
        $orders = Order::with(['user', 'invoice'])
            ->where('invoice_number', 'like', '%' . $this->search . '%')
            ->orWhereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->get();

        return view('livewire.admin.orders', compact('orders'));
    }

    // Helper untuk warna badge status (Flowbite style)
    public function getStatusColor($status)
    {
        return match($status) {
            'pending', 'waiting_invoice' => 'bg-yellow-100 text-yellow-800',
            'unpaid', 'rejected' => 'bg-red-100 text-red-800',
            'paid', 'processing' => 'bg-blue-100 text-blue-800',
            'qc', 'shipping' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Helper untuk format status agar lebih enak dibaca
    public function formatStatus($status)
    {
        return str_replace('_', ' ', ucfirst($status));
    }
}