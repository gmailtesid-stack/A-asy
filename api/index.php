<?php
// FINAL PRODUCTION STABLE (BACK TO AWS - SINGAPORE REGION)

use Illuminate\Http\Request;

// 1. Force Production Essentials
$fallbacks = [
    'APP_KEY'        => 'base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=',
    'APP_DEBUG'      => 'true', 
    'DB_CONNECTION'  => 'mysql',
    'DB_HOST'        => 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com',
    'DB_PORT'        => '4000',
    'DB_DATABASE'    => 'easy_pos',
    'DB_USERNAME'    => '3JKwuvbTLoRLXAb.root',
    'DB_PASSWORD'    => '5dql1tIk3FLU6CXW',
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE'    => 'array',
    'MYSQL_ATTR_SSL_CA' => 'database/isrgrootx1.pem',
];

foreach ($fallbacks as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
}

// 2. Storage Setup
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    @mkdir($storagePath, 0777, true);
    @mkdir($storagePath . '/framework/views', 0777, true);
}

// 3. Load Application
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($storagePath);

// 4. Force AWS Connections with low timeout to prevent 504
$app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\LoadConfiguration::class, function($app) use ($storagePath) {
    $app['config']->set('view.compiled', $storagePath . '/framework/views');
    $app['config']->set('database.connections.mysql.options', [
        \PDO::ATTR_TIMEOUT => 5, // STOP WAITING after 5s to prevent 504
        \PDO::MYSQL_ATTR_SSL_CA => base_path('database/isrgrootx1.pem'),
        \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ]);
});

// 5. Handle Request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Request::capture());
$response->send();
$kernel->terminate($request, $response);
