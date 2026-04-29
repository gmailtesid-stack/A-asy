<?php
// Vercel Entry Point (Laravel Bridge)

define('LARAVEL_START', microtime(true));

$uri = $_SERVER['REQUEST_URI'] ?? '';

// Debug endpoint to inspect URI routing
if (strpos($uri, 'vercel-debug') !== false) {
    header('Content-Type: application/json');
    echo json_encode($_SERVER);
    exit;
}

// ─────────────────────────────────────────────────────────────
//  VERCEL FILE-SYSTEM WORKAROUND
// ─────────────────────────────────────────────────────────────
$isVercel = isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL']) || getenv('VERCEL');

if ($isVercel) {
    // 1. Storage path → /tmp/storage
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

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Apply Vercel path overrides AFTER the app is created
if ($isVercel) {
    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');
}

// Fix Vercel's SCRIPT_NAME so Laravel routes /api correctly
if ($isVercel) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
}

use Illuminate\Http\Request;

// Handle the Request
$app->handleRequest(Request::capture());
