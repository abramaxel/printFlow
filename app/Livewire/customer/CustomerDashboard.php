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

    public string $search = '';
    public bool $urgentOnly = false;
    public ?int $detailOrderId = null;

    private const ACTIVE = ['waiting_invoice', 'unpaid', 'paid', 'processing', 'qc', 'shipping'];
    private const URGENT = ['waiting_invoice', 'unpaid'];

    public function mount()
    {
        $user = auth()->user();
        $adminRoles = ['owner', 'super_admin', 'admin', 'cs', 'produksi', 'production'];
        if (in_array($user->role, $adminRoles)) return redirect()->route('admin.dashboard');
        if ($user->role !== 'customer') abort(403, 'Akses ditolak. Anda bukan customer.');
    }

    public function toggleUrgent(): void { $this->urgentOnly = !$this->urgentOnly; $this->resetPage(); }
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
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status IN ('waiting_invoice','unpaid','paid','processing','qc','shipping') THEN 1 ELSE 0 END) as active")
            ->selectRaw("SUM(CASE WHEN status IN ('waiting_invoice','unpaid') THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN status IN ('completed','cancelled') THEN 1 ELSE 0 END) as archived")
            ->first();

        $stats = [
            'total'     => (int) ($row->total ?? 0),
            'active'    => (int) ($row->active ?? 0),
            'pending'   => (int) ($row->pending ?? 0),
            'completed' => (int) ($row->completed ?? 0),
            'archived'  => (int) ($row->archived ?? 0),
        ];

        $totalPaid = (float) Invoice::whereHas('order', fn($q) => $q->where('user_id', $userId))->sum('total_paid');

        // Dashboard HANYA menampilkan pesanan berjalan. Selesai/batal → halaman Riwayat.
        $orders = Order::where('user_id', $userId)
            ->with(['invoice', 'items.service'])
            ->whereIn('status', self::ACTIVE)
            ->when($this->urgentOnly, fn($q) => $q->whereIn('status', self::URGENT))
            ->when($this->search, fn($q) => $q->where('invoice_number', 'like', '%' . $this->search . '%'))
            ->orderByRaw("CASE WHEN status IN ('waiting_invoice','unpaid') THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(6);

        $detailOrder = $this->detailOrderId
            ? Order::where('user_id', $userId)->with(['invoice', 'items.service', 'items.material'])->find($this->detailOrderId)
            : null;

        return view('livewire.customer.customer-dashboard', [
            'stats'       => $stats,
            'totalPaid'   => $totalPaid,
            'orders'      => $orders,
            'detailOrder' => $detailOrder,
            'statusMeta'  => $this->statusMeta(),
            'contact'     => $this->contactInfo(),
        ]);
    }

    private function contactInfo(): array
    {
        $wa = preg_replace('/[^0-9]/', '', (string) (config('app.brand.whatsapp') ?: config('app.brand.phone') ?: ''));
        if ($wa !== '' && str_starts_with($wa, '0')) $wa = '62' . substr($wa, 1);

        return [
            'whatsapp' => $wa,
            'name'     => config('app.brand.name', 'FC Murni Jaya'),
            'address'  => (string) config('app.brand.address', ''),
            'hours'    => (string) config('app.brand.hours', 'Senin–Sabtu, 08.00–17.00'),
        ];
    }

    private function statusMeta(): array
    {
        return [
            'waiting_invoice' => ['label' => 'Menunggu Invoice', 'badge' => 'bg-ice-200 text-brand-800',      'chip' => 'bg-ice-100 text-brand-500',   'icon' => 'invoice', 'step' => 0],
            'unpaid'          => ['label' => 'Belum Dibayar',    'badge' => 'bg-red-100 text-red-700',        'chip' => 'bg-red-50 text-red-600',      'icon' => 'alert',   'step' => 0],
            'paid'            => ['label' => 'Dibayar',          'badge' => 'bg-brand-100 text-brand-700',    'chip' => 'bg-brand-50 text-brand-600',  'icon' => 'card',    'step' => 1],
            'processing'      => ['label' => 'Diproses',         'badge' => 'bg-brand-200 text-brand-800',    'chip' => 'bg-brand-50 text-brand-600',  'icon' => 'printer', 'step' => 2],
            'qc'              => ['label' => 'Quality Control',  'badge' => 'bg-brand-300 text-brand-900',    'chip' => 'bg-brand-50 text-brand-600',  'icon' => 'shield',  'step' => 3],
            'shipping'        => ['label' => 'Dikirim',          'badge' => 'bg-signal-200 text-signal-700',  'chip' => 'bg-signal-100 text-signal-600','icon' => 'truck',  'step' => 4],
            'completed'       => ['label' => 'Selesai',          'badge' => 'bg-signal-400 text-brand-950',   'chip' => 'bg-signal-100 text-signal-600','icon' => 'check',  'step' => 5],
            'cancelled'       => ['label' => 'Dibatalkan',       'badge' => 'bg-ice-300 text-gray-600',       'chip' => 'bg-ice-100 text-gray-500',    'icon' => 'x',       'step' => -1],
        ];
    }
}