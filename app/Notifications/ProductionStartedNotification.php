<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductionStartedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $invoiceNumber,
        public int $orderId,
        public string $customerName
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'production_started',
            'icon' => '🏭',
            'title' => 'Produksi Dimulai',
            'message' => "Order {$this->invoiceNumber} ({$this->customerName}) telah masuk tahap produksi. Bahan sudah diambil dari gudang.",
            'order_id' => $this->orderId,
            'url' => route('admin.orders.show', $this->orderId),
        ];
    }
}