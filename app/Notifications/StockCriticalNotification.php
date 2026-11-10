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
        public float $minimumStock,
        public int $materialId
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => '🚨 Stok Bahan Kritis (Hampir Habis)',
            'message' => "Stok {$this->materialName} tersisa {$this->currentStock} (Batas Min: {$this->minimumStock}). Segera buat Purchase Request.",
            'url'     => route('admin.materials'), // Deep link ke Manajemen Bahan
            'icon'    => '📦',
            'type'    => 'danger', // Urgent / Merah
            'data'    => [
                'material_name'  => $this->materialName,
                'current_stock'  => $this->currentStock,
                'minimum_stock'  => $this->minimumStock,
            ]
        ];
    }
}