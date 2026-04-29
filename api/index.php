<?php

use Illuminate\Http\Request;

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
$_ENV['APP_ENV'] = 'production';
$_ENV['APP_KEY'] = 'base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=';
$_ENV['APP_DEBUG'] = 'true';
$_ENV['APP_URL'] = 'https://e-asy.vercel.app';
$_ENV['APP_SERVICES_CACHE'] = "$tmpCache/services.php";
$_ENV['APP_PACKAGES_CACHE'] = "$tmpCache/packages.php";
$_ENV['APP_ROUTES_CACHE'] = "$tmpCache/routes.php";

// 4. Bootstrap Laravel
try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    if ($isVercel) {
        $app->useStoragePath('/tmp/storage');
        $app->bind('path.bootstrap', fn() => '/tmp/bootstrap');
        
        // FORCED DEBUG: Show raw errors, bypass Laravel's view-based error handler
        $app->instance(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            new class($app) extends \Illuminate\Foundation\Exceptions\Handler {
                public function __construct($app) { parent::__construct($app); }
                public function render($request, \Throwable $e) { throw $e; }
            }
        );

        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['HTTPS'] = 'on'; 

        // Force HTTPS for all generated URLs
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }

// 4. Handle Request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    header('Content-Type: text/html', true, 500);
    echo "<div style='font-family:sans-serif; padding:20px; border:5px solid red; background:#fff1f1;'>";
    echo "<h1>🚨 LOGIN ERROR DIAGNOSIS</h1>";
    echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " baris " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#eee; padding:10px; overflow:auto; max-height:400px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
