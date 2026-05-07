<?php
// Trigger Deploy: Optimization & Fix Login 500

use Illuminate\Http\Request;

// 🔥 Resilience Patch: Suntikkan ENV secara manual untuk Vercel agar tidak 500
$fallbacks = [
    'APP_KEY'        => 'base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=',
    'APP_DEBUG'      => 'true',
    'DB_CONNECTION'  => 'mysql',
    'DB_HOST'        => 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com',
    'DB_PORT'        => '4000',
    'DB_DATABASE'    => 'easy_pos',
    'DB_USERNAME'    => '2a8pPc78jTqTKYA.root',
    'DB_PASSWORD'    => 'bmQD3Hzj4umm52vV',
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
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0777, true);
}
foreach (['/framework/views', '/framework/cache/data', '/framework/sessions', '/framework/cache', '/app/public'] as $path) {
    if (!is_dir($storagePath . $path)) {
        @mkdir($storagePath . $path, 0777, true);
    }
}

// 3. Alihkan Cache Laravel ke /tmp (PENTING UNTUK VERCEL)
putenv('TMPDIR=/tmp');
putenv('APP_PACKAGES_CACHE=' . $storagePath . '/framework/packages.php');
putenv('APP_SERVICES_CACHE=' . $storagePath . '/framework/services.php');
putenv('APP_CONFIG_CACHE=' . $storagePath . '/framework/config.php');
putenv('APP_ROUTES_CACHE=' . $storagePath . '/framework/routes.php');
putenv('APP_EVENTS_CACHE=' . $storagePath . '/framework/events.php');

// 4. Alihkan Log & Debug
putenv('LOG_CHANNEL=stderr');
// APP_DEBUG otomatis mengikuti env, default false untuk keamanan
if (!getenv('APP_DEBUG')) {
    putenv('APP_DEBUG=false');
}

// Supresi khusus untuk tempnam() warning di Vercel
set_error_handler(function ($errno, $errstr) {
    return (strpos($errstr, 'tempnam()') !== false);
}, E_WARNING | E_NOTICE);

try {
    // 5. Load Autoloader & Bootstrap
    require __DIR__ . '/../vendor/autoload.php';
    
    $app = require __DIR__ . '/../bootstrap/app.php';

    // 6. Step-by-Step Bootstrap (PENTING: Split agar config bisa di-override sebelum BootProviders)
    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    ]);

    $app->useStoragePath($storagePath);

    // Paksa session ke cookie & cache ke array agar Laravel super ringan
    $app['config']->set('session.driver', 'cookie');
    $app['config']->set('cache.default', 'array');
    $app['config']->set('database.connections.mysql.options.' . \PDO::ATTR_PERSISTENT, false);
    $app['config']->set('trustedproxy.proxies', ['*']);

    // Pastikan view compiled path juga mengarah ke /tmp
    $app['config']->set('view.compiled', $storagePath . '/framework/views');

    // KONFIGURASI SSL UNTUK TIDB CLOUD (Dinamis via ENV)
    if (env('DB_CONNECTION') === 'mysql') {
        $app['config']->set('database.connections.mysql.host', env('DB_HOST'));
        $app['config']->set('database.connections.mysql.read.host', [env('DB_HOST')]);
        $app['config']->set('database.connections.mysql.write.host', [env('DB_HOST')]);
        $app['config']->set('database.connections.mysql.port', env('DB_PORT'));
        $app['config']->set('database.connections.mysql.database', env('DB_DATABASE'));
        $app['config']->set('database.connections.mysql.username', env('DB_USERNAME'));
        $app['config']->set('database.connections.mysql.password', env('DB_PASSWORD'));

        $app['config']->set('database.connections.mysql.options', array_filter([
            \PDO::ATTR_TIMEOUT => 30, 
            \PDO::MYSQL_ATTR_SSL_CA => base_path(env('MYSQL_ATTR_SSL_CA', 'database/isrgrootx1.pem')),
            \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
        ], fn($value) => $value !== null));
    }

    // Lanjutkan Bootstrap sisanya
    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ]);

    // Cek APP_KEY
    if (!env('APP_KEY')) {
        die("🚨 ERROR: APP_KEY belum diset di Vercel Environment Variables!");
    }

    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['HTTPS'] = 'on'; 

    // 8. Handle Request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Request::capture());
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    echo "<div style='background:#000;color:#0f0;padding:20px;border:5px solid red;font-family:monospace;'>";
    echo "<h1>🚨 SYSTEM CRITICAL ERROR</h1>";
    echo "<h3>" . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    echo "<hr><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
