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
    
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 4. Force Storage & HTTPS
    $app->useStoragePath($storagePath);

    // Cek APP_KEY (Sering jadi penyebab error di Vercel)
    if (!env('APP_KEY')) {
        die("🚨 ERROR: APP_KEY belum diset di Vercel Environment Variables!");
    }
    
    // Pastikan View & Session provider terdaftar untuk error rendering
    if (!$app->bound('view')) {
        $app->register(\Illuminate\View\ViewServiceProvider::class);
    }
    if (!$app->bound('session')) {
        $app->register(\Illuminate\Session\SessionServiceProvider::class);
    }

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
    
    if ($prev = $e->getPrevious()) {
        echo "<h4 style='color:yellow;'>Root Cause: " . htmlspecialchars($prev->getMessage()) . "</h4>";
        echo "<p>File: " . $prev->getFile() . " (Line: " . $prev->getLine() . ")</p>";
    } else {
        echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    }
    
    echo "<hr><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

