<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Profil & Akun')]
class StaffSettings extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $phone = '';

    public $avatar = null;
    public ?string $currentAvatar = null;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public array $original = [];
    public array $cols = [];

    public function mount(): void
    {
        $u = Auth::user();
        $role = (string) ($u->role ?? '');

        if (!$u || $role === 'customer') {
            abort(403, 'Halaman ini untuk akun staf internal.');
        }

        $this->cols = Schema::getColumnListing('users');

        $this->name = (string) $u->name;
        $this->email = (string) $u->email;
        $this->phone = (string) ($u->phone ?? '');
        $this->currentAvatar = $u->avatar ? (string) $u->avatar : null;

        $this->original = ['name' => $this->name, 'email' => $this->email, 'phone' => $this->phone];
    }

    private function avatarUrl(): ?string
    {
        $u = Auth::user();
        $path = (string) ($u->avatar ?? '');
        if ($path === '' || !Storage::disk('public')->exists($path)) return null;
        return route('media.avatar', ['user' => $u->id, 'v' => substr(md5($path), 0, 8)]);
    }

    public function saveProfile(): void
    {
        $u = Auth::user();

        $data = $this->validate([
            'name' => 'required|string|min:3|max:120',
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('users', 'email')->ignore($u->id)],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s().]+$/'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'phone.regex' => 'Nomor hanya boleh angka, spasi, +, -, dan tanda kurung.',
        ]);

        $u->forceFill([
            'name' => trim($data['name']),
            'email' => trim($data['email']),
            'phone' => trim((string) $data['phone']),
        ])->save();

        Auth::setUser($u);
        $this->original = ['name' => $u->name, 'email' => $u->email, 'phone' => (string) ($u->phone ?? '')];

        session()->flash('acc_success', 'Profil diperbarui. Nama dan kontak Anda langsung berlaku di seluruh sistem.');
        $this->redirect(route('admin.account'));
    }

    public function resetProfile(): void
    {
        foreach ($this->original as $k => $v) $this->{$k} = $v;
        $this->resetErrorBag(['name', 'email', 'phone']);
        $this->dispatch('acc-reset');
    }

    public function saveAvatar(): void
    {
        $this->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'avatar.required' => 'Pilih file foto terlebih dahulu.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format yang diperbolehkan: JPG, PNG, JPEG, WEBP.',
            'avatar.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $u = Auth::user();

        if ($u->avatar && Storage::disk('public')->exists($u->avatar)) {
            Storage::disk('public')->delete($u->avatar);
        }

        $path = $this->avatar->store('avatars', 'public');
        $u->forceFill(['avatar' => $path])->save();
        Auth::setUser($u);

        $this->avatar = null;
        $this->currentAvatar = $path;

        session()->flash('acc_success', 'Foto profil diperbarui.');
        $this->redirect(route('admin.account'));
    }

    public function removeAvatar(): void
    {
        $u = Auth::user();

        if ($u->avatar && Storage::disk('public')->exists($u->avatar)) {
            Storage::disk('public')->delete($u->avatar);
        }

        $u->forceFill(['avatar' => null])->save();
        Auth::setUser($u);
        $this->currentAvatar = null;

        session()->flash('acc_success', 'Foto profil dihapus. Inisial nama dipakai kembali.');
        $this->redirect(route('admin.account'));
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.required' => 'Masukkan kata sandi saat ini.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.different' => 'Kata sandi baru harus berbeda dari yang lama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $u = Auth::user();
        $u->forceFill(['password' => Hash::make($this->password)])->save();
        Auth::setUser($u);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->resetErrorBag(['current_password', 'password']);

        session()->flash('acc_success', 'Kata sandi berhasil diganti. Gunakan kata sandi baru pada login berikutnya.');
        $this->dispatch('acc-password-done');
    }

    public function render()
    {
        $u = Auth::user();
        $role = (string) ($u->role ?? '');

        $roleLabel = match ($role) {
            'owner' => 'Owner',
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'cs' => 'Customer Service',
            'produksi', 'production' => 'Produksi / Gudang',
            default => ucfirst(str_replace('_', ' ', $role)),
        };

        $access = match (true) {
            in_array($role, ['owner', 'super_admin'], true) => [
                'Seluruh modul & master data',
                'Persetujuan purchase request',
                'Revisi deadline & eskalasi produksi',
                'CRM, dashboard inventory, audit trail',
                'Pengaturan toko & rekening pembayaran',
            ],
            $role === 'admin' => [
                'Pesanan, invoice, verifikasi pembayaran',
                'Master data & pengaturan terbatas',
                'Penerimaan barang & audit trail',
                'CRM pelanggan',
            ],
            $role === 'cs' => [
                'Buat pesanan & terbitkan invoice',
                'Follow-up pembayaran pelanggan',
                'Komunikasi serah terima & kendala',
            ],
            default => [
                'Antrian produksi & BOM layanan',
                'Ajukan purchase request',
                'Penerimaan barang & stock opname',
                'Audit trail persediaan',
            ],
        };

        $verified = in_array('email_verified_at', $this->cols, true)
            ? ($u->email_verified_at ? Carbon::parse($u->email_verified_at)->format('d M Y') : null)
            : 'n/a';

        return view('livewire.admin.staff-settings', [
            'user' => $u,
            'avatarUrl' => $this->avatarUrl(),
            'roleLabel' => $roleLabel,
            'access' => $access,
            'joined' => $u->created_at ? Carbon::parse($u->created_at)->format('d M Y') : '—',
            'updated' => $u->updated_at ? Carbon::parse($u->updated_at)->format('d M Y, H:i') : '—',
            'verified' => $verified,
            'statusLabel' => (string) ($u->status ?? 'active'),
        ]);
    }
}