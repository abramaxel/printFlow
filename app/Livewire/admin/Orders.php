<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Order;

#[Layout('components.layouts.admin')]
#[Title('Daftar Pesanan')]
class Orders extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        // Baca filter dari URL parameter (untuk link "Perlu Tindak Lanjut" di sidebar)
        $this->statusFilter = request('status', $this->statusFilter);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function setSortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $userRole = auth()->user()->role;

        $query = Order::with(['user', 'invoice', 'items.service']);

        //  FIX BUG: Group search dengan benar agar tidak mengacaukan filter status
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('invoice', function ($iq) {
                      $iq->where('tracking_number', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filter berdasarkan status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // 🔥 ROLE-BASED FILTERING: CS tidak perlu lihat order selesai/dibatalkan
        if (in_array($userRole, ['cs', 'admin'])) {
            $query->whereNotIn('status', ['completed', 'cancelled']);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $orders = $query->paginate(15);

        // Hitung jumlah per status untuk badge di sidebar (opsional, bisa dipindah ke dashboard)
        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('livewire.admin.orders', compact('orders', 'statusCounts', 'userRole'));
    }

    /**
     * Helper untuk warna badge status
     */
    public function getStatusColor($status)
    {
        return match ($status) {
            'waiting_invoice' => 'bg-gray-100 text-gray-700',
            'unpaid'          => 'bg-yellow-100 text-yellow-700',
            'paid'            => 'bg-blue-100 text-blue-700',
            'processing'      => 'bg-indigo-100 text-indigo-700',
            'qc'              => 'bg-purple-100 text-purple-700',
            'shipping'        => 'bg-cyan-100 text-cyan-700',
            'completed'       => 'bg-green-100 text-green-700',
            'cancelled'       => 'bg-red-100 text-red-700',
            'rejected'        => 'bg-red-100 text-red-700',
            default           => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Helper untuk format status (Title Case yang benar)
     */
    public function formatStatus($status)
    {
        return ucwords(str_replace('_', ' ', $status));
    }

    /**
     * Helper untuk ikon status
     */
    public function getStatusIcon($status)
    {
        return match ($status) {
            'waiting_invoice' => '📄',
            'unpaid'          => '⏳',
            'paid'            => '✅',
            'processing'      => '🔧',
            'qc'              => '🔍',
            'shipping'        => '🚚',
            'completed'       => '🎉',
            'cancelled'       => '❌',
            'rejected'        => '🚫',
            default           => '📦',
        };
    }
}