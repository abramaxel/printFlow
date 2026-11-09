<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerifyEmail extends Component
{
    public function resend()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        auth()->user()->sendEmailVerificationNotification();

        session()->flash('success', 'Link verifikasi telah dikirim ulang ke email Anda.');
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}