<?php
namespace App\Livewire\Admin;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class ActiveOrders extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        // Hanya ambil pesanan yang sedang aktif, urutkan berdasarkan deadline terdekat
        $orders = Order::with(['user', 'items.service'])
            ->whereIn('status', ['paid', 'processing', 'qc', 'shipping'])
            ->where(function($q) {
                if ($this->search) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'));
                }
            })
            ->orderBy('deadline_date', 'asc') // Deadline terdekat di atas
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.active-orders', compact('orders'));
    }
}