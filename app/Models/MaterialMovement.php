<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialMovement extends Model
{
    protected $fillable = [
        'material_id', 'user_id', 'movement_type', 'sub_type',
        'quantity', 'unit', 'unit_cost', 'total_cost',
        'reference_type', 'reference_id',
        'batch_number', 'expiry_date',
        'notes', 'movement_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'movement_date' => 'datetime',
        'expiry_date' => 'date',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Scope untuk query umum
    public function scopeIn($query)
    {
        return $query->where('movement_type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('movement_type', 'out');
    }

    public function scopeReserve($query)
    {
        return $query->where('movement_type', 'reserve');
    }

    public function scopeRelease($query)
    {
        return $query->where('movement_type', 'release');
    }
}