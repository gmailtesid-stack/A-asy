<?php

use Illuminate\Http\Request;

// 1. PAKSA TAMPILKAN ERROR
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. BUAT FOLDER STORAGE
$storagePath = '/tmp/storage';
foreach (['', '/framework/views', '/framework/cache/data', '/framework/sessions'] as $path) {
    if (!is_dir($storagePath . $path)) {
        mkdir($storagePath . $path, 0777, true);
    }
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
    echo "<div style='background:#000;color:#0f0;padding:20px;border:5px solid red;'>";
    echo "<h1>🚨 SYSTEM CRASH DETECTED</h1>";
    echo "<h3>" . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
