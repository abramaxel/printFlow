<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GoodsReceivedNotification extends Notification
{
    use Queueable;
    public function __construct(public string $bpbNumber, public string $supplierName, public float $totalCost, public int $receiptId) {}
    public function via($notifiable): array { return ['database']; }
    public function toArray($notifiable): array
    {
        return [
            'title'   => '📥 Penerimaan Barang Tercatat',
            'message' => "Barang dari {$this->supplierName} (BPB: {$this->bpbNumber}) senilai Rp " . number_format($this->totalCost, 0, ',', '.') . " berhasil masuk gudang.",
            'url'     => route('admin.goods-receipt'),
            'icon'    => '🚚',
            'type'    => 'success',
        ];
    }
}