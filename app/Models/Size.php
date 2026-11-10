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
    public $search = '';
    public $showForm = false;
    public $editingId = null;

    public $name = '';
    public $slug = '';
    public $width_cm = 0;
    public $height_cm = 0;
    public $area_m2 = 0;
    public $price_adjustment = 0;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'area_m2' => 'required|numeric|min:0',
        'width_cm' => 'nullable|numeric|min:0',
        'height_cm' => 'nullable|numeric|min:0',
        'price_adjustment' => 'nullable|numeric',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $sizes = Size::where('name', 'like', '%' . $this->search . '%')->orderBy('name')->get();
        return view('livewire.admin.sizes', compact('sizes'));
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
            $size = Size::findOrFail($id);
            $this->editingId = $size->id;
            $this->name = $size->name;
            $this->slug = $size->slug;
            $this->width_cm = $size->width_cm ?? 0;
            $this->height_cm = $size->height_cm ?? 0;
            $this->area_m2 = $size->area_m2;
            $this->price_adjustment = $size->price_adjustment ?? 0;
            $this->is_active = (bool)$size->is_active;
        } else {
            $this->resetForm();
        }
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();
        $finalSlug = $this->slug ?: Str::slug($this->name);
        if (Size::where('slug', $finalSlug)->where('id', '!=', $this->editingId)->exists()) {
            $base = $finalSlug; $i = 1;
            while (Size::where('slug', $finalSlug)->where('id', '!=', $this->editingId)->exists()) {
                $finalSlug = $base . '-' . $i++;
            }
        }
        Size::updateOrCreate(['id' => $this->editingId], [
            'name' => $this->name, 'slug' => $finalSlug,
            'width_cm' => $this->width_cm, 'height_cm' => $this->height_cm,
            'area_m2' => $this->area_m2, 'price_adjustment' => $this->price_adjustment,
            'is_active' => $this->is_active,
        ]);
        session()->flash('success', $this->editingId ? 'Ukuran diperbarui!' : 'Ukuran baru ditambahkan!');
        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Size::findOrFail($id)->delete();
        session()->flash('success', 'Ukuran dihapus.');
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = $this->slug = '';
        $this->width_cm = $this->height_cm = $this->area_m2 = $this->price_adjustment = 0;
        $this->is_active = true;
    }
}