<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'subtotal',
        'shipping_cost',
        'tax',
        'discount',
        'total_amount',
        'status',
        'due_date',
        'paid_at',
        'payment_token',
        'total_paid',
        'payment_method',     
        'payment_scheme',      
        'cash_receipt',        
        'installment_notes',
        'payment_schedule',   
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'payment_schedule' => 'array',
    ];

    /**
     * Relasi ke Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke Payments (Satu tagihan bisa dibayar berkali-kali / cicilan)
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Helper: Format Rupiah
     */
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
    
        /**
     * Rincian biaya granular per baris item.
     * Standar referensi: EN 16931-1 (line-item breakdown: unit price, qty, allowance/charge).
     */
    public static function costBreakdown($order): array
    {
        $lines = [];
        $totBase = $totFee = $totMaster = $totDiscount = $totFinal = 0.0;

        foreach ($order->items as $item) {
            $unitService  = (float) $item->unit_price_service;
            $unitMaterial = (float) $item->unit_price_material;
            $area         = (float) $item->area_m2;
            $base         = round(($unitService + $unitMaterial) * $area, 2);
            $fee          = round((float) $item->duration_fee, 2);
            $master       = round((float) $item->subtotal, 2);
            $discount     = round((float) $item->discount, 2);
            $final        = round((float) $item->final_subtotal, 2);

            // NAMA & HARI: snapshot dulu, fallback live, terakhir '—' (JUJUR, bukan 'Normal')
            $snapName = $item->duration_name ?? null;
            $liveName = optional($item->duration)->name ?? null;
            $snapDays = $item->duration_days ?? null;
            $liveDays = optional($item->duration)->days ?? null;

            $lines[] = [
                'service'       => optional($item->service)->name ?? '-',
                'material'      => optional($item->material)->name ?? '-',
                'size'          => optional($item->size)->name ?? '-',
                'duration'      => $snapName ?: ($liveName ?: '—'),
                'duration_days' => $snapDays ?? $liveDays,
                'multiplier'    => (float) $item->duration_multiplier,
                'unit_service'  => $unitService,
                'unit_material' => $unitMaterial,
                'unit_combined' => round($unitService + $unitMaterial, 2),
                'area'          => $area,
                'qty'           => (int) $item->quantity,
                'base'          => $base,
                'fee'           => $fee,
                'master'        => $master,
                'discount'      => $discount,
                'final'         => $final,
                'notes'         => $item->notes,
            ];

            $totBase += $base; $totFee += $fee; $totMaster += $master;
            $totDiscount += $discount; $totFinal += $final;
        }

        return [
            'lines'          => $lines,
            'total_base'     => round($totBase, 2),
            'total_fee'      => round($totFee, 2),
            'total_master'   => round($totMaster, 2),
            'total_discount' => round($totDiscount, 2),
            'total_final'    => round($totFinal, 2),
        ];
    }
}