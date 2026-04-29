<?php

// PAKSA PHP TAMPILKAN ERROR
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Illuminate\Http\Request;

try {
    // 1. Load Autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // 2. Vercel Filesystem & Cache Fix
    $isVercel = true;
    $storagePath = '/tmp/storage';
    $storageDirs = ['logs', 'framework/views', 'framework/cache/data', 'framework/sessions', 'framework/testing', 'app/public'];
    foreach (array_merge([$storagePath], array_map(fn($d) => "$storagePath/$d", $storageDirs)) as $dir) {
        if (!is_dir($dir)) mkdir($dir, 0777, true);
    }

    $tmpCache = '/tmp/bootstrap/cache';
    if (!is_dir($tmpCache)) mkdir($tmpCache, 0777, true);

    $_ENV['APP_CONFIG_CACHE'] = "$tmpCache/config.php";
    $_ENV['APP_SERVICES_CACHE'] = "$tmpCache/services.php";
    $_ENV['APP_PACKAGES_CACHE'] = "$tmpCache/packages.php";
    $_ENV['APP_ROUTES_CACHE'] = "$tmpCache/routes.php";

    // 3. Bootstrap Laravel
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    if ($isVercel) {
        $app->useStoragePath('/tmp/storage');
        $app->bind('path.bootstrap', fn() => '/tmp/bootstrap');
        
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['HTTPS'] = 'on'; 

        // Force HTTPS
        $app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\RegisterFacades::class, function ($app) {
            $app['url']->forceScheme('https');
        });

        // FORCE DEBUG
        $app->instance(\Illuminate\Contracts\Debug\ExceptionHandler::class, new class($app) extends \Illuminate\Foundation\Exceptions\Handler {
            public function __construct($app) { parent::__construct($app); }
            public function render($request, \Throwable $e) { throw $e; }
        });
    }

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
    echo "<h1>🚨 CRITICAL ERROR DETECTED</h1>";
    echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " baris " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#eee; padding:10px; overflow:auto; max-height:400px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
