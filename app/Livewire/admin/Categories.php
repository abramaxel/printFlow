<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;

class Categories extends Component
{
    use WithPagination;

    public $name = '';
    public $description = '';
    public $categoryId; // Untuk edit
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
        'description' => 'nullable|string|max:500',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function store()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'slug' => \Illuminate\Support\Str::slug($this->name),
            'description' => $this->description,
            'is_active' => true,
        ]);

        session()->flash('success', '✅ Kategori berhasil ditambahkan!');
        $this->reset(['name', 'description']);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $this->categoryId,
            'description' => 'nullable|string|max:500',
        ]);

        $category = Category::findOrFail($this->categoryId);
        $category->update([
            'name' => $this->name,
            'slug' => \Illuminate\Support\Str::slug($this->name),
            'description' => $this->description,
        ]);

        session()->flash('success', '✅ Kategori berhasil diperbarui!');
        $this->cancelEdit();
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        
        // Cek apakah kategori masih dipakai
        if ($category->services()->count() > 0 || $category->materials()->count() > 0) {
            session()->flash('error', '❌ Tidak bisa hapus! Kategori masih memiliki layanan atau bahan.');
            return;
        }

        $category->delete();
        session()->flash('success', '🗑️ Kategori berhasil dihapus!');
    }

    public function cancelEdit()
    {
        $this->reset(['categoryId', 'name', 'description']);
    }

    public function render()
    {
        $categories = Category::where('name', 'like', '%' . $this->search . '%')
            ->withCount(['services', 'materials'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.categories', compact('categories'));
    }
}