<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerRole
{
    /**
     * Master Data & Pengaturan Toko hanya untuk Owner dan Super Admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = ['owner', 'super_admin'];
        $user = $request->user();

        if (!$user || !in_array($user->role, $allowed, true)) {
            abort(403, 'Akses ditolak. Master Data & Pengaturan Toko hanya untuk Owner dan Super Admin.');
        }

        return $next($request);
    }
}