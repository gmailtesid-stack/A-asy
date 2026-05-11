<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$root = __DIR__ . '/..';

// 1. Setup Vercel Writable Storage
$tmp = '/tmp/storage';
foreach (['/framework/views', '/framework/sessions', '/framework/cache', '/logs'] as $d) {
    if (!is_dir($tmp . $d)) @mkdir($tmp . $d, 0755, true);
}

// 2. Register Autoloader
require $root . '/vendor/autoload.php';

// 3. Bootstrap Laravel
$app = require_once $root . '/bootstrap/app.php';

// 4. Path Overrides
$app->useStoragePath($tmp);

// 5. Database Handling (SQLite)
$srcDb = $root . '/database/database.sqlite';
$tmpDb = '/tmp/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) {
    @copy($srcDb, $tmpDb);
}
if (file_exists($tmpDb)) {
    putenv("DB_DATABASE=$tmpDb");
    $_ENV['DB_DATABASE'] = $tmpDb;
}

// 6. Handle Request (Modern Laravel 11/13 Way)
$app->handleRequest(Request::capture());