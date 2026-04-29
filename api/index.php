<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vercel Entry Point (Laravel Bridge)
require __DIR__ . '/../vendor/autoload.php';

$isVercel = isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL']) || getenv('VERCEL');

if ($isVercel) {
    // 1. Storage path → /tmp/storage (Vercel has read-only filesystem except /tmp)
    $storagePath = '/tmp/storage';
    $storageDirs = [
        'logs',
        'framework/views',
        'framework/cache/data',
        'framework/sessions',
        'framework/testing',
        'app/public',
    ];
    foreach (array_merge([$storagePath], array_map(fn($d) => "$storagePath/$d", $storageDirs)) as $dir) {
        if (!is_dir($dir)) mkdir($dir, 0777, true);
    }

    // 2. Bootstrap cache → /tmp/bootstrap/cache
    $srcCache  = __DIR__ . '/../bootstrap/cache';
    $tmpCache  = '/tmp/bootstrap/cache';
    if (!is_dir($tmpCache)) mkdir($tmpCache, 0777, true);

    foreach (['packages.php', 'services.php'] as $cacheFile) {
        $dest = "$tmpCache/$cacheFile";
        if (!file_exists($dest) && file_exists("$srcCache/$cacheFile")) {
            copy("$srcCache/$cacheFile", $dest);
        }
    }
}

// 3. Maintenance mode
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// 4. Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

if ($isVercel) {
    $app->useStoragePath('/tmp/storage');
    // Laravel 11/13 handles bootstrap path differently, but this is a safe bridge
    $_SERVER['SCRIPT_NAME'] = '/index.php';
}

use Illuminate\Http\Request;

// 5. Handle Request
try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    echo "<h1>Laravel Error</h1>";
    echo "<p><b>Message:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}


