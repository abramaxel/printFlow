<?php
// app/Models/ServiceBom.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBom extends Model
{
    protected $fillable = [
        'service_id', 'code', 'name', 'base_unit',
        'is_active', 'version', 'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'version' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function items()
    {
        return $this->hasMany(ServiceBomItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}