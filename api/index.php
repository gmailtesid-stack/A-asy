<?php

// 1. BUAT FOLDER STORAGE DI /tmp (WAJIB PALING ATAS)
$storagePath = '/tmp/storage';
foreach (['', '/framework/views', '/framework/cache/data', '/framework/sessions'] as $path) {
    if (!is_dir($storagePath . $path)) mkdir($storagePath . $path, 0777, true);
}

// 2. Load Autoloader & Bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Handle Request
use Illuminate\Http\Request;
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Request::capture());
$response->send();
$kernel->terminate($request, $response);
