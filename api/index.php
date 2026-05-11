<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$root = __DIR__ . '/..';
require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';

// Path Overrides
$app->useStoragePath('/tmp/storage');
if (!is_dir('/tmp/storage/framework/views')) @mkdir('/tmp/storage/framework/views', 0755, true);

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Kernel Execution Error</h1>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}