<?php

// ─────────────────────────────────────────────────────────────────────────────
// E-ASY POS — Vercel Serverless Entry Point (Stabilized Version)
// ─────────────────────────────────────────────────────────────────────────────

$projectRoot = dirname(__DIR__);

// 1. Create essential directories in /tmp
if (!is_dir('/tmp/storage/framework/views')) {
    @mkdir('/tmp/storage/framework/views', 0755, true);
}

// 2. Autoload
require $projectRoot . '/vendor/autoload.php';

// 3. Boot Laravel
$app = require_once $projectRoot . '/bootstrap/app.php';

// Essential path overrides for Vercel
$app->useStoragePath('/tmp/storage');

// 4. SQLite Handling (Fast Copy)
$srcDb = $projectRoot . '/database/database.sqlite';
$tmpDb = '/tmp/database/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) {
    @copy($srcDb, $tmpDb);
}
if (file_exists($tmpDb)) {
    putenv("DB_DATABASE=$tmpDb");
    $_ENV['DB_DATABASE'] = $tmpDb;
}

// 5. Run Kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();
$kernel->terminate($request, $response);