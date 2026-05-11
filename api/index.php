<?php

// ── Vercel Entry Point — Force Debug Mode ──
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Force Laravel to show everything
putenv('APP_DEBUG=true');
putenv('APP_ENV=local');
$_ENV['APP_DEBUG'] = true;
$_ENV['APP_ENV'] = 'local';

$projectRoot = dirname(__DIR__);

// 1. Storage prep
$storagePath = '/tmp/storage/framework';
foreach (['/views', '/sessions', '/cache'] as $p) {
    if (!is_dir($storagePath . $p)) @mkdir($storagePath . $p, 0755, true);
}

// 2. Load Laravel
require $projectRoot . '/vendor/autoload.php';
$app = require_once $projectRoot . '/bootstrap/app.php';

// 3. Path Overrides
$app->useStoragePath('/tmp/storage');

// 4. DB Override
$tmpDb = '/tmp/database.sqlite';
if (!file_exists($tmpDb)) @copy($projectRoot . '/database/database.sqlite', $tmpDb);
putenv("DB_DATABASE=$tmpDb");

// 5. Execute
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
$response->send();
$kernel->terminate($request, $response);