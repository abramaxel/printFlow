<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'internal'    => \App\Http\Middleware\EnsureInternalUser::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'warehouse'   => \App\Http\Middleware\EnsureWarehouseRole::class,
            'owner'       => \App\Http\Middleware\EnsureOwnerRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi berakhir.'], 419);
            }
            return redirect()->route('login')->with('error', 'Sesi Anda berakhir demi keamanan. Silakan login kembali.');
        });
    })->create();