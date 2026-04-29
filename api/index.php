<?php

use Illuminate\Http\Request;

// 1. Force absolute path for autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. Setup Storage & Cache for Serverless (MUST BE /tmp)
$storagePath = '/tmp/storage';
$cachePath = '/tmp/bootstrap/cache';

foreach ([$storagePath . '/framework/views', $storagePath . '/framework/sessions', $storagePath . '/framework/cache', $cachePath] as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}

// 3. Bootstrap the Application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Overrides for Vercel
$app->useStoragePath($storagePath);
$app->bind('path.public', function() { return __DIR__ . '/../public'; });

// 5. Handle Request
try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    header('Content-Type: text/html', true, 500);
    echo "<h1>🚨 Error Fatal Terdeteksi</h1>";
    echo "<p><b>Pesan:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>Lokasi:</b> " . $e->getFile() . " L" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}





