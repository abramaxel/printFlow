<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReceiptItem extends Model
{
    protected $fillable = [
        'purchase_receipt_id', 'material_id', 'quantity', 'unit',
        'unit_cost', 'total_cost', 'batch_number', 'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function receipt() { return $this->belongsTo(PurchaseReceipt::class, 'purchase_receipt_id'); }
    public function material() { return $this->belongsTo(Material::class); }
}