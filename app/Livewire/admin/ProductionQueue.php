<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
#[Title('Antrian Produksi')]
class ProductionQueue extends Component
{
    public $search = '';
    public $stockFilter = '';

    public function render()
    {
        // Query dasar: hanya ambil order yang sedang di tahap produksi atau cetak
        $query = Order::with(['user', 'items.material', 'items.service', 'items.size'])
            ->whereIn('status', ['processing', 'printing'])
            ->latest();

        // Filter Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'));
            });
        }

        $orders = $query->get();

        // Filter Stok (Dilakukan di collection karena butuh kalkulasi relasi)
        if ($this->stockFilter === 'ready') {
            $orders = $orders->filter(function($order) {
                return $order->items->every(function($item) {
                    $available = max(0, ($item->material->stock ?? 0) - ($item->material->stock_reserved ?? 0));
                    return $available >= $item->area_m2;
                });
            });
        } elseif ($this->stockFilter === 'shortage') {
            $orders = $orders->filter(function($order) {
                return $order->items->contains(function($item) {
                    $available = max(0, ($item->material->stock ?? 0) - ($item->material->stock_reserved ?? 0));
                    return $available < $item->area_m2;
                });
            });
        }

        // Hitung Statistik untuk Summary Cards
        $allOrders = Order::with('items.material')->whereIn('status', ['processing', 'printing'])->get();
        $totalOrders = $allOrders->count();
        
        $readyOrders = $allOrders->filter(function($order) {
            return $order->items->every(function($item) {
                $available = max(0, ($item->material->stock ?? 0) - ($item->material->stock_reserved ?? 0));
                return $available >= $item->area_m2;
            });
        })->count();

        $shortageOrders = $totalOrders - $readyOrders;

        return view('livewire.admin.production-queue', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'readyOrders' => $readyOrders,
            'shortageOrders' => $shortageOrders,
        ]);
    }

    public function confirmMaterialTakeout($orderId)
    {
        $order = Order::with('items.material')->findOrFail($orderId);

        DB::transaction(function () use ($order) {
            // 1. Potong Stok Fisik di Gudang
            foreach ($order->items as $item) {
                if ($item->material) {
                    $item->material->decrement('stock', $item->area_m2);
                    // Jika Anda menggunakan sistem stock_reserved, kurangi juga reserved-nya
                    // $item->material->decrement('stock_reserved', $item->area_m2); 
                }
            }

            // 2. Ubah Status Order ke 'printing' (Sedang Dicetak)
            $order->update(['status' => 'printing']);

            // 3. Catat History
            OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'old_status' => 'processing',
                'new_status' => 'printing',
                'notes' => 'Bahan fisik telah diambil dari gudang dan masuk mesin cetak.'
            ]);
        });

        session()->flash('success', "Bahan untuk {$order->invoice_number} berhasil ditarik. Status berubah menjadi PRINTING.");
    }

    public function moveToQC($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $oldStatus = $order->status;
        $order->update(['status' => 'qc']);

        OrderHistory::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => 'qc',
            'notes' => 'Proses cetak selesai, masuk ke meja Quality Control.'
        ]);

        session()->flash('success', "Pesanan {$order->invoice_number} selesai dicetak dan masuk ke QC.");
    }
}