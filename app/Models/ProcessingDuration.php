<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessingDuration extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'days', 'multiplier', 'is_active'];

    protected $casts = [
        'days'      => 'integer',
        'multiplier'=> 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Satu durasi dipakai oleh banyak item pesanan
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'duration_id');
    }
}