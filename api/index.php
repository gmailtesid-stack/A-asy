<?php

$projectRoot = dirname(__DIR__);

// ─── Tmp dirs ────────────────────────────────────────────────────────────────
// Minimize directory creation. Only create what's absolutely necessary.
// /tmp is shared across warm invocations on Vercel.
if (!is_dir('/tmp/storage/framework/views')) {
    mkdir('/tmp/storage/framework/views', 0755, true);
    mkdir('/tmp/storage/framework/cache/data', 0755, true);
    mkdir('/tmp/storage/framework/sessions', 0755, true);
    mkdir('/tmp/bootstrap/cache', 0755, true);
    mkdir('/tmp/storage/logs', 0755, true);
}

// ─── Pre-compiled packages & services speedup ──────────────────────────────
// Pre-generated files (via `php artisan package:discover` and `services:cache`)
// ship in git. Copying them eliminates scan overhead on every cold start.
foreach (['packages.php', 'services.php'] as $file) {
    $src = $projectRoot . '/bootstrap/cache/' . $file;
    $tmp = '/tmp/bootstrap/cache/' . $file;
    if (!file_exists($tmp) && file_exists($src)) {
        if (!is_dir('/tmp/bootstrap/cache')) mkdir('/tmp/bootstrap/cache', 0755, true);
        copy($src, $tmp);
    }
}

// ─── SQLite: Copy to /tmp (writable) ─────────────────────────────────────────
$srcDb = $projectRoot . '/database/database.sqlite';
$tmpDb = '/tmp/database/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) {
    if (!is_dir('/tmp/database')) mkdir('/tmp/database', 0755, true);
    copy($srcDb, $tmpDb);
}

// Ensure Laravel uses the writable DB path in /tmp
if (file_exists($tmpDb)) {
    putenv('DB_DATABASE=' . $tmpDb);
    $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = $tmpDb;
}

// ─── Composer ───────────────────────────────────────────────────────────────
require $projectRoot . '/vendor/autoload.php';

// ─── Bootstrap Laravel ──────────────────────────────────────────────────────
$app = require_once $projectRoot . '/bootstrap/app.php';

// ─── Redirect bootstrap/cache & storage to /tmp ─────────────────────────────
// On Vercel, project filesystem is read-only.
$app->useBootstrapPath('/tmp/bootstrap');
$app->useStoragePath('/tmp/storage');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);