<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Supplier;

#[Layout('components.layouts.admin')]
#[Title('Daftar Supplier')]
class Suppliers extends Component
{
    public $search = '';
    public $showForm = false;
    public $editingId = null;
    public $name = '', $contact_person = '', $phone = '', $email = '', $address = '', $notes = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'contact_person' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:30',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string|max:500',
        'notes' => 'nullable|string|max:500',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $suppliers = Supplier::when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount('purchaseReceipts')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.suppliers', ['suppliers' => $suppliers]);
    }

    public function openForm($id = null)
    {
        $this->resetValidation();
        if ($id) {
            $s = Supplier::findOrFail($id);
            $this->editingId = $s->id;
            $this->name = $s->name;
            $this->contact_person = $s->contact_person ?? '';
            $this->phone = $s->phone ?? '';
            $this->email = $s->email ?? '';
            $this->address = $s->address ?? '';
            $this->notes = $s->notes ?? '';
            $this->is_active = (bool) $s->is_active;
        } else {
            $this->editingId = null;
            $this->name = $this->contact_person = $this->phone = $this->email = $this->address = $this->notes = '';
            $this->is_active = true;
        }
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();
        Supplier::updateOrCreate(['id' => $this->editingId], [
            'name' => $this->name,
            'contact_person' => $this->contact_person ?: null,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'address' => $this->address ?: null,
            'notes' => $this->notes ?: null,
            'is_active' => $this->is_active,
        ]);
        $this->showForm = false;
        session()->flash('success', $this->editingId ? 'Supplier diperbarui.' : 'Supplier baru ditambahkan.');
    }

    public function delete($id)
    {
        $s = Supplier::findOrFail($id);
        if ($s->purchaseReceipts()->count() > 0) {
            session()->flash('warning', 'Supplier punya riwayat penerimaan. Nonaktifkan saja daripada dihapus.');
            return;
        }
        $s->delete();
        session()->flash('success', 'Supplier dihapus.');
    }
}