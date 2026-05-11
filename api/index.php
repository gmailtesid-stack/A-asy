<?php

// ─────────────────────────────────────────────────────────────────────────────
// E-ASY POS — Vercel Serverless Entry Point
// Optimised for Hobby-plan cold starts (target: < 5 s TTFB on login page)
// ─────────────────────────────────────────────────────────────────────────────

$projectRoot = dirname(__DIR__);

// ── 1. Ensure writable /tmp directories exist ────────────────────────────────
foreach ([
    '/tmp/bootstrap/cache',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/database',
] as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}

// ── 2. Copy pre-compiled bootstrap caches shipped in git → /tmp ─────────────
// These files were generated locally via `php artisan config:cache`,
// `php artisan route:cache`, `php artisan event:cache`, `php artisan view:cache`
// and committed.  Copying them eliminates ALL compilation work on cold start.
$cacheFiles = [
    'config.php',
    'routes-v7.php',
    'services.php',
    'events.php',
    'packages.php',
];
foreach ($cacheFiles as $f) {
    $src = $projectRoot . '/bootstrap/cache/' . $f;
    $dst = '/tmp/bootstrap/cache/' . $f;
    if (!file_exists($dst) && file_exists($src)) {
        copy($src, $dst);
    }
}

// ── 3. Copy pre-compiled Blade views shipped in git → /tmp ──────────────────
$srcViews = $projectRoot . '/storage/framework/views';
$tmpViews = '/tmp/storage/framework/views';
if (is_dir($srcViews)) {
    foreach (glob($srcViews . '/*.php') as $compiled) {
        $dest = $tmpViews . '/' . basename($compiled);
        if (!file_exists($dest)) {
            copy($compiled, $dest);
        }
    }
}

// ── 4. Copy SQLite database → /tmp (only on cold start) ─────────────────────
$srcDb = $projectRoot . '/database/database.sqlite';
$tmpDb = '/tmp/database/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) {
    copy($srcDb, $tmpDb);
}
if (file_exists($tmpDb)) {
    putenv('DB_DATABASE=' . $tmpDb);
    $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = $tmpDb;
}

// ── 5. Boot Laravel ──────────────────────────────────────────────────────────
require $projectRoot . '/vendor/autoload.php';

$app = require_once $projectRoot . '/bootstrap/app.php';

// Redirect bootstrap/cache & storage to /tmp (read-only filesystem on Vercel)
$app->useBootstrapPath('/tmp/bootstrap');
$app->useStoragePath('/tmp/storage');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);