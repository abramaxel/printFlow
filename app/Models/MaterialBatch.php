<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialBatch extends Model
{
    protected $fillable = [
        'material_id', 'batch_number', 'initial_quantity', 'remaining_quantity',
        'unit_cost', 'received_date', 'expiry_date', 'supplier', 'status',
    ];

    protected $casts = [
        'initial_quantity' => 'decimal:4',
        'remaining_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'received_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
                     ->where('remaining_quantity', '>', 0);
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<=', now()->addDays($days));
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}