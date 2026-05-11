<?php

// ─── Capture even fatal/segfault errors ──────────────────────────────────────
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

register_shutdown_function(function () {
    $error = error_get_last();
    $buffered = ob_get_clean();

    // Force a 200 so browser shows our output even on fatal error
    if (!headers_sent()) {
        http_response_code(200);
        header('Content-Type: text/plain; charset=utf-8');
    }

    echo "=== SHUTDOWN ===\n";
    echo "Error: " . ($error ? print_r($error, true) : "none") . "\n\n";

    if ($buffered) {
        echo "=== BUFFERED OUTPUT ===\n";
        echo strip_tags($buffered) . "\n";
    }
});

// ─── Step-by-step Laravel boot test ─────────────────────────────────────────
echo "BOOT 1: PHP " . PHP_VERSION . "\n";

// Create /tmp dirs
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
echo "BOOT 2: /tmp dirs created\n";

// Copy SQLite
$srcDb = dirname(__DIR__) . '/database/database.sqlite';
$tmpDb = '/tmp/database/database.sqlite';
if (!file_exists($tmpDb) && file_exists($srcDb)) copy($srcDb, $tmpDb);
if (file_exists($tmpDb)) {
    putenv('DB_DATABASE=' . $tmpDb);
    $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = $tmpDb;
}
echo "BOOT 3: SQLite " . (file_exists($tmpDb) ? "copied" : "src missing") . "\n";

// Load autoloader
echo "BOOT 4: loading autoloader...\n";
ob_flush(); flush();
require __DIR__ . '/../vendor/autoload.php';
echo "BOOT 5: autoloader OK\n";
ob_flush(); flush();

// Boot Laravel app
echo "BOOT 6: bootstrapping app...\n";
ob_flush(); flush();
$app = require_once __DIR__ . '/../bootstrap/app.php';
echo "BOOT 7: app created\n";
ob_flush(); flush();

// Handle request
echo "BOOT 8: making kernel...\n";
ob_flush(); flush();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "BOOT 9: handling request...\n";
ob_flush(); flush();

$response = $kernel->handle($request = Illuminate\Http\Request::capture());
echo "BOOT 10: response ready (status=" . $response->getStatusCode() . ")\n";

// At this point if all good, clear buffer and send real response
ob_end_clean();
$response->send();
$kernel->terminate($request, $response);