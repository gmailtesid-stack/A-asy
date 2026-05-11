<?php
echo "PHP Is Alive. ";
$root = dirname(__DIR__);
$autoload = $root . '/vendor/autoload.php';
echo "Checking: $autoload ... ";
if (file_exists($autoload)) {
    echo "FOUND! ";
    try {
        require $autoload;
        echo "LOADED! ";
        $app = require_once $root . '/bootstrap/app.php';
        echo "BOOTED! ";
        $app->useStoragePath('/tmp/storage');
        if (!is_dir('/tmp/storage/framework/views')) @mkdir('/tmp/storage/framework/views', 0755, true);
        echo "READY!";
    } catch (\Throwable $e) {
        echo "ERROR: " . $e->getMessage();
    }
} else {
    echo "MISSING!";
}