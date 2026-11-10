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
            'title'   => '🏭 Produksi Dimulai',
            'message' => "Order {$this->invoiceNumber} untuk {$this->customerName} sedang dalam tahap produksi (On Process).",
            'url'     => route('admin.orders.show', $this->orderId),
            'icon'    => '⚙️',
            'type'    => 'info', // Informasi / Biru
            'data'    => [
                'invoice_number' => $this->invoiceNumber,
                'customer_name'  => $this->customerName,
            ]
        ];
    }
}