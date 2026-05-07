<?php
// Trigger Deploy: Optimization & Fix Login 500

use Illuminate\Http\Request;

// 🔥 Resilience Patch: Suntikkan ENV secara manual untuk Vercel agar tidak 500
$fallbacks = [
    'APP_KEY'        => 'base64:cT3wN1uicXKYsFj04rvpanIYMkb8uQ4YJXThCFE0iIE=',
    'APP_DEBUG'      => 'true',
    'DB_CONNECTION'  => 'mysql',
    'DB_HOST'        => 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com',
    'DB_PORT'        => '4000',
    'DB_DATABASE'    => 'easy_pos',
    'DB_USERNAME'    => '2a8pPc78jTqTKYA.root',
    'DB_PASSWORD'    => 'bmQD3Hzj4umm52vV',
    'SESSION_DRIVER' => 'database',
    'CACHE_STORE'    => 'database',
    'QUEUE_CONNECTION' => 'database',
    'MYSQL_ATTR_SSL_CA' => 'database/isrgrootx1.pem',
];

foreach ($fallbacks as $key => $value) {
    if (!getenv($key)) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// 1. BERSIHKAN CACHE LAMA (HANYA JIKA BISA DITULIS)
$cachePath = __DIR__ . '/../bootstrap/cache';
foreach (['config.php', 'services.php', 'packages.php', 'routes.php'] as $file) {
    $fullPath = "$cachePath/$file";
    if (file_exists($fullPath) && is_writable($fullPath)) {
        @unlink($fullPath);
    }
}

// 2. BUAT FOLDER STORAGE DI /tmp
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0777, true);
}
foreach (['/framework/views', '/framework/cache/data', '/framework/sessions', '/framework/cache', '/app/public'] as $path) {
    if (!is_dir($storagePath . $path)) {
        @mkdir($storagePath . $path, 0777, true);
    }
}

// 3. Alihkan Cache Laravel ke /tmp (PENTING UNTUK VERCEL)
putenv('TMPDIR=/tmp');
putenv('APP_PACKAGES_CACHE=' . $storagePath . '/framework/packages.php');
putenv('APP_SERVICES_CACHE=' . $storagePath . '/framework/services.php');
putenv('APP_CONFIG_CACHE=' . $storagePath . '/framework/config.php');
putenv('APP_ROUTES_CACHE=' . $storagePath . '/framework/routes.php');
putenv('APP_EVENTS_CACHE=' . $storagePath . '/framework/events.php');

// 4. Alihkan Log & Debug
putenv('LOG_CHANNEL=stderr');
// APP_DEBUG otomatis mengikuti env, default false untuk keamanan
if (!getenv('APP_DEBUG')) {
    putenv('APP_DEBUG=false');
}

// Supresi khusus untuk tempnam() warning di Vercel
set_error_handler(function ($errno, $errstr) {
    return (strpos($errstr, 'tempnam()') !== false);
}, E_WARNING | E_NOTICE);

