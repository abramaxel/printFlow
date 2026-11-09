<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Material;
use App\Models\Category;
use App\Models\MaterialMovement;
use App\Models\PurchaseRequest;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Bahan Baku')]
class Materials extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';

    // Form state
    public $showForm = false;
    public $editingId = null;
    public $name = '';
    public $category_id = '';
    public $unit = 'm2';
    public $price = 0; // Ini akan di-mapping ke unit_price di database
    public $stock = 0;
    public $threshold = 0;
    public $minimum_stock = 0;
    public $maximum_stock = 0;
    public $lead_time_days = 3;
    public $is_batch_tracked = false;
    public $shelf_life_days = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'unit' => 'required|string|max:20',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|numeric|min:0',
        'threshold' => 'required|numeric|min:0',
        'minimum_stock' => 'required|numeric|min:0',
        'maximum_stock' => 'nullable|numeric|min:0',
        'lead_time_days' => 'required|integer|min:1|max:365',
        'is_batch_tracked' => 'boolean',
        'shelf_life_days' => 'nullable|integer|min:1',
    ];

    public function render()
    {
        $query = Material::with('category')->orderBy('name');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
            });
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        $materials = $query->get()->map(function ($material) {
            $material->mrp = $this->calculateMrpMetrics($material);
            return $material;
        });

        if ($this->statusFilter) {
            $materials = $materials->filter(fn($m) => isset($m->mrp['status']) && $m->mrp['status'] === $this->statusFilter);
        }

        return view('livewire.admin.materials', [
            'materials' => $materials->values(),
            'categories' => Category::orderBy('name')->get(),
            'criticalCount' => $materials->filter(fn($m) => in_array($m->mrp['status'] ?? '', ['critical', 'out_of_stock']))->count(),
            'warningCount' => $materials->filter(fn($m) => ($m->mrp['status'] ?? '') === 'warning')->count(),
            'safeCount' => $materials->filter(fn($m) => ($m->mrp['status'] ?? '') === 'safe')->count(),
            'totalValue' => $materials->sum(fn($m) => (float)$m->stock * $this->getSafePrice($m)),
        ]);
    }

    /**
     * Helper: Ambil harga dengan aman (support unit_price & price_per_m2)
     */
    private function getSafePrice(Material $material): float
    {
        return (float) ($material->unit_price ?? $material->price_per_m2 ?? $material->price ?? 0);
    }

    /**
     * Hitung semua metrik MRP untuk satu material
     */
    private function calculateMrpMetrics(Material $material): array
    {
        try {
            $availableStock = max(0, (float)$material->stock - (float)($material->stock_reserved ?? 0));
            
            $pipelineDemand = (float)\App\Models\OrderItem::where('material_id', $material->id)
                ->whereHas('order', fn($q) => $q->whereIn('status', ['paid', 'processing']))
                ->sum('area_m2');
                
            $netAvailable = $availableStock - $pipelineDemand;
            
            $avgDailyUsage = (float)MaterialMovement::where('material_id', $material->id)
                ->where('movement_type', 'out')
                ->where('sub_type', 'production_issue')
                ->where('movement_date', '>=', now()->subDays(30))
                ->sum('quantity') / 30;
                
            $rop = (((float)($material->lead_time_days ?? 3)) * $avgDailyUsage) + ((float)($material->minimum_stock ?? 0));

            $status = 'safe';
            $urgency = 'normal';

            if ($netAvailable <= 0) {
                $status = 'out_of_stock';
                $urgency = 'critical';
            } elseif ($netAvailable <= ((float)($material->minimum_stock ?? 0))) {
                $status = 'critical';
                $urgency = 'critical';
            } elseif ($netAvailable <= $rop) {
                $status = 'warning';
                $urgency = 'warning';
            }

            // Hitung suggested qty
            $suggestedQty = 0;
            $price = $this->getSafePrice($material); // Gunakan harga yang benar
            
            if ($urgency !== 'normal') {
                // Logika sederhana: isi sampai maximum stock atau 2x minimum
                $target = $material->maximum_stock > 0 ? $material->maximum_stock : ($material->minimum_stock * 2);
                $suggestedQty = max(0, $target - $netAvailable);
            }

            return [
                'available_stock' => $availableStock,
                'pipeline_demand' => $pipelineDemand,
                'net_available' => $netAvailable,
                'avg_daily_usage' => $avgDailyUsage,
                'rop' => $rop,
                'status' => $status,
                'urgency' => $urgency,
                'suggested_qty' => ceil($suggestedQty),
                'estimated_cost' => ceil($suggestedQty) * $price, // Gunakan harga yang benar
                'needs_reorder' => $urgency !== 'normal',
            ];
        } catch (\Exception $e) {
            // Fallback jika ada error
            return [
                'available_stock' => 0, 'pipeline_demand' => 0, 'net_available' => 0,
                'avg_daily_usage' => 0, 'rop' => 0, 'status' => 'safe', 'urgency' => 'normal',
                'suggested_qty' => 0, 'estimated_cost' => 0, 'needs_reorder' => false,
            ];
        }
    }

    public function createPurchaseRequest($materialId)
    {
        $material = Material::findOrFail($materialId);
        $mrp = $this->calculateMrpMetrics($material);

        $existing = PurchaseRequest::where('material_id', $materialId)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            session()->flash('warning', "Sudah ada PR pending untuk {$material->name} ({$existing->pr_number}).");
            return;
        }

        PurchaseRequest::create([
            'pr_number' => PurchaseRequest::generatePrNumber(),
            'material_id' => $materialId,
            'requested_quantity' => $mrp['suggested_qty'] > 0 ? $mrp['suggested_qty'] : max(1, $material->minimum_stock ?? 0),
            'unit' => $material->unit ?? 'm2',
            'estimated_cost' => $mrp['estimated_cost'],
            'urgency' => $mrp['urgency'],
            'reason' => 'Manual PR from Materials page',
            'status' => 'pending',
            'requested_by' => auth()->id(),
            'needed_by_date' => now()->addDays($material->lead_time_days ?? 3)->toDateString(),
        ]);

        session()->flash('success', "✅ Purchase Request berhasil dibuat untuk {$material->name}!");
    }

    // === CRUD METHODS ===

    public function openForm($id = null)
    {
        $this->resetValidation();
        $this->resetErrorBag();

        if ($id) {
            $material = Material::findOrFail($id);
            $this->editingId = $id;
            $this->name = $material->name;
            $this->category_id = $material->category_id;
            $this->unit = $material->unit ?? 'm2';
            // Baca harga dari kolom yang benar
            $this->price = $this->getSafePrice($material);
            $this->stock = $material->stock;
            $this->threshold = $material->threshold;
            $this->minimum_stock = $material->minimum_stock ?? 0;
            $this->maximum_stock = $material->maximum_stock ?? 0;
            $this->lead_time_days = $material->lead_time_days ?? 3;
            $this->is_batch_tracked = $material->is_batch_tracked ?? false;
            $this->shelf_life_days = $material->shelf_life_days;
        } else {
            $this->resetForm();
        }

        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        // Tentukan kolom harga yang benar berdasarkan schema
        $priceColumn = \Schema::hasColumn('materials', 'unit_price') ? 'unit_price' : 
                      (\Schema::hasColumn('materials', 'price_per_m2') ? 'price_per_m2' : 'price');

        Material::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'category_id' => $this->category_id,
                'unit' => $this->unit,
                $priceColumn => $this->price, // Simpan ke kolom yang benar secara dinamis
                'stock' => $this->stock,
                'threshold' => $this->threshold,
                'minimum_stock' => $this->minimum_stock,
                'maximum_stock' => $this->maximum_stock,
                'lead_time_days' => $this->lead_time_days,
                'is_batch_tracked' => $this->is_batch_tracked,
                'shelf_life_days' => $this->shelf_life_days,
            ]
        );

        session()->flash('success', $this->editingId ? 'Bahan berhasil diperbarui!' : 'Bahan baru berhasil ditambahkan!');
        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Material::findOrFail($id)->delete();
        session()->flash('success', 'Bahan berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->category_id = '';
        $this->unit = 'm2';
        $this->price = 0;
        $this->stock = 0;
        $this->threshold = 0;
        $this->minimum_stock = 0;
        $this->maximum_stock = 0;
        $this->lead_time_days = 3;
        $this->is_batch_tracked = false;
        $this->shelf_life_days = null;
    }
}