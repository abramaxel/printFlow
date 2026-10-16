<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Service;
use Illuminate\Support\Str;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Layanan')]
class Services extends Component
{
    // Properties untuk Form
    public $serviceId;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $price_per_m2 = 0;
    public $is_active = true;

    // Properties untuk UI
    public $showModal = false;
    public $isEditing = false;
    public $search = '';

    // Validasi
    protected $rules = [
        'name' => 'required|min:3',
        'price_per_m2' => 'required|numeric|min:0',
    ];

    public function render()
    {
        $services = Service::with('category')
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->get();

        return view('livewire.admin.services', compact('services'));
    }

    // Buka Modal untuk Tambah
    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    // Buka Modal untuk Edit
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $this->serviceId = $service->id;
        $this->name = $service->name;
        $this->slug = $service->slug;
        $this->description = $service->description;
        $this->price_per_m2 = $service->price_per_m2;
        $this->is_active = $service->is_active;
        
        $this->isEditing = true;
        $this->showModal = true;
    }

    // Simpan Data (Create / Update)
    public function save()
    {
        $this->validate();

        // Auto generate slug jika kosong
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price_per_m2' => $this->price_per_m2,
            'is_active' => $this->is_active,
            'category_id' => 1, // Default ke Kategori "Jenis Layanan"
        ];

        if ($this->isEditing) {
            Service::find($this->serviceId)->update($data);
            session()->flash('success', 'Layanan berhasil diperbarui!');
        } else {
            Service::create($data);
            session()->flash('success', 'Layanan baru berhasil ditambahkan!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    // Hapus Data
    public function delete($id)
    {
        Service::find($id)->delete();
        session()->flash('success', 'Layanan berhasil dihapus!');
    }

    // Reset Form
    private function resetForm()
    {
        $this->reset(['serviceId', 'name', 'slug', 'description', 'price_per_m2', 'is_active', 'isEditing']);
        $this->resetValidation();
    }

        // Method ini akan otomatis jalan setiap kali user mengetik di kolom 'name'
    public function updatedName($value)
    {
        // Hanya auto-generate jika slug masih kosong 
        // (Agar jika admin pernah mengedit slug secara manual, tidak tertimpa)
        if (empty($this->slug)) {
            $this->slug = \Illuminate\Support\Str::slug($value);
        }
    }
}