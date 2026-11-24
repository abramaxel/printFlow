<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\PasswordResetOtp;
use App\Support\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

#[Layout('components.layouts.auth')]
#[Title('Pulihkan Kata Sandi')]
class ForgotPassword extends Component
{
    public string $step = 'identify';
    public string $identifier = '';
    public string $channel = 'email';
    public string $code = '';
    public string $password = '';
    public string $password_confirmation = '';

    public ?int $otpId = null;
    public string $masked = '';
    public string $resetToken = '';
    public string $failure = '';
    public string $notice = '';
    public int $cooldown = 0;
    public int $attemptsLeft = 5;

    public function mount(): void
    {
        if (Auth::check()) {
            $role = (string) (Auth::user()->role ?? '');
            $this->redirect($role === 'customer' ? route('customer.dashboard') : route('admin.dashboard'));
        }
        $this->attemptsLeft = (int) config('otp.max_attempts', 5);
    }

    public function sendCode(): void
    {
        $this->failure = '';
        $this->notice = '';

        $this->validate(['identifier' => 'required|string|min:5|max:150'], [
            'identifier.required' => 'Masukkan email atau nomor WhatsApp terdaftar.',
            'identifier.min' => 'Email atau nomor terlalu pendek.',
        ]);

        $id = trim($this->identifier);
        $normalized = str_contains($id, '@') ? mb_strtolower($id) : OtpService::normalizePhone($id);

        if (OtpService::throttleHit($normalized)) {
            $this->failure = 'Terlalu banyak permintaan kode. Tunggu beberapa menit lalu coba lagi.';
            $this->dispatch('auth-shake');
            return;
        }

        $user = OtpService::findUser($id);
        if (!$user) {
            $this->failure = 'Email atau nomor tersebut tidak terdaftar.';
            $this->dispatch('auth-shake');
            return;
        }

        if ((string) ($user->status ?? 'active') === 'suspended') {
            $this->failure = 'Akun ini dinonaktifkan. Hubungi admin untuk pemulihan.';
            $this->dispatch('auth-shake');
            return;
        }

        $wantWa = $this->channel === 'whatsapp';
        $hasPhone = OtpService::normalizePhone((string) $user->phone) !== '';
        $hasEmail = trim((string) $user->email) !== '';

        $useChannel = 'email';
        if ($wantWa && $hasPhone && OtpService::whatsappReady()) {
            $useChannel = 'whatsapp';
        } elseif ($wantWa && (!$hasPhone || !OtpService::whatsappReady())) {
            if (!$hasEmail) {
                $this->failure = 'Akun ini tidak punya email dan gateway WhatsApp belum aktif. Hubungi admin.';
                $this->dispatch('auth-shake');
                return;
            }
            $this->notice = 'Pengiriman WhatsApp belum tersedia untuk akun ini. Kode dikirim ke email terdaftar.';
        }

        $result = OtpService::issue($user, $useChannel, $normalized);

        if (!$result['ok']) {
            $this->failure = $result['message'];
            $this->dispatch('auth-shake');
            return;
        }

        $this->otpId = $result['record']->id;
        $this->masked = $result['masked'];
        $this->channel = $useChannel;
        $this->code = '';
        $this->step = 'verify';
        $this->cooldown = (int) config('otp.resend_cooldown', 60);
        $this->attemptsLeft = (int) config('otp.max_attempts', 5);

        $this->dispatch('otp-focus');
        $this->dispatch('otp-sent', seconds: $this->cooldown);
    }

