<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Service;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
#[Title('Kelola Layanan')]
class Services extends Component
{
    public $search = '';
    
    // State Modal Form
    public $showForm = false;
    public $editingId = null;
    
    // Field Form
    public $name = '';
    public $slug = '';
    public $price_per_m2 = 0;
    public $description = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'price_per_m2' => 'required|numeric|min:0',
        'description' => 'nullable|string|max:1000',
        'is_active' => 'boolean',
    ];

    // Auto-generate slug saat nama diketik
    public function updatedName($value)
    {
        if (!$this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function render()
    {
        $services = Service::when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                      ->orWhere('slug', 'like', "%{$this->search}%");
            })
            ->orderBy('name')
            ->get();

        return view('livewire.admin.services', compact('services'));
    }

    public function openForm($id = null)
    {
        $this->resetValidation();
        
        if ($id) {
            $service = Service::findOrFail($id);
            $this->editingId = $id;
            $this->name = $service->name;
            $this->slug = $service->slug;
            $this->price_per_m2 = $service->price_per_m2 ?? 0;
            $this->description = $service->description ?? '';
            $this->is_active = (bool) $service->is_active;
        } else {
            $this->resetForm();
        }
        
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        // Pastikan slug unik jika menambah baru atau mengubah slug
        $slug = $this->slug ?: Str::slug($this->name);
        if (!$this->editingId || ($this->editingId && Service::where('slug', $slug)->where('id', '!=', $this->editingId)->exists())) {
            $baseSlug = $slug;
            $counter = 1;
            while (Service::where('slug', $slug)->where('id', '!=', $this->editingId)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        Service::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'slug' => $slug,
                'price_per_m2' => $this->price_per_m2,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('success', $this->editingId ? 'Layanan berhasil diperbarui!' : 'Layanan baru berhasil ditambahkan!');
        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Service::findOrFail($id)->delete();
        session()->flash('success', 'Layanan berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';
        $this->price_per_m2 = 0;
        $this->description = '';
        $this->is_active = true;
    }
}