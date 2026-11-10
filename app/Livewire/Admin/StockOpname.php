<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
#[Title('Stock Opname')]
class StockOpname extends Component
{
    public $search = '';
    public $showForm = false;
    public $selectedMaterialId = null;
    public $physical_qty = 0;
    public $notes = '';

    protected $rules = [
        'selectedMaterialId' => 'required|exists:materials,id',
        'physical_qty'       => 'required|numeric|min:0',
        'notes'              => 'nullable|string|max:500',
    ];

    public function render()
    {
        $materials = Material::with('category')
            ->where('is_active', true)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get()
            ->map(function ($m) {
                $m->system_stock = (float)$m->stock;
                $m->available    = max(0, (float)$m->stock - (float)($m->stock_reserved ?? 0));
                return $m;
            });

        // Lookup map KONSTAN (aman tanpa bergantung relasi MaterialMovement)
        $materialNames = $materials->pluck('name', 'id');
        $userNames     = User::pluck('name', 'id');

        $opnameHistory = MaterialMovement::where('movement_type', 'adjustment')
            ->where('sub_type', 'opname_adjustment')
            ->latest('movement_date')
            ->limit(10)
            ->get();

        return view('livewire.admin.stock-opname', [
            'materials'     => $materials,
            'opnameHistory' => $opnameHistory,
            'materialNames' => $materialNames,
            'userNames'     => $userNames,
        ]);
    }

    public function openOpname($materialId)
    {
        $material = Material::findOrFail($materialId);
        $this->selectedMaterialId = $materialId;
        $this->physical_qty = (float)$material->stock; // default = stok sistem
        $this->notes = '';
        $this->resetValidation();
        $this->showForm = true;
    }

    public function saveOpname()
    {
        $this->validate();

        $material   = Material::findOrFail($this->selectedMaterialId);
        $systemQty  = (float)$material->stock;
        $physicalQty = (float)$this->physical_qty;
        $difference = $physicalQty - $systemQty;

        if (abs($difference) < 0.01) {
            session()->flash('info', "Stok {$material->name} sudah sesuai. Tidak perlu adjustment.");
            $this->showForm = false;
            return;
        }

        $unitPrice = (float)($material->price ?? 0);

        DB::transaction(function () use ($material, $systemQty, $physicalQty, $difference, $unitPrice) {
            // 1. Koreksi stok fisik ke nilai hasil hitung
            $material->update(['stock' => $physicalQty]);

            // 2. Audit trail: movement adjustment (WHO/WHEN/WHY tercatat)
            MaterialMovement::create([
                'material_id'    => $material->id,
                'user_id'        => auth()->id(),
                'movement_type'  => 'adjustment',
                'sub_type'       => 'opname_adjustment',
                'quantity'       => abs($difference),
                'unit'           => $material->unit ?? 'm2',
                'unit_cost'      => $unitPrice,
                'total_cost'     => abs($difference) * $unitPrice,
                'reference_type' => 'StockOpname',
                'reference_id'   => null,
                'notes'          => sprintf(
                    'Opname: Sistem=%.2f, Fisik=%.2f, Selisih=%s%.2f %s. %s',
                    $systemQty, $physicalQty,
                    $difference > 0 ? '+' : '', $difference,
                    $material->unit ?? 'm2',
                    $this->notes ?: 'Tanpa keterangan'
                ),
                'movement_date'  => now(),
            ]);
        });

        $dir = $difference > 0 ? 'NAIL (surplus)' : 'TURUN (defisit)';
        session()->flash('success', "✅ Opname {$material->name}: stok {$dir}. Selisih " . number_format(abs($difference), 2) . " {$material->unit}");
        $this->showForm = false;
    }

    public function cancelOpname()
    {
        $this->showForm = false;
        $this->resetValidation();
    }
}