<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // PERBAIKAN: Percayai semua proxy (dibutuhkan untuk Live Share / Cloudflare / Docker proxy)
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // API murni: tanpa route web 'login'. Arahkan tamu langsung ke 401 JSON
        $middleware->redirectGuestsTo(fn () => response()->json(['message' => 'Unauthenticated.'], 401));

        // Route API menggunakan middleware 'web' (untuk sesi cookie dari frontend
        // proxy). Nonaktifkan verifikasi CSRF khusus untuk prefix /api/*.
        $middleware->validateCsrfTokens(except: ['api/*']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();