<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Size;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Ukuran')]
class Sizes extends Component
{
    // Properties untuk Form
    public $sizeId;
    public $name = '';
    public $slug = '';
    public $width_cm = 0;
    public $height_cm = 0;
    public $area_m2 = 0;
    public $price_adjustment = 0;
    public $is_active = true;

    // Properties untuk UI
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

    // Validasi
    protected $rules = [
        'name' => 'required|min:2',
        'area_m2' => 'required|numeric|min:0',
    ];

    public function render()
    {
        $sizes = Size::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->get();

        return view('livewire.admin.sizes', compact('sizes'));
    }

    // Auto-generate slug saat mengetik nama
    public function updatedName($value)
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $size = Size::findOrFail($id);
        $this->sizeId = $size->id;
        $this->name = $size->name;
        $this->slug = $size->slug;
        $this->width_cm = $size->width_cm;
        $this->height_cm = $size->height_cm;
        $this->area_m2 = $size->area_m2;
        $this->price_adjustment = $size->price_adjustment;
        $this->is_active = $size->is_active;
        
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'width_cm' => $this->width_cm,
            'height_cm' => $this->height_cm,
            'area_m2' => $this->area_m2,
            'price_adjustment' => $this->price_adjustment,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            Size::find($this->sizeId)->update($data);
            session()->flash('success', 'Ukuran berhasil diperbarui!');
        } else {
            Size::create($data);
            session()->flash('success', 'Ukuran baru berhasil ditambahkan!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        Size::find($id)->delete();
        session()->flash('success', 'Ukuran berhasil dihapus!');
    }

    private function resetForm()
    {
        $this->reset(['sizeId', 'name', 'slug', 'width_cm', 'height_cm', 'area_m2', 'price_adjustment', 'is_active', 'isEditing']);
        $this->resetValidation();
    }
}