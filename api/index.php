<?php

// ─── Tmp dirs ────────────────────────────────────────────────────────────────
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

// ─── SQLite: Copy to /tmp (writable) ─────────────────────────────────────────
$srcDb = dirname(__DIR__) . '/database/database.sqlite';
$tmpDb = '/tmp/database/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) copy($srcDb, $tmpDb);
if (file_exists($tmpDb)) {
    putenv('DB_DATABASE=' . $tmpDb);
    $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = $tmpDb;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// ─── KEY FIX: Redirect bootstrap/cache & storage to /tmp ─────────────────────
// On Vercel, project filesystem is read-only. Laravel's PackageManifest
// (auto-discovery) writes packages.php to bootstrap/cache/ - if it can't write,
// ViewServiceProvider is never registered → 'view' binding missing → 500 error.
$app->useBootstrapPath('/tmp/bootstrap');
$app->useStoragePath('/tmp/storage');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);