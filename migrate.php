<?php

require_once __DIR__ . '/src/Database/Connection.php';
require_once __DIR__ . '/src/Database/DB.php';

use POS\Database\Connection;
use POS\Database\DB;

// Load database config
$config = require __DIR__ . '/config/database.php';
Connection::loadConfig($config);

echo "🚀 Running migrations...\n\n";

// Get all migration files
$migrations = glob(__DIR__ . '/migrations/*.php');
sort($migrations); // Run in order

$count = 0;
foreach ($migrations as $file) {
    // Skip any backup files or non-migration files
    if (strpos(basename($file), 'create_') === false) {
        continue;
    }
    
    echo "▶️  " . basename($file) . "... ";
    try {
        require $file;
        echo "✅\n";
        $count++;
    } catch (Exception $e) {
        echo "❌ " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Completed: $count migrations executed.\n";