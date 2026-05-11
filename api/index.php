<?php

// ─── Debug: Show raw PHP errors (remove after fixing) ────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// ─── Serverless: Create required writable directories in /tmp ───────────────
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
    '/tmp/database',
];
foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ─── SQLite: Copy DB to /tmp (writable) on cold start ────────────────────────
// Vercel project filesystem is read-only; SQLite needs write access for WAL/journal.
$projectRoot = dirname(__DIR__);
$srcDb       = $projectRoot . '/database/database.sqlite';
$tmpDb       = '/tmp/database/database.sqlite';

if (!file_exists($tmpDb) && file_exists($srcDb)) {
    copy($srcDb, $tmpDb);
}

// Override DB path to the writable /tmp copy
if (file_exists($tmpDb)) {
    putenv('DB_DATABASE=' . $tmpDb);
    $_ENV['DB_DATABASE']    = $tmpDb;
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);