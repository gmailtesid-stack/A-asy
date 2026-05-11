<?php

// ─────────────────────────────────────────────────────────────────────────────
// E-ASY POS — Vercel Serverless Entry Point (Laravel 13.x-dev Compatible)
// ─────────────────────────────────────────────────────────────────────────────

try {
    // Disable direct display for production look, but keep internal reporting
    ini_set('display_errors', 0);
    error_reporting(E_ALL);

    $projectRoot = dirname(__DIR__);

    // ── 1. Setup writable directories ────────────────────────────────────────
    $dirs = [
        '/tmp/bootstrap/cache',
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
        '/tmp/database',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    // ── 2. Autoload ───────────────────────────────────────────────────────────
    if (!file_exists($projectRoot . '/vendor/autoload.php')) {
        throw new Exception('Vendor autoload file not found. Run composer install.');
    }
    require $projectRoot . '/vendor/autoload.php';

    // ── 3. Boot Laravel ───────────────────────────────────────────────────────
    $app = require_once $projectRoot . '/bootstrap/app.php';

    // Safe path overrides for Vercel
    if (method_exists($app, 'useStoragePath')) {
        $app->useStoragePath('/tmp/storage');
    }
    if (method_exists($app, 'useBootstrapPath')) {
        $app->useBootstrapPath('/tmp/bootstrap');
    }

    // Force compiled views to /tmp
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

    // ── 4. SQLite Handling ───────────────────────────────────────────────────
    $srcDb = $projectRoot . '/database/database.sqlite';
    $tmpDb = '/tmp/database/database.sqlite';
    if (!file_exists($tmpDb) && file_exists($srcDb)) {
        copy($srcDb, $tmpDb);
    }
    if (file_exists($tmpDb)) {
        $_ENV['DB_DATABASE'] = $tmpDb;
        putenv("DB_DATABASE=$tmpDb");
    }

    // ── 5. Run Kernel ────────────────────────────────────────────────────────
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    // Catch everything and display it
    http_response_code(500);
    echo "<h1>Laravel Boot Error</h1>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}