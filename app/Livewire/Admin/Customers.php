<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Order;
use App\Models\Invoice;

#[Layout('components.layouts.admin')]
#[Title('CRM Pelanggan')]
class Customers extends Component
{
    use WithPagination;

    public $search = '';
    public $filterTier = '';
    public $showNoteModal = false;
    public $editingUserId = null;
    public $noteContent = '';
    public $newTier = '';

    protected $queryString = ['search', 'filterTier'];

    public function render()
    {
        // Query yang lebih sederhana dan aman
        $query = User::where('role', 'customer');

        // Search
        if ($this->search) {
            $query->where(function ($sub) {
                $sub->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        // Filter Tier
        if ($this->filterTier) {
            $query->where('tier', $this->filterTier);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);

        // Hitung statistik terpisah (lebih aman)
        $stats = [
            'total' => User::where('role', 'customer')->count(),
            'vip' => User::where('role', 'customer')->where('tier', 'vip')->count(),
            'inactive' => User::where('role', 'customer')->where('tier', 'inactive')->count(),
        ];

        return view('livewire.admin.customers', [
            'customers' => $customers,
            'stats' => $stats,
        ]);
    }

    public function openNoteModal($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $this->editingUserId = $user->id;
            $this->noteContent = $user->internal_notes ?? '';
            $this->newTier = $user->tier ?? 'regular';
            $this->showNoteModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'User tidak ditemukan.');
        }
    }

    public function saveNoteAndTier()
    {
        $this->validate([
            'noteContent' => 'nullable|string|max:1000',
            'newTier' => 'required|in:regular,silver,gold,vip,inactive',
        ]);

        try {
            $user = User::findOrFail($this->editingUserId);
            $user->update([
                'internal_notes' => $this->noteContent,
                'tier' => $this->newTier,
            ]);

            $this->showNoteModal = false;
            session()->flash('success', "✅ Catatan dan status {$user->name} berhasil diperbarui.");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui data.');
        }
    }

    public function closeNoteModal()
    {
        $this->showNoteModal = false;
        $this->editingUserId = null;
        $this->noteContent = '';
        $this->newTier = 'regular';
    }
}