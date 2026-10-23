<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    const DAILY_CAPACITY_PER_SERVICE = 5; // Asumsi 1 layanan bisa handle 5 order/haris

    protected $fillable = [
        'user_id',
        'invoice_number',
        'status',
        'delivery_method',
        'shipping_address',
        'notes',
        'priority', 
        'deadline_date',
        'shipping_method',
        'shipping_address',
    ];

    protected $casts = [
        'deadline_date' => 'date',
    ];

    /**
     * Daftar status yang diizinkan (Validasi di level Model)
     */
    public static $allowedStatuses = [
        'pending',           // Pesanan baru dibuat, menunggu admin cek
        'waiting_invoice',   // Admin sedang membuat tagihan
        'unpaid',            // Tagihan terkirim, menunggu customer bayar
        'paid',              // Customer sudah upload bukti bayar (menunggu verifikasi)
        'processing',        // Produksi sedang berjalan
        'qc',                // Quality Control (Pengecekan kualitas)
        'shipping',          // Sedang dalam pengiriman / siap diambil
        'completed',         // Selesai
        'cancelled',         // Dibatalkan
        'rejected',          // Ditolak admin (misal desain tidak layak)
    ];

    /**
     * Relasi ke User (Pemilik Pesanan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Order Items (Detail produk yang dipesan)
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke Invoice (Tagihan)
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Relasi ke Order Histories (Riwayat perubahan status / Order Track)
     */
    public function histories()
    {
        return $this->hasMany(OrderHistory::class)->latest();
    }

    public static function getProductionLoad($serviceId)
    {
        // Hitung order yang masih aktif (belum selesai/batal) untuk layanan ini
        $activeOrders = self::whereHas('items', function($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            })
            ->whereIn('status', ['waiting_invoice', 'unpaid', 'processing', 'qc'])
            ->count();

        // Rumus Cerdas: (Total Order Aktif / Kapasitas Harian)
        // Jika ada 10 order banner, dan kapasitas 5/hari -> butuh 2 hari tambahan
        $estimatedDelayDays = ceil($activeOrders / self::DAILY_CAPACITY_PER_SERVICE);

        return [
            'active_count' => $activeOrders,
            'estimated_delay_days' => $estimatedDelayDays,
            'suggested_deadline' => now()->addDays($estimatedDelayDays)->format('Y-m-d')
        ];
    }
}