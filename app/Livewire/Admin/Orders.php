<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Layout('components.layouts.admin')]
#[Title('Daftar Pesanan')]
class Orders extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public bool $hasTracking = false;

    protected $queryString = [
        'search'        => ['except' => ''],
        'statusFilter'  => ['except' => ''],
        'sortBy'        => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount(): void
    {
        $this->statusFilter = (string) request('status', $this->statusFilter);
        $this->hasTracking  = Schema::hasColumn('orders', 'tracking_number');
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function setFilter(string $value): void
    {
        $this->statusFilter = $value;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function setSortBy($column): void
    {
        if (!in_array($column, ['created_at', 'invoice_number', 'status'], true)) return;

        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function groups(): array
    {
        return [
            ''            => ['label' => 'Semua',          'statuses' => null],
            'need_action' => ['label' => 'Perlu Tindakan', 'statuses' => ['waiting_invoice', 'unpaid']],
            'production'  => ['label' => 'Produksi',       'statuses' => ['paid', 'processing', 'qc']],
            'shipping'    => ['label' => 'Dikirim',        'statuses' => ['shipping']],
            'completed'   => ['label' => 'Selesai',        'statuses' => ['completed']],
            'cancelled'   => ['label' => 'Batal',          'statuses' => ['cancelled']],
        ];
    }

    public function render()
    {
        $userRole   = (string) auth()->user()->role;
        $restricted = in_array($userRole, ['cs', 'admin'], true);

        $sort = in_array($this->sortBy, ['created_at', 'invoice_number', 'status'], true) ? $this->sortBy : 'created_at';
        $dir  = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        $scope = function ($q) use ($restricted) {
            if ($restricted) $q->whereNotIn('status', ['completed', 'cancelled']);
            return $q;
        };

        $agg = $scope(Order::query())
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status IN ('waiting_invoice','unpaid') THEN 1 ELSE 0 END) as need_action")
            ->selectRaw("SUM(CASE WHEN status IN ('paid','processing','qc') THEN 1 ELSE 0 END) as production")
            ->selectRaw("SUM(CASE WHEN status = 'shipping' THEN 1 ELSE 0 END) as shipping")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled")
            ->first();

        $counts = [
            ''            => (int) ($agg->total ?? 0),
            'need_action' => (int) ($agg->need_action ?? 0),
            'production'  => (int) ($agg->production ?? 0),
            'shipping'    => (int) ($agg->shipping ?? 0),
            'completed'   => (int) ($agg->completed ?? 0),
            'cancelled'   => (int) ($agg->cancelled ?? 0),
        ];

        $query = Order::with(['user', 'invoice', 'items.service']);
        $scope($query);

        $term = trim((string) $this->search);
        if ($term !== '') {
            $like   = '%' . $term . '%';
            $lower  = '%' . mb_strtolower($term) . '%';
            $digits = preg_replace('/\D+/', '', $term) ?? '';
            $phoneExpr = DB::connection()->getDriverName() === 'pgsql'
                ? "regexp_replace(coalesce(phone,''), '[^0-9]', '', 'g')"
                : "coalesce(phone,'')";
            $hasTracking = $this->hasTracking;

            $query->where(function ($q) use ($like, $lower, $digits, $phoneExpr, $hasTracking) {
                $q->whereRaw('LOWER(invoice_number) LIKE ?', [$lower])
                  ->orWhereHas('user', function ($u) use ($lower, $digits, $phoneExpr) {
                      $u->whereRaw('LOWER(name) LIKE ?', [$lower])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$lower]);
                      if (strlen($digits) >= 4) {
                          $u->orWhereRaw($phoneExpr . ' LIKE ?', ['%' . $digits . '%']);
                      }
                  });
                if ($hasTracking) $q->orWhereRaw('LOWER(tracking_number) LIKE ?', [$lower]);
            });
        }

        $groups = $this->groups();
        if ($this->statusFilter !== '') {
            if (isset($groups[$this->statusFilter]) && $groups[$this->statusFilter]['statuses']) {
                $query->whereIn('status', $groups[$this->statusFilter]['statuses']);
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        $orders = $query->orderBy($sort, $dir)->orderBy('id', 'desc')->paginate(15);

        $revenue = (float) Invoice::whereHas('order', fn($q) => $scope($q))->sum('total_paid');

        return view('livewire.admin.orders', [
            'orders'     => $orders,
            'counts'     => $counts,
            'groups'     => $groups,
            'revenue'    => $revenue,
            'userRole'   => $userRole,
            'restricted' => $restricted,
            'statusCounts' => collect($counts),
        ]);
    }

    public function getStatusColor($status)
    {
        return match ($status) {
            'waiting_invoice' => 'bg-ice-200 text-brand-800',
            'unpaid'          => 'bg-red-100 text-red-700',
            'paid'            => 'bg-brand-100 text-brand-700',
            'processing'      => 'bg-brand-200 text-brand-800',
            'qc'              => 'bg-brand-300 text-brand-900',
            'shipping'        => 'bg-signal-200 text-signal-700',
            'completed'       => 'bg-signal-400 text-brand-950',
            'cancelled', 'rejected' => 'bg-ice-300 text-gray-600',
            default           => 'bg-ice-200 text-brand-800',
        };
    }

    public function formatStatus($status)
    {
        return match ($status) {
            'waiting_invoice' => 'Menunggu Invoice',
            'unpaid'          => 'Belum Dibayar',
            'paid'            => 'Dibayar',
            'processing'      => 'Diproses',
            'qc'              => 'Quality Control',
            'shipping'        => 'Dikirim',
            'completed'       => 'Selesai',
            'cancelled'       => 'Dibatalkan',
            'rejected'        => 'Ditolak',
            default           => ucwords(str_replace('_', ' ', (string) $status)),
        };
    }

    public function getStatusIcon($status)
    {
        return match ($status) {
            'waiting_invoice' => 'invoice',
            'unpaid'          => 'alert',
            'paid'            => 'card',
            'processing'      => 'printer',
            'qc'              => 'shield',
            'shipping'        => 'truck',
            'completed'       => 'check',
            'cancelled', 'rejected' => 'x',
            default           => 'box',
        };
    }
}