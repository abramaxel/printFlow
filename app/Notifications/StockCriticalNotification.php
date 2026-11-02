<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StockCriticalNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $materialName,
        public float $currentStock,
        public float $threshold
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'stock_critical',
            'icon' => '⚠️',
            'title' => 'Stok Bahan Kritis!',
            'message' => "{$this->materialName} tersisa {$this->currentStock} m² (threshold: {$this->threshold} m²).",
            'url' => route('admin.materials'),
        ];
    }
}