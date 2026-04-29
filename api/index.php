<?php

// 1. PAKSA TAMPILKAN ERROR (RAW)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. BUAT FOLDER STORAGE
$storagePath = '/tmp/storage';
foreach (['', '/framework/views', '/framework/cache/data', '/framework/sessions'] as $path) {
    if (!is_dir($storagePath . $path)) mkdir($storagePath . $path, 0777, true);
}

try {
    // 3. Load Autoloader & Bootstrap
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 4. Force Debug & Storage
    $app->useStoragePath($storagePath);
    
    // Matikan Handler Laravel (Paksa Error Keluar)
    $app->instance(\Illuminate\Contracts\Debug\ExceptionHandler::class, new class($app) extends \Illuminate\Foundation\Exceptions\Handler {
        public function __construct($app) { parent::__construct($app); }
        public function render($request, \Throwable $e) { throw $e; }
    });

    // 5. Handle Request
    use Illuminate\Http\Request;
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Request::capture());
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    // TAMPILKAN ERROR APA ADANYA
    echo "<div style='background:black;color:lime;padding:20px;font-family:monospace;border:3px solid red;'>";
    echo "<h1>🚨 DETEKSI ERROR FATAL</h1>";
    echo "<h3>" . $e->getMessage() . "</h3>";
    echo "<p>File: " . $e->getFile() . " (Baris: " . $e->getLine() . ")</p>";
    echo "<hr><pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
