<?php
// Trigger Deploy: FINAL PRODUCTION (AWS STABLE)

use Illuminate\Http\Request;

// 🔥 Resilience Patch: Suntikkan ENV secara manual untuk Vercel agar tidak 500
$fallbacks = [
    'APP_KEY'        => 'base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=',
    'APP_DEBUG'      => 'false', // Matikan debug untuk performa maksimal
    'DB_CONNECTION'  => 'mysql',
    'DB_HOST'        => 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com',
    'DB_PORT'        => '4000',
    'DB_DATABASE'    => 'easy_pos',
    'DB_USERNAME'    => '3JKwuvbTLoRLXAb.root',
    'DB_PASSWORD'    => '5dql1tIk3FLU6CXW',
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE'    => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'MYSQL_ATTR_SSL_CA' => 'database/isrgrootx1.pem',
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

    // Force Production Settings
    $app['config']->set('session.driver', 'cookie');
    $app['config']->set('cache.default', 'array');
    $app['config']->set('trustedproxy.proxies', ['*']);
    $app['config']->set('view.compiled', $storagePath . '/framework/views');

    if (env('DB_CONNECTION') === 'mysql') {
        $app['config']->set('database.connections.mysql.host', env('DB_HOST'));
        $app['config']->set('database.connections.mysql.database', env('DB_DATABASE'));
        $app['config']->set('database.connections.mysql.username', env('DB_USERNAME'));
        $app['config']->set('database.connections.mysql.password', env('DB_PASSWORD'));
        $app['config']->set('database.connections.mysql.options', array_filter([
            \PDO::ATTR_TIMEOUT => 20, 
            \PDO::MYSQL_ATTR_SSL_CA => base_path(env('MYSQL_ATTR_SSL_CA', 'database/isrgrootx1.pem')),
            \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // Speed up on AWS
            \PDO::ATTR_EMULATE_PREPARES => true,
        ], fn($value) => $value !== null));
        $app['config']->set('database.connections.mysql.modes', []);
    }

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
