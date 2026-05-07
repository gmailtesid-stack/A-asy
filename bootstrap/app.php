<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Tambahkan ini agar folder view compilation otomatis pindah ke /tmp
if (isset($_SERVER['VERCEL'])) {
    $viewPath = '/tmp/storage/framework/views';
    if (!is_dir($viewPath)) {
        @mkdir($viewPath, 0755, true);
    }
    config(['view.compiled' => $viewPath]);
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->validateCsrfTokens(except: [
            '/api/cron/*',
        ]);

        // $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'role'        => \App\Http\Middleware\CheckRole::class,
            'permission'  => \App\Http\Middleware\CheckPermission::class,
            'same_outlet' => \App\Http\Middleware\EnsureSameOutlet::class,
            'localization' => \App\Http\Middleware\SetDeviceLocalization::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\SetDeviceLocalization::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
