<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.customer')]
#[Title('Riwayat Pesanan')]
class CustomerHistory extends Component
{
    use WithPagination;

    public string $filter = 'all';
    public string $search = '';
    public string $sort = 'newest';

    public function mount()
    {
        $user = auth()->user();
        $internal = ['owner', 'super_admin', 'admin', 'cs', 'produksi', 'production'];
        if (in_array($user->role, $internal)) return redirect()->route('admin.dashboard');
        if ($user->role !== 'customer') abort(403, 'Akses ditolak.');
    }

    public function setFilter(string $f): void { $this->filter = $f; $this->resetPage(); }
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingSort(): void { $this->resetPage(); }

    public function render()
    {
        $userId = Auth::id();

        $row = Order::where('user_id', $userId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status IN ('waiting_invoice','unpaid','paid','processing','qc','shipping') THEN 1 ELSE 0 END) as active")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN status IN ('waiting_invoice','unpaid') THEN 1 ELSE 0 END) as pending")
            ->first();

        $summary = [
            'total'     => (int) ($row->total ?? 0),
            'active'    => (int) ($row->active ?? 0),
            'completed' => (int) ($row->completed ?? 0),
            'pending'   => (int) ($row->pending ?? 0),
            'paid'      => (float) Invoice::whereHas('order', fn($q) => $q->where('user_id', $userId))->sum('total_paid'),
        ];

        $orders = Order::where('user_id', $userId)
            ->with(['invoice', 'items.service'])
            ->when($this->search, fn($q) => $q->where('invoice_number', 'like', '%' . $this->search . '%'))
            ->when($this->filter === 'active', fn($q) => $q->whereIn('status', ['waiting_invoice', 'unpaid', 'paid', 'processing', 'qc', 'shipping']))
            ->when($this->filter === 'unpaid', fn($q) => $q->whereIn('status', ['waiting_invoice', 'unpaid']))
            ->when($this->filter === 'completed', fn($q) => $q->where('status', 'completed'))
            ->orderBy('created_at', $this->sort === 'oldest' ? 'asc' : 'desc')
            ->paginate(8);

        return view('livewire.customer.customer-history', [
            'orders'     => $orders,
            'summary'    => $summary,
            'statusMeta' => $this->statusMeta(),
            'wa'         => config('app.brand.whatsapp'),
        ]);
    }

    private function statusMeta(): array
    {
        return [
            'waiting_invoice' => [
                'label' => 'Menunggu Invoice', 'badge' => 'bg-ice-200 text-brand-800', 'step' => 0, 'icon' => 'invoice',
                'node' => 'bg-white border-2 border-ice-300 text-brand-400', 'bar' => 'from-ice-400 via-ice-300 to-transparent',
            ],
            'unpaid' => [
                'label' => 'Belum Dibayar', 'badge' => 'bg-red-100 text-red-700', 'step' => 0, 'icon' => 'alert',
                'node' => 'bg-red-50 border-2 border-red-300 text-red-600', 'bar' => 'from-red-500 via-red-300 to-transparent',
            ],
            'paid' => [
                'label' => 'Dibayar', 'badge' => 'bg-brand-100 text-brand-700', 'step' => 1, 'icon' => 'card',
                'node' => 'bg-brand-600 text-signal-300', 'bar' => 'from-brand-600 via-brand-400 to-transparent',
            ],
            'processing' => [
                'label' => 'Diproses', 'badge' => 'bg-brand-200 text-brand-800', 'step' => 2, 'icon' => 'printer',
                'node' => 'bg-brand-600 text-signal-300', 'bar' => 'from-brand-600 via-brand-400 to-transparent',
            ],
            'qc' => [
                'label' => 'Quality Control', 'badge' => 'bg-brand-300 text-brand-900', 'step' => 3, 'icon' => 'shield',
                'node' => 'bg-brand-600 text-signal-300', 'bar' => 'from-brand-600 via-brand-400 to-transparent',
            ],
            'shipping' => [
                'label' => 'Dikirim', 'badge' => 'bg-signal-200 text-signal-700', 'step' => 4, 'icon' => 'truck',
                'node' => 'bg-signal-400 text-brand-950', 'bar' => 'from-signal-500 via-signal-300 to-transparent',
            ],
            'completed' => [
                'label' => 'Selesai', 'badge' => 'bg-signal-400 text-brand-950', 'step' => 5, 'icon' => 'check',
                'node' => 'bg-signal-400 text-brand-950', 'bar' => 'from-signal-500 via-signal-300 to-transparent',
            ],
            'cancelled' => [
                'label' => 'Dibatalkan', 'badge' => 'bg-ice-300 text-gray-600', 'step' => -1, 'icon' => 'x',
                'node' => 'bg-ice-200 border-2 border-ice-300 text-gray-500', 'bar' => 'from-ice-400 via-ice-300 to-transparent',
            ],
        ];
    }
}