<?php
// Use require_once to prevent multiple includes
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/src/Database/Connection.php';
require_once __DIR__ . '/src/Database/DB.php';

use POS\Database\Connection;
use POS\Database\DB;

$config = require __DIR__ . '/config/database.php';
Connection::loadConfig($config);

echo "🚀 Running migrations...\n\n";

$files = glob(__DIR__ . '/migrations/*.php');
sort($files);

foreach ($files as $file) {
    echo "▶️  " . basename($file) . "... ";
    try {
        require_once $file; // use require_once to avoid redeclaration
        echo "✅\n";
    } catch (Exception $e) {
        echo "❌ " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Migrations completed.\n";
