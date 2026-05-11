<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// ─── Tmp dirs ────────────────────────────────────────────────────────────────
foreach ([
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
    '/tmp/database',
] as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}

// ─── SQLite copy ─────────────────────────────────────────────────────────────
$srcDb = dirname(__DIR__) . '/database/database.sqlite';
$tmpDb = '/tmp/database/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) copy($srcDb, $tmpDb);
if (file_exists($tmpDb)) {
    putenv('DB_DATABASE=' . $tmpDb);
    $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = $tmpDb;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// ─── Capture PRIMARY error before Laravel exception handler masks it ──────────
try {
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    http_response_code(200);
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== PRIMARY ERROR ===\n";
    echo get_class($e) . ": " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "=== TRACE ===\n";
    echo $e->getTraceAsString() . "\n\n";

    // Also show previous exception if chained
    if ($prev = $e->getPrevious()) {
        echo "=== CAUSED BY ===\n";
        echo get_class($prev) . ": " . $prev->getMessage() . "\n";
        echo "File: " . $prev->getFile() . ":" . $prev->getLine() . "\n";
    }
    exit;
}

$response->send();
$kernel->terminate($request, $response);