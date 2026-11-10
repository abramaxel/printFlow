<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Models\User;

#[Layout('components.layouts.admin')]
#[Title('Audit Trail Stok')]
class MaterialAuditTrail extends Component
{
    use WithPagination;

    public $materialId = '';
    public $movementType = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $search = '';

    protected $queryString = ['materialId', 'movementType', 'dateFrom', 'dateTo'];

    public function updatingMaterialId()   { $this->resetPage(); }
    public function updatingMovementType() { $this->resetPage(); }

    public function render()
    {
        $materials = Material::where('is_active', true)->orderBy('name')->get(['id', 'name', 'unit']);

        $baseQuery = MaterialMovement::query()
            ->when($this->materialId,  fn($q) => $q->where('material_id', $this->materialId))
            ->when($this->movementType, fn($q) => $q->where('movement_type', $this->movementType))
            ->when($this->dateFrom, fn($q) => $q->where('movement_date', '>=', $this->dateFrom . ' 00:00:00'))
            ->when($this->dateTo,   fn($q) => $q->where('movement_date', '<=', $this->dateTo . ' 23:59:59'))
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('notes', 'like', "%{$this->search}%")
                    ->orWhere('sub_type', 'like', "%{$this->search}%");
            }));

        $movements = (clone $baseQuery)->latest('movement_date')->paginate(25);

        // Lookup map KONSTAN (aman tanpa relasi)
        $materialNames = Material::pluck('name', 'id');
        $userNames     = User::pluck('name', 'id');

        // Statistik (3 query count konstan, hanya saat filter aktif)
        $stats = [
            'in'  => (clone $baseQuery)->where('movement_type', 'in')->count(),
            'out' => (clone $baseQuery)->where('movement_type', 'out')->count(),
            'adj' => (clone $baseQuery)->where('movement_type', 'adjustment')->count(),
        ];

        return view('livewire.admin.material-audit-trail', [
            'materials'     => $materials,
            'movements'     => $movements,
            'materialNames' => $materialNames,
            'userNames'     => $userNames,
            'stats'         => $stats,
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['materialId', 'movementType', 'dateFrom', 'dateTo', 'search']);
        $this->resetPage();
    }
}