<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeadlineApproachingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $invoiceNumber,
        public string $deadlineDate,
        public int $orderId
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'deadline_approaching',
            'icon' => '⏰',
            'title' => 'Deadline Mendekat!',
            'message' => "Order {$this->invoiceNumber} harus selesai pada {$this->deadlineDate}.",
            'order_id' => $this->orderId,
            'url' => route('admin.orders.show', $this->orderId),
        ];
    }
}