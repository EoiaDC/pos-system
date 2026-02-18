<?php

require_once __DIR__ . '/Connection.php';
require_once __DIR__ . '/DB.php';

use POS\Database\DB;

// Load database configuration
$config = require __DIR__ . '/../../config/database.php';
POS\Database\Connection::loadConfig($config);

// Now test the connection
$row = DB::fetch("SELECT 1 AS ok");

if ($row && (int)$row['ok'] === 1) {
    echo "DB OK";
} else {
    echo "DB FAIL";
}
