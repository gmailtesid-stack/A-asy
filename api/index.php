<?php
// FINAL STABLE: SINGAPORE + SQLITE + CORRECT BOOT SEQUENCE

use Illuminate\Http\Request;

// 1. Force Production Essentials
putenv('APP_ENV=production');
putenv('APP_DEBUG=false');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . __DIR__ . '/../database/database.sqlite');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');

// 2. Storage Setup (RAM Disk)
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    @mkdir($storagePath, 0777, true);
    @mkdir($storagePath . '/framework/views', 0777, true);
}

// 3. Load Application
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

// 4. Correct Boot Sequence for Vercel
$app->useStoragePath($storagePath);

// This is CRITICAL: Load the basic configuration services
$app->bootstrapWith([
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
]);

$app['config']->set('view.compiled', $storagePath . '/framework/views');
$app['config']->set('database.default', 'sqlite');
$app['config']->set('database.connections.sqlite.database', __DIR__ . '/../database/database.sqlite');

// 5. Handle Request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Request::capture());
$response->send();
$kernel->terminate($request, $response);
