<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Support\ShopSettings as Store;

#[Layout('components.layouts.admin')]
#[Title('Pengaturan Toko')]
class ShopSettings extends Component
{
    public string $shop_name = '';
    public string $shop_phone = '';
    public string $shop_email = '';
    public string $shop_address = '';
    public string $shop_hours = '';
    public string $bank_name = '';
    public string $bank_account = '';
    public string $bank_holder = '';
    public string $invoice_footer = '';
    public string $payment_instructions = '';

    public array $original = [];
    public bool $ready = false;

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin'], true)) {
            abort(403, 'Pengaturan toko hanya dapat diubah owner.');
        }

        $this->ready = !empty(Store::columns());

        $v = Store::all();
        $this->shop_name            = $v['shop_name'] !== '' ? $v['shop_name'] : (string) config('app.brand.name', config('app.name', 'FC Murni Jaya'));
        $this->shop_phone           = $v['shop_phone'];
        $this->shop_email           = $v['shop_email'];
        $this->shop_address         = $v['shop_address'];
        $this->shop_hours           = $v['shop_hours'];
        $this->bank_name            = $v['bank_name'];
        $this->bank_account         = $v['bank_account'];
        $this->bank_holder          = $v['bank_holder'];
        $this->invoice_footer       = $v['invoice_footer'];
        $this->payment_instructions = $v['payment_instructions'];

        $this->original = $this->snapshot();
    }

    private function snapshot(): array
    {
        return [
            'shop_name' => $this->shop_name,
            'shop_phone' => $this->shop_phone,
            'shop_email' => $this->shop_email,
            'shop_address' => $this->shop_address,
            'shop_hours' => $this->shop_hours,
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'bank_holder' => $this->bank_holder,
            'invoice_footer' => $this->invoice_footer,
            'payment_instructions' => $this->payment_instructions,
        ];
    }

    public function save(): void
    {
        if (!$this->ready) {
            session()->flash('shop_error', 'Tabel shop_settings belum siap. Jalankan php artisan migrate.');
            return;
        }

        $data = $this->validate([
            'shop_name' => 'required|string|min:2|max:120',
            'shop_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s().]+$/'],
            'shop_email' => 'nullable|email:rfc|max:120',
            'shop_address' => 'nullable|string|max:500',
            'shop_hours' => 'nullable|string|max:120',
            'bank_name' => 'nullable|string|max:60',
            'bank_account' => ['nullable', 'string', 'max:60', 'regex:/^[0-9\-\s]+$/'],
            'bank_holder' => 'nullable|string|max:120',
            'invoice_footer' => 'nullable|string|max:300',
            'payment_instructions' => 'nullable|string|max:500',
        ], [
            'shop_name.required' => 'Nama toko wajib diisi — dipakai pada kop invoice dan portal.',
            'shop_name.min' => 'Nama toko minimal 2 karakter.',
            'shop_phone.regex' => 'Nomor hanya boleh angka, spasi, +, -, dan tanda kurung.',
            'shop_email.email' => 'Format email toko tidak valid.',
            'bank_account.regex' => 'Nomor rekening hanya boleh angka, spasi, dan tanda hubung.',
        ]);

        foreach ($data as $k => $v) $data[$k] = trim((string) $v);

        Store::put($data);

        foreach ($data as $k => $v) $this->{$k} = $v;
        $this->original = $this->snapshot();

        session()->flash('shop_success', 'Pengaturan toko disimpan. Invoice PDF, halaman bayar, dan portal pelanggan langsung memakai data terbaru.');
        $this->dispatch('shop-saved');
    }

    public function resetForm(): void
    {
        foreach ($this->original as $k => $v) $this->{$k} = $v;
        $this->resetErrorBag();
        $this->dispatch('shop-reset');
    }

    public function render()
    {
        return view('livewire.admin.shop-settings', [
            'waNumber' => Store::wa($this->shop_phone),
            'storageMode' => Store::isKv() ? 'key-value' : 'kolom',
        ]);
    }
}