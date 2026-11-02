<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\ShopSetting;

#[Layout('components.layouts.admin')]
#[Title('Pengaturan Toko')]
class ShopSettings extends Component
{
    public array $settings = [];

    protected array $fields = [
        'shop_name'      => ['label' => 'Nama Toko',         'type' => 'text'],
        'shop_address'   => ['label' => 'Alamat Lengkap',    'type' => 'textarea'],
        'shop_phone'     => ['label' => 'No. Telepon / WA',  'type' => 'text'],
        'shop_email'     => ['label' => 'Email Toko',        'type' => 'email'],
        'bank_name'      => ['label' => 'Nama Bank',         'type' => 'text'],
        'bank_account'   => ['label' => 'No. Rekening',      'type' => 'text'],
        'bank_holder'    => ['label' => 'Atas Nama',         'type' => 'text'],
        'invoice_footer' => ['label' => 'Footer Invoice',    'type' => 'textarea'],
    ];

    public function mount(): void
    {
        // Hanya Owner & Super Admin yang boleh akses
        if (!in_array(auth()->user()->role, ['owner', 'super_admin'])) {
            abort(403, 'Hanya Owner yang dapat mengakses pengaturan toko.');
        }

        $this->settings = ShopSetting::allSettings();
    }

    public function save(): void
    {
        foreach ($this->settings as $key => $value) {
            ShopSetting::set($key, $value);
        }

        session()->flash('success', '✅ Pengaturan toko berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.admin.shop-settings', [
            'fields' => $this->fields,
        ]);
    }
}