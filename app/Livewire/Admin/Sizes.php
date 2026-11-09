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
    // Properties untuk UI & Modal (DISESAMAKAN DENGAN BLADE)
    public $search = '';
    public $showForm = false; 
    public $editingId = null;

    // Properties untuk Form
    public $name = '';
    public $slug = '';
    public $width_cm = 0;
    public $height_cm = 0;
    public $area_m2 = 0;
    public $price_adjustment = 0;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'slug' => 'nullable|string|max:100',
        'width_cm' => 'nullable|numeric|min:0',
        'height_cm' => 'nullable|numeric|min:0',
        'area_m2' => 'required|numeric|min:0',
        'price_adjustment' => 'nullable|numeric',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $sizes = Size::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.sizes', compact('sizes'));
    }

    // Auto-generate slug saat mengetik nama
    public function updatedName($value)
    {
        if (!$this->editingId && empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    // Method untuk membuka modal (Tambah / Edit)
    public function openForm($id = null)
    {
        $this->resetValidation();
        
        if ($id) {
            $size = Size::findOrFail($id);
            $this->editingId = $id;
            $this->name = $size->name;
            $this->slug = $size->slug;
            $this->width_cm = $size->width_cm ?? 0;
            $this->height_cm = $size->height_cm ?? 0;
            $this->area_m2 = $size->area_m2;
            $this->price_adjustment = $size->price_adjustment ?? 0;
            $this->is_active = (bool) $size->is_active;
        } else {
            $this->resetForm();
        }
        
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        // Pastikan slug unik jika menambah baru atau mengubah slug
        $finalSlug = $this->slug ?: Str::slug($this->name);
        if (!$this->editingId || ($this->editingId && Size::where('slug', $finalSlug)->where('id', '!=', $this->editingId)->exists())) {
            $baseSlug = $finalSlug;
            $counter = 1;
            while (Size::where('slug', $finalSlug)->where('id', '!=', $this->editingId)->exists()) {
                $finalSlug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        Size::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'slug' => $finalSlug,
                'width_cm' => $this->width_cm,
                'height_cm' => $this->height_cm,
                'area_m2' => $this->area_m2,
                'price_adjustment' => $this->price_adjustment,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('success', $this->editingId ? 'Ukuran berhasil diperbarui!' : 'Ukuran baru berhasil ditambahkan!');
        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Size::findOrFail($id)->delete();
        session()->flash('success', 'Ukuran berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';
        $this->width_cm = 0;
        $this->height_cm = 0;
        $this->area_m2 = 0;
        $this->price_adjustment = 0;
        $this->is_active = true;
    }
}