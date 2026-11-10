<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'name', 'code', 'category_id', 'unit', 'price', 'stock', 'stock_reserved',
        'threshold', 'minimum_stock', 'maximum_stock', 'lead_time_days',
        'ordering_cost', 'holding_cost_rate', 'is_batch_tracked', 'shelf_life_days',
        'is_active', 'description',
    ];

    protected $casts = [
        'stock' => 'decimal:2', 'stock_reserved' => 'decimal:2', 'price' => 'decimal:2',
        'minimum_stock' => 'integer', 'maximum_stock' => 'integer', 'lead_time_days' => 'integer',
        'ordering_cost' => 'decimal:2', 'holding_cost_rate' => 'decimal:2',
        'is_batch_tracked' => 'boolean', 'shelf_life_days' => 'integer', 'is_active' => 'boolean',
    ];

    // === RELASI ===
    public function category()         { return $this->belongsTo(Category::class); }
    public function orderItems()       { return $this->hasMany(OrderItem::class); }
    public function movements()        { return $this->hasMany(MaterialMovement::class); }
    public function batches()          { return $this->hasMany(MaterialBatch::class)->where('status', 'active'); }
    public function purchaseRequests() { return $this->hasMany(PurchaseRequest::class); }
    public function serviceBomItems()  { return $this->hasMany(ServiceBomItem::class); }

    // === AKSESSOR ===
    public function getAvailableStockAttribute(): float
    {
        return max(0, (float) $this->stock - (float) ($this->stock_reserved ?? 0));
    }

    public function getStockStatusAttribute(): string
    {
        $available = $this->available_stock;
        if ($available <= 0) return 'out_of_stock';
        if ($available <= (float) ($this->minimum_stock ?? 0)) return 'critical';
        if ($available <= (float) ($this->threshold ?? 0)) return 'warning';
        return 'safe';
    }

    // === METHOD DOMAIN MRP ===
    public function calculateEoq(float $annualDemand): float
    {
        $holdingCost = (float) $this->price * (float) ($this->holding_cost_rate ?? 0.20);
        if ($holdingCost <= 0) return $annualDemand;
        return sqrt((2 * $annualDemand * (float) ($this->ordering_cost ?? 50000)) / $holdingCost);
    }

    public function calculateRop(): float
    {
        return ((float) ($this->lead_time_days ?? 3) * $this->getAverageDailyUsage(30)) + (float) ($this->minimum_stock ?? 0);
    }

    public function getAverageDailyUsage(int $days = 30): float
    {
        if ($days <= 0) return 0.0;
        $total = MaterialMovement::where('material_id', $this->id)
            ->where('movement_type', 'out')
            ->where('sub_type', 'production_issue')
            ->where('movement_date', '>=', now()->subDays($days))
            ->sum('quantity');
        return (float) $total / $days;
    }

    public function needsReorder(): bool { return $this->available_stock <= $this->calculateRop(); }

    public function getPipelineDemand(): float
    {
        return (float) OrderItem::where('material_id', $this->id)
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['paid', 'processing']))
            ->sum('area_m2');
    }

    public function getNetAvailableStock(): float { return $this->available_stock - $this->getPipelineDemand(); }
}