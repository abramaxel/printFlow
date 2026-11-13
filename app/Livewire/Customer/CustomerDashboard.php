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
#[Title('Dashboard Pelanggan')]
class CustomerDashboard extends Component
{
    use WithPagination;

    public string $filter = 'all';
    public string $search = '';
    public ?int $detailOrderId = null;

    public function mount()
    {
        $user = auth()->user();
        $adminRoles = ['owner', 'super_admin', 'admin', 'cs', 'produksi', 'production'];
        if (in_array($user->role, $adminRoles)) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->role !== 'customer') {
            abort(403, 'Akses ditolak. Anda bukan customer.');
        }
    }

    public function setFilter(string $f): void { $this->filter = $f; $this->resetPage(); }
    public function updatingSearch(): void { $this->resetPage(); }

    public function openDetail($id): void
    {
        $this->detailOrderId = Order::where('user_id', Auth::id())->where('id', $id)->exists() ? (int) $id : null;
    }
    public function closeDetail(): void { $this->detailOrderId = null; }

    public function render()
    {
        $userId = Auth::id();

        $row = Order::where('user_id', $userId)
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw("SUM(CASE WHEN status IN ('waiting_invoice','unpaid','paid','processing','qc','shipping') THEN 1 ELSE 0 END) as active_orders")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders")
            ->selectRaw("SUM(CASE WHEN status IN ('waiting_invoice','unpaid') THEN 1 ELSE 0 END) as unpaid_orders")
            ->first();

        $stats = [
            'total' => (int) ($row->total_orders ?? 0),
            'active' => (int) ($row->active_orders ?? 0),
            'completed' => (int) ($row->completed_orders ?? 0),
            'unpaid' => (int) ($row->unpaid_orders ?? 0),
        ];

        $totalPaid = (float) Invoice::whereHas('order', fn($q) => $q->where('user_id', $userId))->sum('total_paid');

        $activeStatuses = ['waiting_invoice', 'unpaid', 'paid', 'processing', 'qc', 'shipping'];

        $orders = Order::where('user_id', $userId)
            ->with(['invoice', 'items.service'])
            ->when($this->search, fn($q) => $q->where('invoice_number', 'like', '%' . $this->search . '%'))
            ->when($this->filter === 'active', fn($q) => $q->whereIn('status', $activeStatuses))
            ->when($this->filter === 'unpaid', fn($q) => $q->whereIn('status', ['waiting_invoice', 'unpaid']))
            ->when($this->filter === 'completed', fn($q) => $q->where('status', 'completed'))
            ->latest()
            ->paginate(8);

        $detailOrder = $this->detailOrderId
            ? Order::where('user_id', $userId)->with(['invoice', 'items.service', 'items.material'])->find($this->detailOrderId)
            : null;

        return view('livewire.customer.customer-dashboard', [
            'stats' => $stats,
            'totalPaid' => $totalPaid,
            'orders' => $orders,
            'detailOrder' => $detailOrder,
            'statusMeta' => $this->statusMeta(),
            'contact' => $this->contactInfo(),
        ]);
    }

    private function contactInfo(): array
    {
        $wa = preg_replace('/[^0-9]/', '', (string) (config('app.brand.whatsapp') ?: config('app.brand.phone') ?: ''));
        if ($wa !== '' && str_starts_with($wa, '0')) {
            $wa = '62' . substr($wa, 1);
        }
        return [
            'whatsapp' => $wa,
            'name' => config('app.brand.name', 'PrintOrder'),
            'address' => (string) config('app.brand.address', ''),
            'hours' => (string) config('app.brand.hours', 'Senin–Sabtu, 08.00–17.00'),
        ];
    }

    private function statusMeta(): array
    {
        return [
            'waiting_invoice' => ['label' => 'Menunggu Invoice', 'badge' => 'bg-ice-200 text-brand-800',  'step' => 0],
            'unpaid'          => ['label' => 'Belum Dibayar',    'badge' => 'bg-red-100 text-red-700',    'step' => 0],
            'paid'            => ['label' => 'Dibayar',          'badge' => 'bg-brand-100 text-brand-700','step' => 1],
            'processing'      => ['label' => 'Diproses',         'badge' => 'bg-brand-200 text-brand-800','step' => 2],
            'qc'              => ['label' => 'Quality Control',  'badge' => 'bg-brand-300 text-brand-900','step' => 3],
            'shipping'        => ['label' => 'Dikirim',          'badge' => 'bg-signal-200 text-signal-700', 'step' => 4],
            'completed'       => ['label' => 'Selesai',          'badge' => 'bg-signal-400 text-brand-950',  'step' => 5],
            'cancelled'       => ['label' => 'Dibatalkan',       'badge' => 'bg-ice-300 text-gray-600',   'step' => -1],
        ];
    }
}