    public function resend(): void
    {
        $this->failure = '';
        $this->notice = '';

        $id = trim($this->identifier);
        $normalized = str_contains($id, '@') ? mb_strtolower($id) : OtpService::normalizePhone($id);

        $wait = OtpService::cooldownRemaining($normalized);
        if ($wait > 0) {
            $this->failure = 'Tunggu ' . $wait . ' detik sebelum meminta kode baru.';
            return;
        }

        if (OtpService::throttleHit($normalized)) {
            $this->failure = 'Batas permintaan kode tercapai. Coba lagi beberapa menit.';
            return;
        }

        $user = OtpService::findUser($id);
        if (!$user) {
            $this->step = 'identify';
            $this->failure = 'Sesi berakhir. Masukkan ulang email atau nomor Anda.';
            return;
        }

        $result = OtpService::issue($user, $this->channel, $normalized);
        if (!$result['ok']) {
            $this->failure = $result['message'];
            return;
        }

        $this->otpId = $result['record']->id;
        $this->masked = $result['masked'];
        $this->code = '';
        $this->cooldown = (int) config('otp.resend_cooldown', 60);
        $this->attemptsLeft = (int) config('otp.max_attempts', 5);
        $this->notice = 'Kode baru sudah dikirim.';

        $this->dispatch('otp-focus');
        $this->dispatch('otp-sent', seconds: $this->cooldown);
    }

    public function verify(): void
    {
        $this->failure = '';
        $this->notice = '';

        $digits = (int) config('otp.length', 6);
        if (!preg_match('/^\d{' . $digits . '}$/', $this->code)) {
            $this->failure = 'Masukkan ' . $digits . ' digit kode lengkap.';
            $this->dispatch('auth-shake');
            return;
        }

        $record = PasswordResetOtp::find($this->otpId);
        if (!$record) {
            $this->step = 'identify';
            $this->failure = 'Sesi kode tidak ditemukan. Mulai ulang.';
            return;
        }

        $result = OtpService::verify($record, $this->code);
        $this->attemptsLeft = max(0, (int) config('otp.max_attempts', 5) - (int) $record->fresh()->attempts);

        if (!$result['ok']) {
            $this->failure = $result['message'];
            $this->code = '';
            $this->dispatch('otp-clear');
            $this->dispatch('auth-shake');
            return;
        }

        $this->resetToken = $result['token'];
        $this->step = 'reset';
        $this->dispatch('reset-focus');
    }

    public function savePassword(): void
    {
        $this->failure = '';

        $this->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi belum sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $record = OtpService::consume($this->resetToken);
        if (!$record) {
            $this->step = 'identify';
            $this->failure = 'Sesi pemulihan kedaluwarsa. Mulai ulang dari awal.';
            return;
        }

        $user = $record->user;
        if (!$user) {
            $this->step = 'identify';
            $this->failure = 'Akun tidak ditemukan.';
            return;
        }

        if (Hash::check($this->password, $user->password)) {
            $this->failure = 'Kata sandi baru harus berbeda dari yang lama.';
            $this->dispatch('auth-shake');
            return;
        }

        $user->forceFill(['password' => Hash::make($this->password)])->save();
        $record->forceFill(['consumed_at' => now()])->save();

        PasswordResetOtp::where('user_id', $user->id)->whereNull('consumed_at')->update(['consumed_at' => now()]);

        $this->step = 'done';
        $this->password = '';
        $this->password_confirmation = '';
        $this->dispatch('reset-done');
    }

    public function backToIdentify(): void
    {
        $this->step = 'identify';
        $this->code = '';
        $this->failure = '';
        $this->notice = '';
        $this->otpId = null;
        $this->resetToken = '';
    }

    public function setChannel(string $c): void
    {
        if (in_array($c, ['email', 'whatsapp'], true)) $this->channel = $c;
    }

    public function finish()
    {
        session()->flash('auth_success', 'Kata sandi berhasil diperbarui. Silakan masuk dengan sandi baru.');
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.forgot-password', [
            'brand' => (string) config('app.brand.name', config('app.name', 'FC Murni Jaya')),
            'waReady' => OtpService::whatsappReady(),
            'digits' => (int) config('otp.length', 6),
            'ttlMinutes' => (int) round((int) config('otp.ttl', 600) / 60),
            'support' => (string) config('app.brand.whatsapp', ''),
        ]);
    }
}