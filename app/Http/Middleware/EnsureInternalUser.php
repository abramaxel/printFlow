<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInternalUser
{
    /**
     * Handle an incoming request.
     * Hanya izinkan role internal (bukan customer) mengakses area admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRoles = [
            'owner', 'super_admin', 'admin', 'cs', 
            'produksi', 'production'
        ];

        if (!$request->user() || !in_array($request->user()->role, $allowedRoles)) {
            // Jika customer mencoba akses halaman admin, tendang ke dashboard customer
            if ($request->user() && $request->user()->role === 'customer') {
                return redirect()->route('customer.dashboard')   // ✅ sesuai web.php
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            abort(403, 'Akses ditolak. Area ini khusus untuk staff internal.');
        }

        return $next($request);
    }
}