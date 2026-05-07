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
    // Fallback to local key if Vercel env is missing (for stability)
    putenv('APP_KEY=base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=');
    $_ENV['APP_KEY'] = 'base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=';
}

putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';
putenv('APP_ENV=local');
$_ENV['APP_ENV'] = 'local';

$app = require __DIR__ . '/../bootstrap/app.php';

// 3. Configure Storage
$app->useStoragePath($storagePath);

// 4. Handle Request with Brute-Force Debugging
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Catch early bootstrap errors before they go to Laravel's handler
    $response = $kernel->handle($request = Request::capture());
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    header('Content-Type: text/plain');
    echo "CAPTURED BOOT ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}




