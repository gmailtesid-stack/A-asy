<?php

// PAKSA PHP TAMPILKAN ERROR
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Illuminate\Http\Request;

try {
    // 1. Load Autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // 2. Vercel Storage Fix
    $storagePath = '/tmp/storage';
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0777, true);
        mkdir("$storagePath/framework/views", 0777, true);
        mkdir("$storagePath/framework/cache/data", 0777, true);
        mkdir("$storagePath/framework/sessions", 0777, true);
    }

    // 3. Bootstrap Laravel
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // FORCE VERCEL PATHS
    $app->useStoragePath($storagePath);
    $app->instance('path.public', __DIR__ . '/../public');
    $app->instance('path.base', __DIR__ . '/..');
    
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['HTTPS'] = 'on'; 

    // Force HTTPS & Fix View Paths
    $app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\RegisterFacades::class, function ($app) {
        $app['url']->forceScheme('https');
        
        // Pastikan folder views terdeteksi
        $app['view.finder']->addLocation(__DIR__ . '/../resources/views');
    });

    // 4. Handle Request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Request::capture()
    );
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    // Abaikan normal auth redirect
    if (str_contains(get_class($e), 'AuthenticationException')) {
        throw $e;
    }

    header('Content-Type: text/html', true, 500);
    echo "<div style='font-family:sans-serif; padding:20px; border:5px solid red; background:#fff1f1;'>";
    echo "<h1>🚨 CRITICAL ERROR</h1>";
    echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " baris " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#eee; padding:10px; overflow:auto; max-height:400px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
