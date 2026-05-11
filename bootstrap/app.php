<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 1. Trust Proxies wajib buat HTTPS di Vercel
        $middleware->trustProxies(at: '*');
        
        $middleware->redirectGuestsTo(fn () => route('login'));
        
        // 2. CSRF Exception (Biar cron atau webhook gak mental)
        $middleware->validateCsrfTokens(except: [
            '/api/cron/*',
            '/api/webhook/*', // Jaga-jaga buat integrasi kedepan
        ]);

        // 3. Alias Middleware
        $middleware->alias([
            'role'         => \App\Http\Middleware\CheckRole::class,
            'permission'   => \App\Http\Middleware\CheckPermission::class,
            'same_outlet'  => \App\Http\Middleware\EnsureSameOutlet::class,
            'localization' => \App\Http\Middleware\SetDeviceLocalization::class,
        ]);

        // 4. API Middleware
        $middleware->api(append: [
            \App\Http\Middleware\SetDeviceLocalization::class,
        ]);
        
        // 5. Statefulness Fix buat Vercel (Opsional tapi membantu)
        $middleware->statefulApi(); 
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle 419 CSRF Token Mismatch secara halus
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
        });

        // 6. Handle 504/Timeout secara halus (Biar user gak liat halaman putih Vercel)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 504) {
                return response()->view('errors.504', [], 504);
            }
        });
    })
    ->create();