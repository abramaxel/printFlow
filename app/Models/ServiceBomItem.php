<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBomItem extends Model
{
    protected $fillable = [
        'service_bom_id', 'material_id', 'quantity_per_unit',
        'unit', 'scrap_percentage', 'is_primary', 'sort_order', 'notes',
    ];

    protected $casts = [
        'quantity_per_unit' => 'decimal:4',
        'scrap_percentage' => 'decimal:2',
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function bom()
    {
        return $this->belongsTo(ServiceBom::class, 'service_bom_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Hitung kebutuhan material untuk output tertentu
     */
    public function calculateNeed(float $outputQuantity): float
    {
        $base = $this->quantity_per_unit * $outputQuantity;
        $scrap = $base * ($this->scrap_percentage / 100);
        return $base + $scrap;
    }
}