<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;

#[Layout('components.layouts.customer')]
#[Title('Profil Saya')]
class CustomerProfile extends Component
{
    public function mount()
    {
        $user = auth()->user();
        $internal = ['owner', 'super_admin', 'admin', 'cs', 'produksi', 'production'];
        if (in_array($user->role, $internal)) return redirect()->route('admin.dashboard');
        if ($user->role !== 'customer') abort(403, 'Akses ditolak.');
    }

    public function render()
    {
        // Selalu ambil segar dari database agar tier sinkron
        $user = User::findOrFail(auth()->id());
        $tier = $user->tier ?? 'regular';
        $tiers = $this->tierProgram();

        return view('livewire.customer.customer-profile', [
            'user'    => $user,
            'tier'    => $tier,
            'tiers'   => $tiers,
            'current' => $tiers[$tier] ?? $tiers['regular'],
            'ladder'  => ['regular', 'silver', 'gold', 'vip'],
        ]);
    }

    private function tierProgram(): array
    {
        return [
            'regular' => [
                'label' => 'Regular', 'tag' => 'Member Baru',
                'benefits' => [
                    'Portal pesanan & invoice digital 24/7',
                    'Riwayat transaksi tersimpan otomatis',
                    'Pelacakan progres produksi real-time',
                ],
            ],
            'silver' => [
                'label' => 'Silver', 'tag' => 'Member Setia',
                'benefits' => [
                    'Semua keuntungan Regular',
                    'Prioritas konfirmasi pesanan lebih cepat',
                    'Notifikasi status produksi lebih awal',
                ],
            ],
            'gold' => [
                'label' => 'Gold', 'tag' => 'Member Prioritas',
                'benefits' => [
                    'Semua keuntungan Silver',
                    'Prioritas antrian produksi & QC',
                    'Penawaran harga khusus sesuai kebijakan toko',
                ],
            ],
            'vip' => [
                'label' => 'VIP', 'tag' => 'Member Elite',
                'benefits' => [
                    'Semua keuntungan Gold',
                    'Prioritas tertinggi produksi & pengiriman',
                    'Konsultasi desain didampingi tim',
                    'Harga & termin pembayaran khusus',
                ],
            ],
            'inactive' => [
                'label' => 'Non-Aktif', 'tag' => 'Perlu Aktivasi',
                'benefits' => [
                    'Akun ditandai non-aktif oleh admin',
                    'Hubungi admin untuk mengaktifkan kembali',
                ],
            ],
        ];
    }
}