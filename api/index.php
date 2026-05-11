<?php

// ─────────────────────────────────────────────────────────────────────────────
// E-ASY POS — Vercel Diagnostic Bootloader
// ─────────────────────────────────────────────────────────────────────────────

try {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $projectRoot = dirname(__DIR__);

    // 1. Setup writable directories
    if (!is_dir('/tmp/storage/framework/views')) {
        @mkdir('/tmp/storage/framework/views', 0755, true);
    }
    if (!is_dir('/tmp/storage/framework/sessions')) {
        @mkdir('/tmp/storage/framework/sessions', 0755, true);
    }

    // 2. Autoload
    if (!file_exists($projectRoot . '/vendor/autoload.php')) {
        die("Autoload missing at: " . $projectRoot . '/vendor/autoload.php');
    }
    require $projectRoot . '/vendor/autoload.php';

    // 3. Boot Laravel
    $app = require_once $projectRoot . '/bootstrap/app.php';

    // Path overrides
    if (method_exists($app, 'useStoragePath')) {
        $app->useStoragePath('/tmp/storage');
    }

    // 4. SQLite Handling
    $srcDb = $projectRoot . '/database/database.sqlite';
    $tmpDb = '/tmp/database/database.sqlite';
    if (!file_exists($tmpDb) && file_exists($srcDb)) {
        if (!is_dir('/tmp/database')) @mkdir('/tmp/database', 0755, true);
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

} catch (Throwable $e) {
    echo "<h1>Boot Failure</h1>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}