<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWarehouseRole
{
    /**
     * Modul Purchasing & Inventory/Gudang hanya untuk Produksi dan Owner.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = ['owner', 'super_admin', 'produksi', 'production'];
        $user = $request->user();

        if (!$user || !in_array($user->role, $allowed, true)) {
            abort(403, 'Akses ditolak. Modul Purchasing & Inventory hanya untuk role Produksi dan Owner.');
        }

        return $next($request);
    }
}