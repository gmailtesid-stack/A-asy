<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Path Definitions
$root = __DIR__ . '/..';
$storage = '/tmp/storage';
$viewPath = $storage . '/framework/views';

// 2. Create Required Folders Recursively (The "One-Shot" Way)
$dirs = [
    $storage . '/framework/views',
    $storage . '/framework/sessions',
    $storage . '/framework/cache',
    $storage . '/app/public',
    $storage . '/logs',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 3. Force Core Environment Variables (Before Autoload)
putenv("VIEW_COMPILED_PATH=$viewPath");
putenv("SESSION_DRIVER=cookie"); // Force cookie for zero-latency session
putenv("LOG_CHANNEL=stderr");
$_ENV['VIEW_COMPILED_PATH'] = $viewPath;

// 4. Autoload & Bootstrap
require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';

// 5. Hard Override Storage
$app->useStoragePath($storage);

// 6. Database Setup (SQLite)
$srcDb = $root . '/database/database.sqlite';
$tmpDb = '/tmp/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) {
    @copy($srcDb, $tmpDb);
}
if (file_exists($tmpDb)) {
    putenv("DB_DATABASE=$tmpDb");
    $_ENV['DB_DATABASE'] = $tmpDb;
}

// 7. Handle Request
$app->handleRequest(Request::capture());