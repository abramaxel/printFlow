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

        public function incidents()
    {
        return $this->hasMany(ProductionIncident::class);
    }

    /** Anggaran waktu per tahap, sebagai fraksi dari jendela SLA (created_at -> deadline). */
    public const STAGE_BUDGET = ['paid' => 0.15, 'processing' => 0.60, 'qc' => 0.25];

    /**
     * Trace produksi: gejala delay otomatis + kemacetan per tahap.
     * Menghindari API Carbon yang beda antar versi -> hitung via timestamp mentah.
     */
    public function productionTrace(): array
    {
        $now = now();
        $terminal = ['completed', 'cancelled', 'rejected'];
        $stage = $this->status;

        if (in_array($stage, $terminal, true)) {
            return ['state'=>'done','stage'=>$stage,'stage_since'=>null,'dwell_hours'=>0.0,
                    'budget_hours'=>null,'overrun'=>false,'hours_left'=>null,'deadline'=>$this->deadline_date];
        }

        $last = $this->histories()->latest('id')->first();
        $stageSince = $last?->created_at ?? $this->created_at;
        $dwell = round(abs($now->getTimestamp() - $stageSince->getTimestamp()) / 3600, 1);

        $budget = null;
        if ($this->deadline_date && $this->created_at) {
            $window = ($this->deadline_date->getTimestamp() - $this->created_at->getTimestamp()) / 3600;
            if ($window > 0 && isset(self::STAGE_BUDGET[$stage])) {
                $budget = round($window * self::STAGE_BUDGET[$stage], 1);
            }
        }
        $overrun = $budget !== null && $dwell > $budget;

        $hoursLeft = $this->deadline_date
            ? round(($this->deadline_date->getTimestamp() - $now->getTimestamp()) / 3600, 1)
            : null;

        if ($this->deadline_date && $now->greaterThan($this->deadline_date->copy()->endOfDay())) {
            $state = 'delayed';                       // lewat deadline, belum selesai
        } elseif ($hoursLeft !== null && $hoursLeft <= 24) {
            $state = 'at_risk';                       // <= 24 jam lagi
        } elseif ($overrun) {
            $state = 'stalled';                       // diam terlalu lama di satu tahap
        } else {
            $state = 'on_track';
        }

        return ['state'=>$state,'stage'=>$stage,'stage_since'=>$stageSince,'dwell_hours'=>$dwell,
                'budget_hours'=>$budget,'overrun'=>$overrun,'hours_left'=>$hoursLeft,'deadline'=>$this->deadline_date];
    }

    /** Proyeksi deadline bila semua kendala terbuka benar-benar memakan estimasinya. */
    public function projectedDeadline()
    {
        if (!$this->deadline_date) return null;
        $extra = $this->incidents()->where('status', 'open')->sum('impact_days');
        return $extra > 0 ? $this->deadline_date->copy()->addDays($extra) : $this->deadline_date;
    }
}