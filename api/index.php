<?php
// Trigger Deploy: FINAL PRODUCTION (SQLITE STABLE)

use Illuminate\Http\Request;

// 🔥 Resilience Patch: Force SQLite for 100% Stability on Vercel Hobby
$fallbacks = [
    'APP_KEY'        => 'base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=',
    'APP_DEBUG'      => 'false', 
    'DB_CONNECTION'  => 'sqlite',
    'DB_DATABASE'    => __DIR__ . '/../database/database.sqlite', // Local file = 0ms latency
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE'    => 'array',
    'QUEUE_CONNECTION' => 'sync',
];

foreach ($fallbacks as $key => $value) {
    if (!getenv($key)) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// 1. BUAT FOLDER STORAGE DI /tmp
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    @mkdir($storagePath, 0777, true);
}
foreach (['/framework/views', '/framework/cache/data', '/framework/sessions', '/app/public'] as $path) {
    if (!is_dir($storagePath . $path)) {
        @mkdir($storagePath . $path, 0777, true);
    }
}

// 2. Alihkan Cache Laravel ke /tmp
putenv('TMPDIR=/tmp');
putenv('APP_PACKAGES_CACHE=' . $storagePath . '/framework/packages.php');
putenv('APP_SERVICES_CACHE=' . $storagePath . '/framework/services.php');
putenv('APP_CONFIG_CACHE=' . $storagePath . '/framework/config.php');
putenv('APP_ROUTES_CACHE=' . $storagePath . '/framework/routes.php');
putenv('APP_EVENTS_CACHE=' . $storagePath . '/framework/events.php');
putenv('LOG_CHANNEL=stderr');

try {
    // 3. Load Autoloader & Bootstrap
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';

    // 4. Split Bootstrap (Optimization)
    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    ]);

    $app->useStoragePath($storagePath);

    // Force SQLite Settings
    $app['config']->set('session.driver', 'cookie');
    $app['config']->set('cache.default', 'array');
    $app['config']->set('database.default', 'sqlite');
    $app['config']->set('database.connections.sqlite.database', env('DB_DATABASE'));
    $app['config']->set('view.compiled', $storagePath . '/framework/views');

    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ]);

    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['HTTPS'] = 'on'; 

    // 5. Handle Request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Request::capture());
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    echo "<h1>Critical Error</h1><p>" . $e->getMessage() . "</p>";
}
