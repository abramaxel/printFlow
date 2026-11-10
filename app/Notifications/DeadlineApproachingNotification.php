<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeadlineApproachingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $invoiceNumber,
        public string $serviceName,
        public string $timeRemaining, // Contoh: "2 jam lagi" atau "1 hari lagi"
        public int $orderId
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => '⏰ Deadline Produksi Mendekat!',
            'message' => "Order {$this->invoiceNumber} ({$this->serviceName}) harus selesai dalam {$this->timeRemaining}. Prioritaskan antrian ini.",
            'url'     => route('admin.production'), // Deep link ke Antrian Produksi
            'icon'    => '⚠️',
            'type'    => 'danger', // Urgent / Merah
            'data'    => [
                'invoice_number' => $this->invoiceNumber,
                'time_remaining' => $this->timeRemaining,
            ]
        ];
    }
}