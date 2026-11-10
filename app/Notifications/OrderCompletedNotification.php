<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCompletedNotification extends Notification
{
    use Queueable;
    public function __construct(public string $invoiceNumber, public int $orderId) {}
    public function via($notifiable): array { return ['database']; }
    public function toArray($notifiable): array
    {
        return [
            'title'   => '✅ Produksi Selesai',
            'message' => "Order {$this->invoiceNumber} selesai diproduksi. Silakan lakukan Quality Control (QC) dan packing.",
            'url'     => route('admin.orders.show', $this->orderId),
            'icon'    => '🎉',
            'type'    => 'success',
        ];
    }
}