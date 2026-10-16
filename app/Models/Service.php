<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price_per_m2',
        'is_active',
    ];

    protected $casts = [
        'price_per_m2' => 'decimal:2', // Otomatis format 2 desimal
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Category
     * Satu service dimiliki oleh satu kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}