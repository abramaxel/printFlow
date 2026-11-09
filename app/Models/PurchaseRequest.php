<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $fillable = [
        'pr_number', 'material_id', 'requested_quantity', 'unit',
        'estimated_cost', 'urgency', 'reason', 'status',
        'requested_by', 'approved_by', 'approved_at',
        'needed_by_date', 'notes',
    ];

    protected $casts = [
        'requested_quantity' => 'decimal:4',
        'estimated_cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'needed_by_date' => 'date',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCritical($query)
    {
        return $query->where('urgency', 'critical');
    }

    public static function generatePrNumber(): string
    {
        $last = static::latest('id')->first();
        $next = $last ? (intval(substr($last->pr_number, -4)) + 1) : 1;
        return 'PR-' . now()->format('Y') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}