<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentPendingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $invoiceNumber,
        public float $amount,
        public int $orderId
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'payment_pending',
            'icon' => '💰',
            'title' => 'Pembayaran Menunggu Verifikasi',
            'message' => "Invoice {$this->invoiceNumber} sebesar Rp " . number_format($this->amount, 0, ',', '.') . " menunggu verifikasi.",
            'order_id' => $this->orderId,
            'url' => route('admin.orders.show', $this->orderId),
        ];
    }
}