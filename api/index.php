<?php

$projectRoot = dirname(__DIR__);

// ─── Tmp dirs ────────────────────────────────────────────────────────────────
// Create all necessary directories in /tmp. @ hides errors if they already exist.
@mkdir('/tmp/storage/framework/views', 0755, true);
@mkdir('/tmp/storage/framework/cache/data', 0755, true);
@mkdir('/tmp/storage/framework/sessions', 0755, true);
@mkdir('/tmp/bootstrap/cache', 0755, true);
@mkdir('/tmp/storage/logs', 0755, true);
@mkdir('/tmp/database', 0755, true);

// ─── Pre-compiled packages speedup ──────────────────────────────────────────
$srcPkg = $projectRoot . '/bootstrap/cache/packages.php';
$tmpPkg = '/tmp/bootstrap/cache/packages.php';
if (!file_exists($tmpPkg) && file_exists($srcPkg)) {
    copy($srcPkg, $tmpPkg);
}

// ─── SQLite: Copy to /tmp (writable) ─────────────────────────────────────────
$srcDb = $projectRoot . '/database/database.sqlite';
$tmpDb = '/tmp/database/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) {
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