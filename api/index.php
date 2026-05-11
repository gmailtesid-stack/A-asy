<?php
// Minimal test - step by step debug
echo "<pre>\n";
echo "Step 1: PHP " . PHP_VERSION . " OK\n";

// Step 2: Check if vendor exists
$vendorPath = __DIR__ . '/../vendor/autoload.php';
echo "Step 2: vendor/autoload.php " . (file_exists($vendorPath) ? "EXISTS" : "MISSING") . "\n";

// Step 3: Check SQLite
$dbSrc = dirname(__DIR__) . '/database/database.sqlite';
echo "Step 3: database.sqlite " . (file_exists($dbSrc) ? "EXISTS (" . filesize($dbSrc) . " bytes)" : "MISSING") . "\n";

// Step 4: Check /tmp writable
$testFile = '/tmp/write_test_' . time() . '.txt';
$written = @file_put_contents($testFile, 'test');
echo "Step 4: /tmp writable: " . ($written !== false ? "YES" : "NO") . "\n";
if ($written !== false) @unlink($testFile);

// Step 5: Check extensions
echo "Step 5: PDO loaded: " . (extension_loaded('pdo') ? "YES" : "NO") . "\n";
echo "Step 5: pdo_sqlite loaded: " . (extension_loaded('pdo_sqlite') ? "YES" : "NO") . "\n";

// Step 6: Check APP_KEY env
$appKey = getenv('APP_KEY');
echo "Step 6: APP_KEY set: " . ($appKey ? "YES (len=" . strlen($appKey) . ")" : "NO - MISSING!") . "\n";

echo "\nAll steps done. If you see this, PHP runtime works.\n";
echo "</pre>\n";