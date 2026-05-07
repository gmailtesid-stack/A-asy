<?php
// THE ULTIMATE STABLE DEPLOY: STANDARD BOOT + SINGAPORE + SQLITE

use Illuminate\Http\Request;

// 1. Force Essential Env
putenv('APP_ENV=production');
putenv('APP_DEBUG=false');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . __DIR__ . '/../database/database.sqlite');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');

// 2. Storage Fix for Vercel (/tmp is the only writable area)
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    @mkdir($storagePath, 0777, true);
    @mkdir($storagePath . '/framework/views', 0777, true);
    @mkdir($storagePath . '/framework/cache', 0777, true);
    @mkdir($storagePath . '/framework/sessions', 0777, true);
}

// 3. Load Autoloader & App
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

// 4. Inject Vercel-Specific Configs
$app->useStoragePath($storagePath);
$app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\LoadConfiguration::class, function($app) use ($storagePath) {
    $app['config']->set('view.compiled', $storagePath . '/framework/views');
    $app['config']->set('database.default', 'sqlite');
    $app['config']->set('database.connections.sqlite.database', __DIR__ . '/../database/database.sqlite');
});

// 5. Standard Kernel Handle
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Request::capture());
$response->send();
$kernel->terminate($request, $response);
