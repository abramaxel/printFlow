<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user login DAN role-nya super_admin
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak. Hanya Owner yang boleh masuk.');
        }

        return $next($request);
    }
}