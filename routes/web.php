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
        $userRole = auth()->user()->role;
        $adminRoles = ['owner', 'super_admin', 'admin', 'cs', 'produksi', 'production'];
        
        if (in_array($userRole, $adminRoles)) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($userRole === 'customer') {
            return redirect()->route('customer.dashboard');
        }
        
        // Fallback untuk role yang tidak dikenali
        auth()->logout();
        return redirect()->route('login')
            ->with('error', 'Role user tidak dikenali. Silakan hubungi administrator.');
    }
    return redirect()->route('login');
});

// ==========================================
// LOGOUT
// ==========================================
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// ==========================================
// AUTH ROUTES (Guest Only)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});

// ==========================================
// EMAIL VERIFICATION
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
// CUSTOMER DASHBOARD (HANYA untuk role 'customer')
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        // Guard: Hanya customer yang boleh akses /dashboard
        if (auth()->user()->role !== 'customer') {
            // Redirect ke admin dashboard untuk staff internal
            $adminRoles = ['owner', 'super_admin', 'admin', 'cs', 'produksi', 'production'];
            if (in_array(auth()->user()->role, $adminRoles)) {
                return redirect()->route('admin.dashboard');
            }
            abort(403, 'Akses ditolak. Anda bukan customer.');
        }
        
        return app(\App\Livewire\Customer\CustomerDashboard::class);
    })->name('customer.dashboard');
});

// ==========================================
// ADMIN & INTERNAL ROUTES
// DIJAGA OLEH MIDDLEWARE 'internal' 
// (Mencegah customer akses via URL manual)
// ==========================================
Route::middleware(['auth', 'internal'])->group(function () {
    Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/settings', \App\Livewire\Admin\ShopSettings::class)->name('admin.settings');
    Route::get('/admin/inventory', \App\Livewire\Admin\InventoryDashboard::class)->name('admin.inventory');
    Route::get('/admin/stock-opname', \App\Livewire\Admin\StockOpname::class)->name('admin.stock-opname');
    Route::get('/admin/material-audit-trail', \App\Livewire\Admin\MaterialAuditTrail::class)->name('admin.material-audit-trail');
    
    // Master 
    Route::get('/admin/categories', \App\Livewire\Admin\Categories::class)->name('admin.categories');
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

    // === MODUL PURCHASING / MRP ===
    Route::get('/admin/suppliers', \App\Livewire\Admin\Suppliers::class)->name('admin.suppliers');
    Route::get('/admin/purchase-requests', \App\Livewire\Admin\PurchaseRequests::class)->name('admin.purchase-requests');
    Route::get('/admin/goods-receipt', \App\Livewire\Admin\GoodsReceipt::class)->name('admin.goods-receipt');
    Route::get('/admin/purchase-receipts/{receipt}/pdf', function (\App\Models\PurchaseReceipt $receipt) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('purchase-receipts.bpb', ['receipt' => $receipt]);
        return $pdf->stream($receipt->bpb_number . '.pdf');
    })->name('admin.purchase-receipts.pdf');
});

// ==========================================
// MAGIC LINK ROUTES (Public)
// ==========================================
Route::get('/bayar/{token}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/bayar/{token}', [PaymentController::class, 'upload'])->name('payment.upload');