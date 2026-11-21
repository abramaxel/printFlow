<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads; // <-- WAJIB ADA INI
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.customer')]
#[Title('Pengaturan Akun')]
class CustomerSettings extends Component
{
    use WithFileUploads; // <-- WAJIB ADA INI

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $address_detail = '';

    public $avatar = null; // Untuk Livewire file upload
    public ?string $current_avatar = null; // Path avatar yang sedang aktif

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public array $original = [];

    public function mount()
    {
        $user = Auth::user();
        $internal = ['owner', 'super_admin', 'admin', 'cs', 'produksi', 'production'];

        if (in_array($user->role, $internal, true)) return redirect()->route('admin.dashboard');
        if ($user->role !== 'customer') abort(403, 'Akses ditolak.');

        $this->name           = (string) $user->name;
        $this->email          = (string) $user->email;
        $this->phone          = (string) ($user->phone ?? '');
        $this->address        = (string) ($user->address ?? '');
        $this->address_detail = (string) ($user->address_detail ?? '');
        $this->current_avatar = $user->avatar_url;
        $this->original       = $this->snapshot();
    }

    private function snapshot(): array
    {
        return [
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'address'        => $this->address,
            'address_detail' => $this->address_detail,
        ];
    }

    public function addLandmarkHint(string $hint): void
    {
        $hint = trim($hint);
        if ($hint === '') return;

        $current = trim($this->address_detail);
        if ($current !== '' && str_contains(mb_strtolower($current), mb_strtolower($hint))) {
            $this->dispatch('landmark-updated');
            return;
        }

        $this->address_detail = mb_substr($current === '' ? $hint : $current . ', ' . $hint, 0, 500);
        $this->dispatch('landmark-updated');
    }

    public function clearAddressDetail(): void
    {
        $this->address_detail = '';
        $this->dispatch('landmark-updated');
    }

    public function saveProfile(): void
    {
        $user = Auth::user();

        $data = $this->validate([
            'name'           => 'required|string|min:3|max:255',
            'email'          => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'          => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s().]+$/'],
            'address'        => 'nullable|string|max:1000',
            'address_detail' => 'nullable|string|max:500',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'name.min'           => 'Nama lengkap minimal 3 karakter.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format alamat email tidak valid.',
            'email.unique'       => 'Email ini sudah digunakan akun lain.',
            'phone.regex'        => 'Nomor hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
            'phone.max'          => 'Nomor telepon terlalu panjang.',
            'address.max'        => 'Alamat lengkap maksimal 1000 karakter.',
            'address_detail.max' => 'Detail alamat / patokan maksimal 500 karakter.',
        ]);

        $data['phone']          = trim((string) ($data['phone'] ?? ''));
        $data['address']        = trim((string) ($data['address'] ?? ''));
        $data['address_detail'] = trim((string) ($data['address_detail'] ?? ''));

        $user->forceFill($data)->save();
        Auth::setUser($user);

        $this->name           = (string) $data['name'];
        $this->email          = (string) $data['email'];
        $this->phone          = $data['phone'];
        $this->address        = $data['address'];
        $this->address_detail = $data['address_detail'];
        $this->original       = $this->snapshot();

        session()->flash('success', 'Perubahan profil berhasil disimpan.');
        $this->dispatch('profile-saved');
    }

    public function resetProfile(): void
    {
        $this->name           = $this->original['name'];
        $this->email          = $this->original['email'];
        $this->phone          = $this->original['phone'];
        $this->address        = $this->original['address'];
        $this->address_detail = $this->original['address_detail'];

        $this->resetErrorBag(['name', 'email', 'phone', 'address', 'address_detail']);
        $this->dispatch('profile-reset');
    }

        public function saveAvatar(): void
    {
        $this->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'avatar.required' => 'Pilih file foto terlebih dahulu.',
            'avatar.image'    => 'File harus berupa gambar.',
            'avatar.mimes'    => 'Format yang diperbolehkan: JPG, PNG, JPEG, WEBP.',
            'avatar.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $this->avatar->store('avatars', 'public');
        $user->forceFill(['avatar' => $path])->save();
        Auth::setUser($user);

        $this->current_avatar = $path;
        $this->avatar = null;

        session()->flash('success_avatar', 'Foto profil berhasil diperbarui.');
        $this->redirect(route('customer.settings'));
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->forceFill(['avatar' => null])->save();
        Auth::setUser($user);

        $this->current_avatar = null;

        session()->flash('success_avatar', 'Foto profil dihapus.');
        $this->redirect(route('customer.settings'));
    }

    public function savePassword(): void
    {
        $user = Auth::user();

        $this->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Masukkan password Anda saat ini.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini salah.');
            return;
        }

        if (Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Password baru harus berbeda dari password saat ini.');
            return;
        }

        $user->update(['password' => Hash::make($this->password)]);
        Auth::setUser($user);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->resetErrorBag(['current_password', 'password', 'password_confirmation']);

        session()->flash('success_password', 'Password berhasil diperbarui.');
        $this->dispatch('password-saved');
    }

    public function render()
    {
        $this->current_avatar = Auth::user()->avatar;

        return view('livewire.customer.customer-settings', [
            'user'      => Auth::user(),
            'avatarUrl' => $this->avatarUrl(),
            'tierMeta'  => $this->tierMeta(),
        ]);
    }

    private function tierMeta(): array
    {
        return [
            'regular'  => ['label' => 'Regular',   'badge' => 'bg-ice-200 text-brand-800'],
            'silver'   => ['label' => 'Silver',    'badge' => 'bg-white/20 text-white'],
            'gold'     => ['label' => 'Gold',      'badge' => 'bg-amber-300 text-brand-950'],
            'vip'      => ['label' => 'VIP',       'badge' => 'bg-signal-400 text-brand-950'],
            'inactive' => ['label' => 'Non-Aktif', 'badge' => 'bg-red-500 text-white'],
        ];
    }

    private function avatarUrl(): ?string
    {
        $user = Auth::user();
        $path = (string) ($user->avatar ?? '');

        if ($path === '' || !Storage::disk('public')->exists($path)) {
            return null;
        }

        return route('media.avatar', ['user' => $user->id, 'v' => substr(md5($path), 0, 8)]);
    }
}