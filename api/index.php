<?php

use Illuminate\Http\Request;

// 1. Storage Setup for Vercel
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    @mkdir($storagePath, 0777, true);
    @mkdir($storagePath . '/framework/views', 0777, true);
}

// 2. Load Application
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

// 3. Configure Storage and Vercel specific settings
$app->useStoragePath($storagePath);

$app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\LoadConfiguration::class, function($app) use ($storagePath) {
    // Ensure views are compiled to /tmp on Vercel
    if (env('VERCEL')) {
        $app['config']->set('view.compiled', $storagePath . '/framework/views');
    }
    
    // Add SSL support for TiDB if using MySQL connection
    if ($app['config']->get('database.default') === 'mysql') {
        $app['config']->set('database.connections.mysql.options', array_filter([
            PDO::ATTR_TIMEOUT => 5,
            PDO::MYSQL_ATTR_SSL_CA => file_exists(base_path('database/isrgrootx1.pem')) ? base_path('database/isrgrootx1.pem') : null,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ]));
    }
});

// 4. Handle Request
try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    echo "<h1>Fatal Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

