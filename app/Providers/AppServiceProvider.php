<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Counter;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\VerifyEmail;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Livewire::component('counter', Counter::class);
        Livewire::component('register', Register::class);
        Livewire::component('login', Login::class);
        Livewire::component('verify-email', VerifyEmail::class);
    }
}