<?php

use Illuminate\Http\Request;

// 1. BERSIHKAN CACHE LAMA (WAJIB UNTUK VERCEL)
$cachePath = __DIR__ . '/../bootstrap/cache';
foreach (['config.php', 'services.php', 'packages.php', 'routes.php'] as $file) {
    if (file_exists("$cachePath/$file")) unlink("$cachePath/$file");
}

// 2. BUAT FOLDER STORAGE DI /tmp
$storagePath = '/tmp/storage';
foreach (['', '/framework/views', '/framework/cache/data', '/framework/sessions'] as $path) {
    if (!is_dir($storagePath . $path)) mkdir($storagePath . $path, 0777, true);
}

try {
    // 3. Load Autoloader & Bootstrap
    require __DIR__ . '/../vendor/autoload.php';
    
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 4. Force Storage & HTTPS
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
    echo "<h1>🚨 SYSTEM AUTO-RECOVERY</h1>";
    echo "<h3>" . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    echo "<hr><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
