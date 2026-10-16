<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use App\Models\Category;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Bahan')]
class Materials extends Component
{
    // Properties untuk Form
    public $materialId;
    public $category_id = '';
    public $name = '';
    public $slug = '';
    public $description = '';
    public $price_per_m2 = 0;
    public $stock = 0;
    public $is_active = true;

    // Properties untuk UI
    public $showModal = false;
    public $isEditing = false;
    public $search = '';
    public $categories = [];

    // Validasi
    protected $rules = [
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|min:3',
        'price_per_m2' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
    ];

    public function mount()
    {
        // Ambil semua kategori untuk dropdown
        $this->categories = Category::where('is_active', true)->get();
    }

    public function render()
    {
        $materials = Material::with('category')
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->get();

        return view('livewire.admin.materials', compact('materials'));
    }

    // Auto-generate slug saat mengetik nama (Fitur Opsi 2)
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
        $material = Material::findOrFail($id);
        $this->materialId = $material->id;
        $this->category_id = $material->category_id;
        $this->name = $material->name;
        $this->slug = $material->slug;
        $this->description = $material->description;
        $this->price_per_m2 = $material->price_per_m2;
        $this->stock = $material->stock;
        $this->is_active = $material->is_active;
        
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
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price_per_m2' => $this->price_per_m2,
            'stock' => $this->stock,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            Material::find($this->materialId)->update($data);
            session()->flash('success', 'Bahan berhasil diperbarui!');
        } else {
            Material::create($data);
            session()->flash('success', 'Bahan baru berhasil ditambahkan!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        Material::find($id)->delete();
        session()->flash('success', 'Bahan berhasil dihapus!');
    }

    private function resetForm()
    {
        $this->reset(['materialId', 'category_id', 'name', 'slug', 'description', 'price_per_m2', 'stock', 'is_active', 'isEditing']);
        $this->resetValidation();
    }
}