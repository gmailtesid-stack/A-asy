<?php

// ── Vercel Resilient Bootloader ──
ini_set('display_errors', 1);
error_reporting(E_ALL);

$projectRoot = __DIR__ . '/..';

// 1. Storage & Cache Setup (Crucial for Vercel)
$tmpStorage = '/tmp/storage';
$subDirs = [
    '',
    '/framework',
    '/framework/views',
    '/framework/sessions',
    '/framework/cache',
    '/app',
    '/logs'
];

foreach ($subDirs as $dir) {
    $fullPath = $tmpStorage . $dir;
    if (!is_dir($fullPath)) {
        @mkdir($fullPath, 0755, true);
    }
}

// 2. Load Dependencies
require $projectRoot . '/vendor/autoload.php';

// 3. Initialize Laravel
$app = require_once $projectRoot . '/bootstrap/app.php';

// Path Overrides
$app->useStoragePath($tmpStorage);

// 4. Database Setup (SQLite)
$srcDb = $projectRoot . '/database/database.sqlite';
$tmpDb = '/tmp/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) {
    @copy($srcDb, $tmpDb);
}
if (file_exists($tmpDb)) {
    putenv("DB_DATABASE=$tmpDb");
    $_ENV['DB_DATABASE'] = $tmpDb;
}

// 5. Execute Kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
$response->send();
$kernel->terminate($request, $response);