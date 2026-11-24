<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.admin')]
#[Title('Kanal Pembayaran')]
class PaymentChannels extends Component
{
    use WithFileUploads;

    public string $qris_merchant = '';
    public string $ewallet_provider = '';
    public string $ewallet_number = '';
    public string $ewallet_holder = '';
    public $qris = null;

    public ?string $qrisPath = null;
    private array $cols = [];
    private bool $kv = false;

    public function mount(): void
    {
        $role = (string) (auth()->user()->role ?? '');
        if (!in_array($role, ['owner', 'super_admin'], true)) {
            abort(403, 'Kanal pembayaran hanya dapat diatur owner.');
        }

        $this->cols = Schema::hasTable('shop_settings') ? Schema::getColumnListing('shop_settings') : [];
        $this->kv = in_array('key', $this->cols, true) && in_array('value', $this->cols, true);

        $v = $this->readAll();
        $this->qris_merchant = (string) ($v['qris_merchant'] ?? '');
        $this->ewallet_provider = (string) ($v['ewallet_provider'] ?? '');
        $this->ewallet_number = (string) ($v['ewallet_number'] ?? '');
        $this->ewallet_holder = (string) ($v['ewallet_holder'] ?? '');
        $this->qrisPath = (string) ($v['qris_image'] ?? '') ?: null;
    }

    private function readAll(): array
    {
        if (empty($this->cols)) return [];
        try {
            if ($this->kv) return DB::table('shop_settings')->pluck('value', 'key')->all();
            $row = DB::table('shop_settings')->orderBy('id')->first();
            return $row ? (array) $row : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function write(array $data): void
    {
        if (empty($this->cols)) return;
        $hasUpd = in_array('updated_at', $this->cols, true);
        $hasCre = in_array('created_at', $this->cols, true);

        if ($this->kv) {
            foreach ($data as $k => $v) {
                $payload = ['value' => (string) $v];
                if ($hasUpd) $payload['updated_at'] = now();
                if (DB::table('shop_settings')->where('key', $k)->exists()) {
                    DB::table('shop_settings')->where('key', $k)->update($payload);
                } else {
                    $payload['key'] = $k;
                    if ($hasCre) $payload['created_at'] = now();
                    DB::table('shop_settings')->insert($payload);
                }
            }
        } else {
            $payload = [];
            foreach ($data as $k => $v) {
                if (in_array($k, $this->cols, true)) $payload[$k] = (string) $v;
            }
            if (empty($payload)) return;
            if ($hasUpd) $payload['updated_at'] = now();
            $row = DB::table('shop_settings')->orderBy('id')->first();
            if ($row) {
                DB::table('shop_settings')->where('id', $row->id)->update($payload);
            } else {
                if ($hasCre) $payload['created_at'] = now();
                DB::table('shop_settings')->insert($payload);
            }
        }

        if (class_exists(\App\Support\ShopSettings::class)) {
            try { \App\Support\ShopSettings::flush(); } catch (\Throwable $e) {}
        }
    }

    public function save(): void
    {
        $this->validate([
            'qris_merchant' => 'nullable|string|max:120',
            'ewallet_provider' => 'nullable|string|max:80',
            'ewallet_number' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\-\s().]*$/'],
            'ewallet_holder' => 'nullable|string|max:120',
        ], [
            'ewallet_number.regex' => 'Nomor hanya boleh angka, spasi, +, -, dan tanda kurung.',
        ]);

        $this->write([
            'qris_merchant' => trim($this->qris_merchant),
            'ewallet_provider' => trim($this->ewallet_provider),
            'ewallet_number' => trim($this->ewallet_number),
            'ewallet_holder' => trim($this->ewallet_holder),
        ]);

        session()->flash('pc_success', 'Kanal pembayaran disimpan. Halaman bayar pelanggan langsung memakai data terbaru.');
    }

    public function saveQris(): void
    {
        $this->validate([
            'qris' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'qris.required' => 'Pilih gambar QRIS terlebih dahulu.',
            'qris.image' => 'File harus berupa gambar.',
            'qris.mimes' => 'Format: JPG, PNG, atau WEBP.',
            'qris.max' => 'Ukuran maksimal 2MB.',
        ]);

        if ($this->qrisPath && Storage::disk('public')->exists($this->qrisPath)) {
            Storage::disk('public')->delete($this->qrisPath);
        }

        $path = $this->qris->store('payment-channels', 'public');
        $this->write(['qris_image' => $path]);

        $this->qris = null;
        $this->qrisPath = $path;

        session()->flash('pc_success', 'Gambar QRIS diperbarui.');
    }

    public function removeQris(): void
    {
        if ($this->qrisPath && Storage::disk('public')->exists($this->qrisPath)) {
            Storage::disk('public')->delete($this->qrisPath);
        }
        $this->write(['qris_image' => '']);
        $this->qrisPath = null;

        session()->flash('pc_success', 'Gambar QRIS dihapus. Invoice berkanal QRIS akan diarahkan ke transfer bank.');
    }

    public function render()
    {
        $url = null;
        if ($this->qrisPath) {
            try {
                if (Storage::disk('public')->exists($this->qrisPath)) {
                    $url = Storage::disk('public')->url($this->qrisPath);
                }
            } catch (\Throwable $e) {
            }
        }

        $bank = $this->readAll();

        return view('livewire.admin.payment-channels', [
            'qrisUrl' => $url,
            'ready' => !empty($this->cols),
            'bankReady' => trim((string) ($bank['bank_account'] ?? '')) !== '',
            'qrisReady' => $url !== null,
            'walletReady' => trim($this->ewallet_number) !== '',
        ]);
    }
}