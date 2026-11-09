<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\PurchaseRequest;

#[Layout('components.layouts.admin')]
#[Title('Purchase Request')]
class PurchaseRequests extends Component
{
    public $statusFilter = '';

    public function render()
    {
        $prs = PurchaseRequest::with('material')
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'approved' THEN 1 ELSE 2 END")
            ->orderByRaw("CASE WHEN urgency = 'critical' THEN 0 ELSE 1 END")
            ->latest()
            ->get();

        return view('livewire.admin.purchase-requests', ['prs' => $prs]);
    }

    public function approve($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        if ($pr->status !== 'pending') {
            session()->flash('warning', 'PR sudah diproses sebelumnya.');
            return;
        }
        $pr->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        session()->flash('success', "PR {$pr->pr_number} disetujui. Saat barang tiba, lanjut ke menu Penerimaan Barang.");
    }

    public function reject($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        if ($pr->status !== 'pending') {
            session()->flash('warning', 'PR sudah diproses sebelumnya.');
            return;
        }
        $pr->update(['status' => 'cancelled', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        session()->flash('success', "PR {$pr->pr_number} dibatalkan.");
    }
}