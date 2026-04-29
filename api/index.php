<?php

use Illuminate\Http\Request;

// 1. BUAT FOLDER STORAGE
$storagePath = '/tmp/storage';
foreach (['', '/framework/views', '/framework/cache/data', '/framework/sessions'] as $path) {
    if (!is_dir($storagePath . $path)) mkdir($storagePath . $path, 0777, true);
}

try {
    // 2. Load Autoloader & Bootstrap
    require __DIR__ . '/../vendor/autoload.php';
    
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 3. DAFTAR SELURUH LAYANAN INTI (WAJIB UNTUK VERCEL + LARAVEL 13)
    $app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
    $app->register(\Illuminate\Cookie\CookieServiceProvider::class);
    $app->register(\Illuminate\Session\SessionServiceProvider::class);
    $app->register(\Illuminate\View\ViewServiceProvider::class);
    $app->register(\Illuminate\Database\DatabaseServiceProvider::class);
    $app->register(\Illuminate\Encryption\EncryptionServiceProvider::class);
    $app->register(\Illuminate\Routing\RoutingServiceProvider::class);
    $app->register(\Illuminate\Auth\AuthServiceProvider::class);

    // 4. Force Storage Path & HTTPS
    $app->useStoragePath($storagePath);
    
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['HTTPS'] = 'on'; 

    $app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\RegisterFacades::class, function ($app) {
        $app['url']->forceScheme('https');
    });

    // 5. Handle Request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Request::capture());
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    // Abaikan auth redirect
    if (str_contains(get_class($e), 'AuthenticationException')) {
        throw $e;
    }

    echo "<div style='background:#000;color:#0f0;padding:20px;border:5px solid red;font-family:monospace;'>";
    echo "<h1>🚨 CRITICAL SYSTEM RECOVERY</h1>";
    echo "<h3>" . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    echo "<hr><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
