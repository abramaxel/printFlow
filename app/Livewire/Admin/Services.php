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
    public $showForm = false;
    public $editingId = null;

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

    public function render()
    {
        $services = Service::when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")->orWhere('slug', 'like', "%{$this->search}%"))
            ->orderBy('name')->get();
        return view('livewire.admin.services', compact('services'));
    }

    public function updatedName($value)
    {
        if (!$this->editingId && empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function openForm($id = null)
    {
        $this->resetValidation();
        if ($id) {
            $s = Service::findOrFail($id);
            $this->editingId = $s->id;
            $this->name = $s->name;
            $this->slug = $s->slug;
            $this->price_per_m2 = $s->price_per_m2 ?? 0;
            $this->description = $s->description ?? '';
            $this->is_active = (bool)$s->is_active;
        } else {
            $this->resetForm();
        }
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();
        $finalSlug = $this->slug ?: Str::slug($this->name);
        if (Service::where('slug', $finalSlug)->where('id', '!=', $this->editingId)->exists()) {
            $base = $finalSlug; $i = 1;
            while (Service::where('slug', $finalSlug)->where('id', '!=', $this->editingId)->exists()) {
                $finalSlug = $base . '-' . $i++;
            }
        }
        Service::updateOrCreate(['id' => $this->editingId], [
            'name' => $this->name, 'slug' => $finalSlug,
            'price_per_m2' => $this->price_per_m2, 'description' => $this->description,
            'is_active' => $this->is_active,
        ]);
        session()->flash('success', $this->editingId ? 'Layanan diperbarui!' : 'Layanan baru ditambahkan!');
        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Service::findOrFail($id)->delete();
        session()->flash('success', 'Layanan dihapus.');
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = $this->slug = $this->description = '';
        $this->price_per_m2 = 0;
        $this->is_active = true;
    }
}