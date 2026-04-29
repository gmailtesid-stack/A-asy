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
}

// 4. Handle Request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();
$kernel->terminate($request, $response);
