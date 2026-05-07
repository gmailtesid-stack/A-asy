<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = DB::select('SHOW TABLES');
foreach ($tables as $t) {
    $tname = array_values((array)$t)[0];
    echo '=TABLE=' . $tname . PHP_EOL;
    $cols = DB::select('SHOW FULL COLUMNS FROM `' . $tname . '`');
    foreach ($cols as $c) {
        $default = $c->Default ?? 'NULL';
        $comment = $c->Comment ?? '';
        echo $c->Field . '|' . $c->Type . '|' . $c->Null . '|' . $c->Key . '|' . $default . '|' . $comment . PHP_EOL;
    }
    // row count
    $cnt = DB::selectOne('SELECT COUNT(*) as cnt FROM `' . $tname . '`');
    echo '=ROWS=' . $cnt->cnt . PHP_EOL;
    echo PHP_EOL;
}
