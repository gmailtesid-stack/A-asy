<?php
try {
    $pdo = new PDO('mysql:host=gateway01.ap-southeast-1.prod.aws.tidbcloud.com;port=4000;dbname=test', 'jm7ETdoFCLactTB.root', 'kwRci29We1dRiVVW', [
        PDO::MYSQL_ATTR_SSL_CA => 'database/isrgrootx1.pem'
    ]);
    $stmt = $pdo->query('SELECT COUNT(*) FROM sessions');
    echo 'Session Count: ' . $stmt->fetchColumn() . PHP_EOL;
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM users');
    echo 'User Count: ' . $stmt->fetchColumn() . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
