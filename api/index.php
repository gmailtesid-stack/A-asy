<?php

use Illuminate\Http\Request;

// 1. Load Application
require __DIR__ . '/../vendor/autoload.php';

// 2. Handle Fallback APP_KEY for Vercel
if (!env('APP_KEY')) {
    putenv('APP_KEY=base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=');
    $_ENV['APP_KEY'] = 'base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=';
}

$app = require __DIR__ . '/../bootstrap/app.php';

// 3. Configure Storage Path
$app->useStoragePath('/tmp');

// 4. Handle Request with Raw Debug
try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    header('Content-Type: text/plain');
    echo "VERCEL BOOT ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}



