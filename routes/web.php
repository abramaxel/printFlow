<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Services;
use App\Livewire\Admin\Materials;
use App\Livewire\Admin\Sizes;
use App\Livewire\Admin\OrderCreate;
use App\Livewire\Admin\Orders;
use App\Livewire\Admin\ProductionQueue;
use App\Livewire\Admin\OrderShow;
use App\Livewire\Admin\ProductionMonitor;
use App\Livewire\Customer\CustomerDashboard;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;

// ==========================================
// HALAMAN UTAMA (Root /)
// ==========================================
Route::get('/', function () {
    if (auth()->check()) {
        // Daftar semua role internal (termasuk variasi penulisan)
        $adminRoles = [
            'owner', 'super_admin', 'admin', 'cs', 
            'produksi', 'production',  // Support 2 variasi
        ];
        
        $userRole = auth()->user()->role;
        
        // Debug: log role user untuk troubleshooting
        logger("User role detected: '{$userRole}'");
        
        if (in_array($userRole, $adminRoles)) {
            return redirect()->route('admin.dashboard');
        }
        
        // Customer atau role lain
        return redirect()->route('customer.dashboard');
    }
    return redirect()->route('login');
});

// ==========================================
// ROUTE LOGOUT
// ==========================================
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// ==========================================
// AUTH ROUTES (Hanya untuk Guest)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});

// ==========================================
// ROUTE CUSTOMER (Butuh verified)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', CustomerDashboard::class)->name('customer.dashboard');
});

// ==========================================
// EMAIL VERIFICATION ROUTES
// ==========================================
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link verifikasi berhasil dikirim ulang!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ==========================================
// ROUTE ADMIN & INTERNAL (TIDAK BUTUH verified!)
// ==========================================
Route::middleware(['auth'])->group(function () {
    // Dashboard Role-Aware
    Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');
    
    // Master Data
    Route::get('/admin/services', Services::class)->name('admin.services');
    Route::get('/admin/materials', Materials::class)->name('admin.materials');
    Route::get('/admin/sizes', Sizes::class)->name('admin.sizes');
    
    // Production
    Route::get('/admin/production', ProductionQueue::class)->name('admin.production');
    Route::get('/admin/production-monitor', ProductionMonitor::class)->name('admin.production-monitor');
    
    // Order Management
    Route::get('/admin/orders', Orders::class)->name('admin.orders');
    Route::get('/admin/orders/create', OrderCreate::class)->name('admin.orders.create');
    Route::get('/admin/orders/{order}', OrderShow::class)->name('admin.orders.show');
    Route::get('/admin/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('admin.invoices.download');

    Route::get('/admin/active-orders', \App\Livewire\Admin\ActiveOrders::class)->name('admin.active-orders');
});

// ==========================================
// MAGIC LINK ROUTES (PUBLIC / GUEST)
// ==========================================
Route::get('/bayar/{token}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/bayar/{token}', [PaymentController::class, 'upload'])->name('payment.upload');