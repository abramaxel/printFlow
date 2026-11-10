<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentPendingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $invoiceNumber,
        public string $customerName,
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
            'title'   => '💰 Bukti Pembayaran Baru Masuk',
            'message' => "{$this->customerName} mengupload bukti bayar sebesar Rp " . number_format($this->amount, 0, ',', '.') . " untuk Invoice {$this->invoiceNumber}. Segera verifikasi.",
            'url'     => route('admin.orders.show', $this->orderId), // Deep link ke detail order
            'icon'    => '💵',
            'type'    => 'warning', // Butuh tindakan admin
            'data'    => [
                'invoice_number' => $this->invoiceNumber,
                'customer_name'  => $this->customerName,
            ]
        ];
    }
}