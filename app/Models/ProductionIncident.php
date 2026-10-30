<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionIncident extends Model
{
    protected $fillable = [
        'order_id',
        'reported_by',
        'type',
        'description',
        'impact_days',
        'status',
        'resolved_at',
    ];

    protected $casts = [
        'impact_days' => 'integer',
        'resolved_at' => 'datetime',
    ];

    public const TYPES = [
        'machine_breakdown' => 'Kerusakan Mesin',
        'material_shortage' => 'Bahan Habis / Kurang',
        'design_revision'   => 'Revisi Desain dari Customer',
        'human_error'       => 'Kesalahan Operasional',
        'utility_outage'    => 'Listrik / Jaringan Padam',
        'other'             => 'Lainnya',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}