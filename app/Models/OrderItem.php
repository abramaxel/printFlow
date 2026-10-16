<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'service_id',
        'material_id',
        'size_id',
        'quantity',
        'area_m2',
        'unit_price_service',
        'unit_price_material',
        'subtotal',
        'discount',
        'final_subtotal',
        'design_file',
        'notes',
    ];

    protected $casts = [
        'area_m2' => 'decimal:2',
        'unit_price_service' => 'decimal:2',
        'unit_price_material' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'design_file' => 'array',
    ];

    /**
     * Relasi ke Order (Header)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke Service (Layanan)
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relasi ke Material (Bahan)
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Relasi ke Size (Ukuran)
     */
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}