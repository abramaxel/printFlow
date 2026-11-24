<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\EmailVerificationOtp;
use App\Mail\VerifyEmailOtpMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

#[Layout('components.layouts.auth')]
#[Title('Daftar Akun')]
class Register extends Component
{
    public string $step = 'form';
    public string $accountType = 'business';
    public string $companyName = '';
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $agree = false;

    public string $code = '';
    public ?int $otpId = null;
    public string $masked = '';
    public string $failure = '';
    public string $notice = '';
    public int $cooldown = 0;
    public int $attemptsLeft = 5;

    public function mount(): void
    {
        if (Auth::check()) {
            $role = (string) (Auth::user()->role ?? '');
            $this->redirect($role === 'customer' ? route('customer.dashboard') : route('admin.dashboard'));
            return;
        }
        $this->attemptsLeft = (int) config('otp.max_attempts', 5);
    }

    public function setType(string $t): void
    {
        if (in_array($t, ['business', 'personal'], true)) $this->accountType = $t;
    }

    private function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) return $email;
        [$n, $d] = explode('@', $email, 2);
        $len = mb_strlen($n);
        return mb_substr($n, 0, min(2, $len)) . str_repeat('*', max(3, $len - 2)) . '@' . $d;
    }

    private function issue(string $email, array $payload): array
    {
        $len = (int) config('otp.length', 6);
        $code = '';
        for ($i = 0; $i < $len; $i++) $code .= (string) random_int(0, 9);

        EmailVerificationOtp::where('email', $email)->whereNull('consumed_at')->update(['consumed_at' => now()]);

        $rec = EmailVerificationOtp::create([
            'email' => $email,
            'payload' => $payload,
            'otp_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addSeconds((int) config('otp.ttl', 600)),
            'ip' => request()->ip(),
        ]);

        try {
            Mail::to($email)->send(new VerifyEmailOtpMail($code, (string) ($payload['name'] ?? 'Calon Klien')));
        } catch (\Throwable $e) {
            $rec->delete();
            return ['ok' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()];
        }

        return ['ok' => true, 'record' => $rec];
    }

    public function submit(): void
    {
        $this->failure = '';
        $this->notice = '';

        $this->validate([
            'accountType' => 'required|in:business,personal',
            'companyName' => 'required_if:accountType,business|nullable|string|min:2|max:150',
            'name' => 'required|string|min:3|max:120',
            'email' => 'required|email:rfc|max:150',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s().]+$/'],
            'address' => 'nullable|string|max:500',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'agree' => 'accepted',
        ], [
            'companyName.required_if' => 'Nama perusahaan wajib diisi untuk akun perusahaan.',
            'companyName.min' => 'Nama perusahaan minimal 2 karakter.',
            'name.required' => 'Nama penanggung jawab wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email belum benar.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Nomor hanya boleh angka, spasi, +, -, dan tanda kurung.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi belum sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'agree.accepted' => 'Anda perlu menyetujui ketentuan layanan.',
        ]);

        $email = mb_strtolower(trim($this->email));

        if (User::whereRaw('LOWER(email) = ?', [$email])->exists()) {
            $this->failure = 'Email ini sudah terdaftar. Silakan masuk atau gunakan pemulihan sandi.';
            $this->dispatch('reg-shake');
            return;
        }

        $since = now()->subSeconds((int) config('otp.throttle_window', 900));
        if (EmailVerificationOtp::where('email', $email)->where('created_at', '>=', $since)->count() >= (int) config('otp.throttle_max', 5)) {
            $this->failure = 'Terlalu banyak permintaan kode untuk email ini. Tunggu beberapa menit.';
            return;
        }

        $payload = [
            'account_type' => $this->accountType,
            'company_name' => trim($this->companyName),
            'name' => trim($this->name),
            'email' => $email,
            'phone' => trim($this->phone),
            'address' => trim($this->address),
            'password_hash' => Hash::make($this->password),
        ];

        $res = $this->issue($email, $payload);
        if (!$res['ok']) {
            $this->failure = $res['message'];
            $this->dispatch('reg-shake');
            return;
        }

        $this->otpId = $res['record']->id;
        $this->masked = $this->maskEmail($email);
        $this->code = '';
        $this->step = 'verify';
        $this->cooldown = (int) config('otp.resend_cooldown', 60);
        $this->attemptsLeft = (int) config('otp.max_attempts', 5);
        $this->password = '';
        $this->password_confirmation = '';

        $this->dispatch('otp-focus');
        $this->dispatch('otp-sent', seconds: $this->cooldown);
    }

    public function resend(): void
    {
        $this->failure = '';
        $this->notice = '';

        $rec = EmailVerificationOtp::find($this->otpId);
        if (!$rec) {
            $this->step = 'form';
            $this->failure = 'Sesi berakhir. Silakan isi ulang formulir.';
            return;
        }

        $elapsed = now()->timestamp - $rec->created_at->timestamp;
        $wait = max(0, (int) config('otp.resend_cooldown', 60) - $elapsed);
        if ($wait > 0) {
            $this->failure = 'Tunggu ' . $wait . ' detik sebelum meminta kode baru.';
            return;
        }

        $res = $this->issue($rec->email, (array) $rec->payload);
        if (!$res['ok']) {
            $this->failure = $res['message'];
            return;
        }

        $this->otpId = $res['record']->id;
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
            $this->dispatch('reg-shake');
            return;
        }

        $rec = EmailVerificationOtp::find($this->otpId);
        if (!$rec || !$rec->isUsable()) {
            $this->failure = 'Kode kedaluwarsa. Minta kode baru.';
            $this->dispatch('reg-shake');
            return;
        }

        $max = (int) config('otp.max_attempts', 5);
        if ($rec->attempts >= $max) {
            $this->failure = 'Percobaan habis. Minta kode baru.';
            $this->dispatch('reg-shake');
            return;
        }

        $rec->increment('attempts');
        $this->attemptsLeft = max(0, $max - (int) $rec->fresh()->attempts);

        if (!Hash::check($this->code, $rec->otp_hash)) {
            $this->failure = 'Kode salah. Sisa ' . $this->attemptsLeft . ' percobaan.';
            $this->code = '';
            $this->dispatch('otp-clear');
            $this->dispatch('reg-shake');
            return;
        }

        $p = (array) $rec->payload;

        if (User::whereRaw('LOWER(email) = ?', [mb_strtolower((string) $p['email'])])->exists()) {
            $rec->forceFill(['consumed_at' => now()])->save();
            $this->step = 'form';
            $this->failure = 'Email sudah terdaftar. Silakan masuk.';
            return;
        }

        $cols = Schema::getColumnListing('users');
        $fill = [
            'name' => (string) $p['name'],
            'email' => (string) $p['email'],
            'password' => (string) $p['password_hash'],
            'role' => 'customer',
        ];
        if (in_array('phone', $cols, true)) $fill['phone'] = (string) $p['phone'];
        if (in_array('address', $cols, true)) $fill['address'] = (string) $p['address'];
        if (in_array('company_name', $cols, true)) $fill['company_name'] = (string) $p['company_name'];
        if (in_array('customer_type', $cols, true)) $fill['customer_type'] = (string) $p['account_type'];
        if (in_array('status', $cols, true)) $fill['status'] = 'active';
        if (in_array('tier', $cols, true)) $fill['tier'] = 'regular';
        if (in_array('is_guest', $cols, true)) $fill['is_guest'] = false;
        if (in_array('email_verified_at', $cols, true)) $fill['email_verified_at'] = now();

        $user = new User();
        $user->forceFill($fill)->save();

        $rec->forceFill(['verified_at' => now(), 'consumed_at' => now()])->save();
        EmailVerificationOtp::where('email', $rec->email)->whereNull('consumed_at')->update(['consumed_at' => now()]);

        Auth::login($user);
        session()->regenerate();

        $this->step = 'done';
    }

    public function backToForm(): void
    {
        $this->step = 'form';
        $this->code = '';
        $this->failure = '';
        $this->notice = '';
    }

    public function finish()
    {
        return redirect()->route('customer.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register', [
            'brand' => (string) config('app.brand.name', config('app.name', 'FC Murni Jaya')),
            'support' => (string) config('app.brand.whatsapp', ''),
            'digits' => (int) config('otp.length', 6),
            'ttlMinutes' => (int) round((int) config('otp.ttl', 600) / 60),
            'loginUrl' => \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login'),
        ]);
    }
}