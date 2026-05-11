<?php

use Illuminate\Http\Request;

try {
    define('LARAVEL_START', microtime(true));

    $root = __DIR__ . '/..';
    require $root . '/vendor/autoload.php';

    $app = require_once $root . '/bootstrap/app.php';

    // Path Overrides for Vercel
    $tmp = '/tmp/storage';
    foreach (['/framework/views', '/framework/sessions', '/framework/cache', '/logs'] as $d) {
        if (!is_dir($tmp . $d)) @mkdir($tmp . $d, 0755, true);
    }
    $app->useStoragePath($tmp);

    // Database Handling (SQLite)
    $srcDb = $root . '/database/database.sqlite';
    $tmpDb = '/tmp/database.sqlite';
    if (!file_exists($tmpDb) && file_exists($srcDb)) {
        @copy($srcDb, $tmpDb);
    }
    if (file_exists($tmpDb)) {
        putenv("DB_DATABASE=$tmpDb");
        $_ENV['DB_DATABASE'] = $tmpDb;
    }

    // Handle Request with detailed error reporting
    $app->handleRequest(Request::capture());

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Laravel 13 Runtime Error</h1>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}