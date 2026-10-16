<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Fillable attributes
     * Kolom yang boleh diisi secara massal
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * Cast attributes
     * Konversi tipe data otomatis
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Services
     * Satu kategori punya banyak services
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Relasi ke Materials
     * Satu kategori punya banyak materials
     */
    public function materials()
    {
        return $this->hasMany(Material::class);
    }
}