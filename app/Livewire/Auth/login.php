<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

#[Layout('components.layouts.auth')]
#[Title('Masuk')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $failure = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect($this->homeFor((string) (Auth::user()->role ?? '')));
        }
    }

    private function homeFor(string $role): string
    {
        return $role === 'customer' ? route('customer.dashboard') : route('admin.dashboard');
    }

    public function authenticate(): void
    {
        $this->failure = '';

        $this->validate([
            'email' => 'required|email:rfc|max:150',
            'password' => 'required|string|min:6|max:120',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email belum benar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $key = 'login:' . Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->failure = 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.';
            $this->dispatch('auth-shake');
            return;
        }

        $ok = Auth::attempt(
            ['email' => Str::lower(trim($this->email)), 'password' => $this->password],
            $this->remember
        );

        if (!$ok) {
            RateLimiter::hit($key, 120);
            $this->failure = 'Email atau kata sandi tidak cocok.';
            $this->password = '';
            $this->dispatch('auth-shake');
            return;
        }

        $user = Auth::user();
        $status = (string) ($user->status ?? 'active');

        if ($status === 'suspended') {
            Auth::logout();
            $this->failure = 'Akun ini dinonaktifkan. Hubungi admin untuk mengaktifkannya kembali.';
            $this->dispatch('auth-shake');
            return;
        }

        RateLimiter::clear($key);
        session()->regenerate();

        $this->redirect($this->homeFor((string) ($user->role ?? '')));
    }

    private function registerUrl(): ?string
    {
        foreach (['register', 'customer.register', 'auth.register', 'signup', 'daftar', 'customer.signup'] as $n) {
            if (\Illuminate\Support\Facades\Route::has($n)) {
                return route($n);
            }
        }

        foreach (\Illuminate\Support\Facades\Route::getRoutes()->getRoutes() as $r) {
            if (!in_array('GET', $r->methods(), true)) continue;
            $uri = $r->uri();
            if (str_contains($uri, '{')) continue;
            if (preg_match('/(^|\/)(register|daftar|signup)(\/|$)/i', $uri)) {
                return url($uri);
            }
        }

        return null;
    }

    private function resetUrl(): ?string
    {
        foreach (['password.request', 'password.email', 'forgot-password', 'lupa-password'] as $n) {
            if (\Illuminate\Support\Facades\Route::has($n)) {
                return route($n);
            }
        }
        return null;
    }

    public function render()
    {
        $reg = $this->registerUrl();
        $res = $this->resetUrl();

        return view('livewire.auth.login', [
            'brand' => (string) config('app.brand.name', config('app.name', 'FC Murni Jaya')),
            'support' => (string) config('app.brand.whatsapp', ''),
            'canReset' => $res !== null,
            'resetUrl' => $res,
            'canRegister' => $reg !== null,
            'registerUrl' => $reg,
        ]);
    }
}