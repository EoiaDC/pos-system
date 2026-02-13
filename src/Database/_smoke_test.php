<?php

require_once __DIR__ . '/Connection.php';
require_once __DIR__ . '/DB.php';

use POS\Database\Connection;
use POS\Database\DB;

$config = [
    'host' => 'localhost',
    'port' => '3306',
    'name' => 'pos_integration',
    'user' => 'pos_user',
    'pass' => 'secure_password_here', // <-- PUT YOUR PASSWORD HERE
    'charset' => 'utf8mb4'
];

try {
    Connection::loadConfig($config);
    $result = DB::fetch('SELECT 1 as ok');

    if ($result && $result['ok'] == 1) {
        echo "<h1 style='color: green;'>✅ DB OK - Connection successful</h1>";
        echo "<p>MySQL Timezone: +08:00 (Asia/Manila)</p>";
        echo "<p>Strict SQL Mode: Enabled</p>";
        echo "<p>Prepared Statements: Real (no emulation)</p>";

        $version = DB::fetch('SELECT VERSION() as version');
        echo "<p>Server: " . $version['version'] . "</p>";
    }
} catch (Exception $e) {
    echo "<h1 style='color: red;'>❌ DB Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
