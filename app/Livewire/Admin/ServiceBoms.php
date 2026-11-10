<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Service;
use App\Models\ServiceBom;
use App\Models\ServiceBomItem;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
#[Title('Kelola BOM Layanan')]
class ServiceBoms extends Component
{
    public $services;
    public $selectedServiceId = null;
    public $boms = [];
    
    // Form State
    public $showForm = false;
    public $editingBomId = null;
    public $bomVersion = 1;
    public $bomNotes = '';
    public $bomItems = []; // Array dinamis untuk item

    public function mount()
    {
        $this->services = Service::orderBy('name')->get();
        $this->loadBoms();
    }

    public function loadBoms()
    {
        if ($this->selectedServiceId) {
            $this->boms = ServiceBom::with('items.material')
                ->where('service_id', $this->selectedServiceId)
                ->orderByDesc('version')
                ->get();
        } else {
            $this->boms = [];
        }
    }

    public function updatedSelectedServiceId()
    {
        $this->loadBoms();
    }

    public function openForm($bomId = null)
    {
        $this->resetValidation();
        if ($bomId) {
            $bom = ServiceBom::with('items')->findOrFail($bomId);
            $this->editingBomId = $bom->id;
            $this->selectedServiceId = $bom->service_id;
            $this->bomVersion = $bom->version;
            $this->bomNotes = $bom->notes ?? '';
            $this->bomItems = $bom->items->map(function ($item) {
                return [
                    'material_id' => $item->material_id,
                    'quantity_per_unit' => (float) $item->quantity_per_unit,
                    'unit' => $item->unit ?? 'm2',
                    'scrap_percentage' => (float) $item->scrap_percentage,
                    'is_primary' => (bool) $item->is_primary,
                ];
            })->toArray();
        } else {
            $this->editingBomId = null;
            $this->bomVersion = ($this->boms->max('version') ?? 0) + 1;
            $this->bomNotes = '';
            $this->bomItems = [['material_id' => '', 'quantity_per_unit' => 1, 'unit' => 'm2', 'scrap_percentage' => 0, 'is_primary' => true]];
        }
        $this->showForm = true;
    }

    public function addItemRow()
    {
        $this->bomItems[] = ['material_id' => '', 'quantity_per_unit' => 1, 'unit' => 'm2', 'scrap_percentage' => 0, 'is_primary' => false];
    }

    public function removeItemRow($index)
    {
        unset($this->bomItems[$index]);
        $this->bomItems = array_values($this->bomItems);
        if (empty($this->bomItems)) {
            $this->addItemRow();
        }
    }

    public function save()
    {
        $this->validate([
            'selectedServiceId' => 'required|exists:services,id',
            'bomVersion' => 'required|integer|min:1',
            'bomItems' => 'required|array|min:1',
            'bomItems.*.material_id' => 'required|exists:materials,id',
            'bomItems.*.quantity_per_unit' => 'required|numeric|min:0.01',
            'bomItems.*.scrap_percentage' => 'required|numeric|min:0|max:100',
        ]);

        DB::transaction(function () {
            $bom = ServiceBom::updateOrCreate(
                ['id' => $this->editingBomId],
                [
                    'service_id' => $this->selectedServiceId,
                    'version' => $this->bomVersion,
                    'notes' => $this->bomNotes,
                    'is_active' => true, // Default aktif saat dibuat/diedit
                ]
            );

            // Hapus item lama jika edit
            if ($this->editingBomId) {
                $bom->items()->delete();
            }

            // Simpan item baru
            foreach ($this->bomItems as $index => $itemData) {
                $material = Material::find($itemData['material_id']);
                ServiceBomItem::create([
                    'service_bom_id' => $bom->id,
                    'material_id' => $itemData['material_id'],
                    'quantity_per_unit' => $itemData['quantity_per_unit'],
                    'unit' => $itemData['unit'] ?? $material->unit,
                    'scrap_percentage' => $itemData['scrap_percentage'],
                    'is_primary' => $itemData['is_primary'],
                    'sort_order' => $index,
                ]);
            }
        });

        session()->flash('success', 'BOM Layanan berhasil disimpan!');
        $this->showForm = false;
        $this->loadBoms();
    }

    public function toggleActive($bomId)
    {
        $bom = ServiceBom::findOrFail($bomId);
        // Jika mengaktifkan, nonaktifkan versi lain agar tidak konflik di MRP
        if (!$bom->is_active) {
            ServiceBom::where('service_id', $bom->service_id)->update(['is_active' => false]);
        }
        $bom->update(['is_active' => !$bom->is_active]);
        $this->loadBoms();
    }

    public function render()
    {
        return view('livewire.admin.service-boms', [
            'materials' => Material::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}