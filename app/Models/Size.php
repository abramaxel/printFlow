<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'width_cm',
        'height_cm',
        'area_m2',
        'price_adjustment',
        'is_active',
    ];

    protected $casts = [
        'area_m2' => 'decimal:4', // Presisi 4 desimal untuk akurasi perhitungan luas
        'price_adjustment' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}