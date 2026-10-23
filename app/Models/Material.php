<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price_per_m2',
        'stock',
        'stock_reserved',
        'is_active',
    ];

    protected $casts = [
        'price_per_m2' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Helper: Hitung stok yang benar-benar bisa dijual
    public function getAvailableStockAttribute()
    {
        return max(0, $this->stock - $this->stock_reserved);
    }
}