try {
    // 5. Load Autoloader & Bootstrap
    require __DIR__ . '/../vendor/autoload.php';
    
    $app = require __DIR__ . '/../bootstrap/app.php';

    // 6. Force Bootstrap Laravel
    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ]);

    $app->useStoragePath($storagePath);

    // Paksa session ke database untuk Vercel (karena /tmp tidak shared)
    $app['config']->set('session.driver', 'database');

    // Trust Proxies untuk Vercel HTTPS agar session cookie aman
    $app['config']->set('trustedproxy.proxies', ['*']);

    // Pastikan view compiled path juga mengarah ke /tmp
    $app['config']->set('view.compiled', $storagePath . '/framework/views');

    // KONFIGURASI SSL UNTUK TIDB CLOUD (Dinamis via ENV)
    if (env('DB_CONNECTION') === 'mysql') {
        $app['config']->set('database.connections.mysql.host', env('DB_HOST'));
        $app['config']->set('database.connections.mysql.read.host', [env('DB_HOST')]);
        $app['config']->set('database.connections.mysql.write.host', [env('DB_HOST')]);
        $app['config']->set('database.connections.mysql.port', env('DB_PORT'));
        $app['config']->set('database.connections.mysql.database', env('DB_DATABASE'));
        $app['config']->set('database.connections.mysql.username', env('DB_USERNAME'));
        $app['config']->set('database.connections.mysql.password', env('DB_PASSWORD'));

        $app['config']->set('database.connections.mysql.options', array_filter([
            \PDO::ATTR_TIMEOUT => 3, // Force fail fast instead of 504 Timeout
            \PDO::MYSQL_ATTR_SSL_CA => base_path(env('MYSQL_ATTR_SSL_CA', 'database/isrgrootx1.pem')),
            \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
        ], fn($value) => $value !== null));
        
        // AUTO-FIX removed as we are now using easy_pos
        // if (env('DB_DATABASE') === 'sys' || $app['config']->get('database.connections.mysql.database') === 'sys') {
        //     $app['config']->set('database.connections.mysql.database', 'easy_pos');
        // }
    }

    // Cek APP_KEY
    if (!env('APP_KEY')) {
        die("🚨 ERROR: APP_KEY belum diset di Vercel Environment Variables!");
    }

    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['HTTPS'] = 'on'; 

    if (isset($_GET['test_db'])) {
        echo "<pre>Testing direct PDO connection to TiDB...\n";
        $host = env('DB_HOST');
        $port = env('DB_PORT');
        $db = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $ca = base_path(env('MYSQL_ATTR_SSL_CA', 'database/isrgrootx1.pem'));
        
        echo "Host: $host\nDB: $db\nUser: $user\nCA Path: $ca\nCA Exists: " . (file_exists($ca) ? 'YES' : 'NO') . "\n\n";
        
        try {
            $options = [
                \PDO::ATTR_TIMEOUT => 3, // 3 seconds timeout to avoid 504
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_SSL_CA => file_exists($ca) ? $ca : null,
                \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
            ];
            
            $pdo = new \PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass, $options);
            echo "✅ CONNECTION SUCCESSFUL!\n";
            $stmt = $pdo->query('SHOW TABLES');
            echo "Tables:\n";
            foreach ($stmt->fetchAll(\PDO::FETCH_COLUMN) as $table) {
                echo "- $table\n";
            }
        } catch (\PDOException $e) {
            echo "❌ CONNECTION FAILED:\n";
            echo $e->getMessage() . "\n";
        }
        exit;
    }

    // ─── 7. Step-by-Step Initialization (To avoid 504 Timeout) ───
    if (isset($_GET['migrate']) || isset($_GET['seed']) || isset($_GET['wipe'])) {
        $app['config']->set('session.driver', 'array'); // Paksa pakai memori saja, jangan cari tabel
    }

    if (isset($_GET['wipe'])) {
        echo "<pre>🧹 Super Wiping Database (Unlocking Constraints)...\n";
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
        foreach($tables as $table) {
            $name = array_values((array)$table)[0];
            echo "Dropping $name...\n";
            \Illuminate\Support\Facades\Schema::dropIfExists($name);
        }
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        echo "✅ Database is truly EMPTY now. Run ?migrate=1\n</pre>";
        exit;
    }

    if (isset($_GET['migrate'])) {
        echo "<pre>🛠 Step 1: Running Migration Batch (To avoid timeout)...\n";
        $migrator = $app->make('migrator');
        
        // Pastikan tabel "Buku Catatan" migrations ada dulu
        if (!$migrator->repositoryExists()) {
            echo "Creating migrations table...\n";
            $migrator->getRepository()->createRepository();
        }

        $files = $migrator->getMigrationFiles($migrator->paths());
        $ran = $migrator->getRepository()->getRan();
        $pending = array_diff(array_keys($files), $ran);
        
        if (empty($pending)) {
            echo "✅ All Migrations Completed! Now run ?seed=1\n";
        } else {
            $batchSize = 5; // Kita cicil 5 tabel saja sekali jalan
            $toRun = array_slice($pending, 0, $batchSize);
            foreach ($toRun as $file) {
                echo "Running: $file...\n";
                $migrator->runUp($files[$file], $migrator->getRepository()->getNextBatchNumber(), false);
            }
            echo "\n⏳ Batch Done! Refresh this page to run the next " . $batchSize . " migrations.\n";
            echo "Remaining: " . (count($pending) - count($toRun)) . " tables.\n";
        }
        exit;
    }
    if (isset($_GET['seed'])) {
        echo "<pre>🌱 Step 2: Running Seeders...\n";
        \Illuminate\Database\Eloquent\Model::unguard();
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        echo \Illuminate\Support\Facades\Artisan::output();
        echo "\n✅ Seeding Done! You can now LOGIN.</pre>";
        exit;
    }

    // 8. Handle Request
    // echo "<!-- [DEBUG] Starting Kernel -->\n";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // echo "<!-- [DEBUG] Capturing Request -->\n";
    $request = Request::capture();

    // echo "<!-- [DEBUG] Handling Request -->\n";
    $response = $kernel->handle($request);
    
    // echo "<!-- [DEBUG] Sending Response -->\n";
    $response->send();
    
    // echo "<!-- [DEBUG] Terminating Kernel -->\n";
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    echo "<div style='background:#000;color:#0f0;padding:20px;border:5px solid red;font-family:monospace;'>";
    echo "<h1>🚨 SYSTEM CRITICAL ERROR</h1>";
    echo "<h3>" . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    echo "<hr><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}


