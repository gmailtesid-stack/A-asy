<?php

use Illuminate\Http\Request;

// 1. Storage Setup for Vercel
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    @mkdir($storagePath, 0777, true);
    @mkdir($storagePath . '/framework/views', 0777, true);
}

// 2. Load Application
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

// 3. Configure Storage
$app->useStoragePath($storagePath);

// 4. Handle Request
try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    header('Content-Type: text/plain');
    echo "Fatal Error during handleRequest: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}


