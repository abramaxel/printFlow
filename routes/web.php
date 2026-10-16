<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Livewire\Counter;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Services;
use App\Livewire\Admin\Materials;
use App\Livewire\Admin\Sizes;
use App\Livewire\Admin\OrderCreate;
use App\Livewire\Admin\Orders; 
use Illuminate\Http\Request;
use App\Livewire\Admin\OrderShow;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;

// ==========================================
// HALAMAN UTAMA (Root /)
// ==========================================
Route::get('/', function () {
    // Jika user sudah login, arahkan sesuai role
    if (auth()->check()) {
        if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard'); // Dashboard customer
    }
    
    // Jika belum login, arahkan ke halaman login
    return redirect()->route('login');
});

// ==========================================
// ROUTE LOGOUT
// ==========================================
Route::post('/logout', function () {
    Auth::logout(); // Logout user
       
    request()->session()->invalidate(); // Hapus semua data session
    request()->session()->regenerateToken(); // Buat token CSRF baru untuk keamanan
       
    return redirect('/'); // Arahkan kembali ke halaman utama (yang akan redirect ke login)
})->name('logout');
   
// ==========================================
// AUTH ROUTES (Hanya untuk Guest / Belum Login)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});

// ==========================================
// ROUTE UNTUK USER SUDAH LOGIN (Customer)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('livewire.customer.dashboard'); // Placeholder untuk dashboard customer
    })->name('dashboard');
});

// ==========================================
// EMAIL VERIFICATION ROUTES
// ==========================================

// 1. Halaman pemberitahuan "Silakan cek email Anda"
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. Route untuk memproses klik link dari email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // Tandai email sebagai verified
    
    // Redirect ke root, nanti root route akan mengarahkan sesuai role (Admin/Customer)
    return redirect('/'); 
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. Route untuk mengirim ulang email verifikasi jika user tidak menerimanya
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link verifikasi berhasil dikirim ulang!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ==========================================
// ROUTE KHUSUS ADMIN & SUPER ADMIN
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard & Master Data
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/services', Services::class)->name('admin.services');
    Route::get('/admin/materials', Materials::class)->name('admin.materials');
    Route::get('/admin/sizes', Sizes::class)->name('admin.sizes');
    
    // Order Management
    Route::get('/admin/orders', Orders::class)->name('admin.orders'); // Aktifkan nanti
    Route::get('/admin/orders/create', OrderCreate::class)->name('admin.orders.create');
    Route::get('/admin/orders/{order}', OrderShow::class)->name('admin.orders.show');
    Route::get('/admin/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('admin.invoices.download');
});

// ==========================================
// MAGIC LINK ROUTES (PUBLIC / GUEST - TANPA LOGIN)
// ==========================================
Route::get('/bayar/{token}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/bayar/{token}', [PaymentController::class, 'upload'])->name('payment.upload');