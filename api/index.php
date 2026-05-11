<?php

// ─────────────────────────────────────────────────────────────────────────────
// E-ASY POS — Vercel Serverless Entry Point
// ─────────────────────────────────────────────────────────────────────────────

// Enable Error Reporting for Debugging (Temporary)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$projectRoot = dirname(__DIR__);

// ── 1. Ensure writable /tmp directories exist ────────────────────────────────
$dirs = [
    '/tmp/bootstrap/cache',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/database',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ── 2. Handle Autoload ───────────────────────────────────────────────────────
if (!file_exists($projectRoot . '/vendor/autoload.php')) {
    die('Vendor autoload file not found. Please run composer install.');
}
require $projectRoot . '/vendor/autoload.php';

// ── 3. Boot Laravel ──────────────────────────────────────────────────────────
$app = require_once $projectRoot . '/bootstrap/app.php';

// Overriding paths for Vercel's read-only filesystem
// Laravel 11+ compatible path overrides
$app->useStoragePath('/tmp/storage');
if (method_exists($app, 'useBootstrapPath')) {
    $app->useBootstrapPath('/tmp/bootstrap');
}

// Ensure the compiled views path is set to /tmp
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// ── 4. SQLite Handling ───────────────────────────────────────────────────────
$srcDb = $projectRoot . '/database/database.sqlite';
$tmpDb = '/tmp/database/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) {
    copy($srcDb, $tmpDb);
}
if (file_exists($tmpDb)) {
    $_ENV['DB_DATABASE'] = $tmpDb;
    putenv("DB_DATABASE=$tmpDb");
}

// ── 5. Run Kernel ────────────────────────────────────────────────────────────
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);