<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReceipt extends Model
{
    protected $fillable = [
        'bpb_number', 'supplier_id', 'purchase_request_id', 'supplier_doc_number',
        'receipt_date', 'delivery_note_file', 'invoice_file', 'total_cost',
        'status', 'received_by', 'notes',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'total_cost' => 'decimal:2',
    ];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function items() { return $this->hasMany(PurchaseReceiptItem::class); }
    public function receiver() { return $this->belongsTo(User::class, 'received_by'); }
    public function purchaseRequest() { return $this->belongsTo(PurchaseRequest::class); }

    public static function generateBpbNumber(): string
    {
        $seq = static::whereYear('created_at', now()->year)->count() + 1;
        return 'BPB-' . now()->format('Y') . '-' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}