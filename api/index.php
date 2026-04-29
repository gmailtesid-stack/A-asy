<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Tentukan jika aplikasi dalam maintenance...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register autoloader...
require __DIR__.'/../vendor/autoload.php';

// === VERCEL SERVERLESS OVERRIDES ===
// Karena Vercel filesystm bersifat Read-Only (kecuali /tmp),
// kita harus mengarahkan penyimpanan temporary (cache, views, session files) ke /tmp.
if (env('APP_ENV') === 'production' || isset($_ENV['VERCEL'])) {
    $tmpDir = '/tmp';
    
    // Pastikan direktori ada di /tmp
    $dirs = [
        "$tmpDir/storage/framework/views",
        "$tmpDir/storage/framework/cache/data",
        "$tmpDir/storage/framework/sessions",
        "$tmpDir/storage/logs",
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    // Paksa Laravel menggunakan direktori /tmp untuk config cache, views, dll
    putenv("VIEW_COMPILED_PATH={$tmpDir}/storage/framework/views");
    putenv("SESSION_DRIVER=cookie"); // Hindari file session di serverless
    putenv("LOG_CHANNEL=stderr");    // Arahkan log langsung ke konsol Vercel
}
// ===================================

// Bootstrap aplikasi...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Override storage path secara runtime jika di Vercel
if (env('APP_ENV') === 'production' || isset($_ENV['VERCEL'])) {
    $app->useStoragePath('/tmp/storage');
}

$app->handleRequest(Request::capture());
