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
}