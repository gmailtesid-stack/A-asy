<?php

use Illuminate\Http\Request;

// 1. Setup Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Load Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 3. Vercel Environment Detection & Storage Fix
$isVercel = true; // Hardcode for this entry point

if ($isVercel) {
    $storagePath = '/tmp/storage';
    $storageDirs = [
        'logs',
        'framework/views',
        'framework/cache/data',
        'framework/sessions',
        'framework/testing',
        'app/public',
    ];
    foreach (array_merge([$storagePath], array_map(fn($d) => "$storagePath/$d", $storageDirs)) as $dir) {
        if (!is_dir($dir)) mkdir($dir, 0777, true);
    }
}

// 4. Bootstrap Laravel
try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    if ($isVercel) {
        $app->useStoragePath('/tmp/storage');
        $_SERVER['SCRIPT_NAME'] = '/index.php';
    }

    // 5. Handle Request
    $app->handleRequest(Request::capture());

} catch (\Throwable $e) {
    // Reveal the ORIGINAL error that caused the crash
    $error = $e;
    // Walk down the chain to find the root cause
    while ($error->getPrevious()) {
        $error = $error->getPrevious();
    }

    header('Content-Type: text/html', true, 500);
    echo "<div style='font-family:sans-serif; padding:20px; border:5px solid red;'>";
    echo "<h1>🚨 Error Akar (Root Cause) Terdeteksi</h1>";
    echo "<p style='font-size:1.2em;'><b>Pesan:</b> " . htmlspecialchars($error->getMessage()) . "</p>";
    echo "<p><b>Lokasi:</b> " . $error->getFile() . " baris " . $error->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#eee; padding:10px; overflow:auto; max-height:400px;'>" . htmlspecialchars($error->getTraceAsString()) . "</pre>";
    echo "</div>";
}




