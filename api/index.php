<?php

use Illuminate\Http\Request;

// 1. Load Application
require __DIR__ . '/../vendor/autoload.php';

// 2. Handle Fallback APP_KEY for Vercel
if (!env('APP_KEY')) {
    putenv('APP_KEY=base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=');
    $_ENV['APP_KEY'] = 'base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=';
}

$app = require __DIR__ . '/../bootstrap/app.php';

// 3. Configure Storage for Vercel
$storagePath = '/tmp';
$app->useStoragePath($storagePath);

// 4. Force View Compilation Path
$app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\LoadConfiguration::class, function($app) use ($storagePath) {
    $app['config']->set('view.compiled', $storagePath);
});

// 5. Handle Request
$app->handleRequest(Illuminate\Http\Request::capture());

