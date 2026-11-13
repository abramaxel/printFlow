<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\Material;
use App\Models\User;
use App\Models\Supplier;

class GlobalSearch extends Component
{
    public string $q = '';

    public function render()
    {
        $role = auth()->user()->role ?? '';
        $warehouse = in_array($role, ['owner', 'super_admin', 'produksi', 'production']);

        $results = [
            'orders'    => collect(),
            'customers' => collect(),
            'materials' => collect(),
            'suppliers' => collect(),
        ];

        $term = trim($this->q);
        $open = mb_strlen($term) >= 2;

        if ($open) {
            $like = '%' . $term . '%';

            $results['orders'] = Order::with('user')
                ->where('invoice_number', 'like', $like)
                ->latest()->limit(5)->get();

            $results['customers'] = User::where('role', 'customer')
                ->where(fn($s) => $s->where('name', 'like', $like)->orWhere('email', 'like', $like))
                ->limit(4)->get();

            if ($warehouse) {
                $results['materials'] = Material::where('name', 'like', $like)->limit(4)->get();
                $results['suppliers'] = Supplier::where('name', 'like', $like)->limit(3)->get();
            }
        }

        return view('livewire.admin.global-search', [
            'results' => $results,
            'open'    => $open,
            'term'    => $term,
        ]);
    }
}