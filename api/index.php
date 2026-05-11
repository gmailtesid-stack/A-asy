<?php

// ── Vercel Laravel 13 Ultra-Bootloader ──
ini_set('display_errors', 1);
error_reporting(E_ALL);

$root = __DIR__ . '/..';
require $root . '/vendor/autoload.php';

// Create storage structure in /tmp immediately
$tmp = '/tmp/storage';
foreach (['/framework/views', '/framework/sessions', '/framework/cache', '/logs'] as $d) {
    if (!is_dir($tmp . $d)) @mkdir($tmp . $d, 0755, true);
}

// Boot the application
$app = require_once $root . '/bootstrap/app.php';

// Force overrides
$app->useStoragePath($tmp);

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    echo "<h1>Critical failure during request handling</h1>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}