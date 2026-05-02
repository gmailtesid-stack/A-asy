<?php

use Illuminate\Http\Request;

// 1. BERSIHKAN CACHE LAMA (HANYA JIKA BISA DITULIS)
$cachePath = __DIR__ . '/../bootstrap/cache';
foreach (['config.php', 'services.php', 'packages.php', 'routes.php'] as $file) {
    $fullPath = "$cachePath/$file";
    if (file_exists($fullPath) && is_writable($fullPath)) {
        @unlink($fullPath);
    }
}

// 2. BUAT FOLDER STORAGE DI /tmp
$storagePath = '/tmp/storage';
foreach (['', '/framework/views', '/framework/cache/data', '/framework/sessions', '/framework/cache', '/app/public'] as $path) {
    if (!is_dir($storagePath . $path)) {
        @mkdir($storagePath . $path, 0777, true);
    }
}

try {
    // 3. Load Autoloader & Bootstrap
    require __DIR__ . '/../vendor/autoload.php';
    
    $app = require __DIR__ . '/../bootstrap/app.php';

    // 4. Force Storage & HTTPS
    $app->useStoragePath($storagePath);

    // BRUTE FORCE REGISTRATION (PENTING UNTUK VERCEL)
    $coreProviders = [
        \Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        \Illuminate\Filesystem\FilesystemServiceProvider::class,
        \Illuminate\Auth\AuthServiceProvider::class,
        \Illuminate\Cookie\CookieServiceProvider::class,
        \Illuminate\Database\DatabaseServiceProvider::class,
        \Illuminate\Encryption\EncryptionServiceProvider::class,
        \Illuminate\Session\SessionServiceProvider::class,
        \Illuminate\View\ViewServiceProvider::class,
        \Illuminate\Routing\RoutingServiceProvider::class,
        \Illuminate\Validation\ValidationServiceProvider::class,
        \Illuminate\Translation\TranslationServiceProvider::class,
        \Illuminate\Pagination\PaginationServiceProvider::class,
    ];

    foreach ($coreProviders as $provider) {
        $app->register($provider);
    }

    // Cek APP_KEY
    if (!env('APP_KEY')) {
        die("🚨 ERROR: APP_KEY belum diset di Vercel Environment Variables!");
    }

    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['HTTPS'] = 'on'; 

    // 5. Handle Request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Request::capture());
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    echo "<div style='background:#000;color:#0f0;padding:20px;border:5px solid red;font-family:monospace;'>";
    echo "<h1>🚨 SYSTEM CRITICAL ERROR</h1>";
    echo "<h3>" . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    echo "<hr><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}


