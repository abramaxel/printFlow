<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        // 1. Validasi input
        $this->validate();

        // 2. Coba lakukan autentikasi
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            
            // Regenerasi session untuk mencegah session fixation
            session()->regenerate();

            // Simpan user ke variabel agar aman dari error 'null'
            $user = auth()->user(); 

            // 3. Cek apakah email sudah diverifikasi
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // 4. Redirect Berdasarkan Role
            if ($user->isAdmin() || $user->isSuperAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            // Jika customer biasa
            return redirect()->intended('/dashboard');
        }

        // 5. Jika login gagal (email/password salah), tampilkan error
        $this->addError('email', 'Email atau password yang Anda masukkan salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}