<?php

use Illuminate\Http\Request;

// 1. Storage Setup for Vercel
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    @mkdir($storagePath, 0777, true);
    @mkdir($storagePath . '/framework/views', 0777, true);
    @mkdir($storagePath . '/framework/sessions', 0777, true);
    @mkdir($storagePath . '/framework/cache', 0777, true);
}

// 2. Load Application
require __DIR__ . '/../vendor/autoload.php';

if (!env('APP_KEY')) {
    die("FATAL: APP_KEY is not set in environment variables!");
}

$app = require __DIR__ . '/../bootstrap/app.php';

// 3. Configure Storage
$app->useStoragePath($storagePath);

// 4. Handle Request with Debugging
try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    header('Content-Type: text/plain');
    echo "BOOTSTRAP ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}



