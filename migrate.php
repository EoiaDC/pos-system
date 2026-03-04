<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load bootstrap FIRST (defines env_get and loads config)
require_once __DIR__ . '/config/bootstrap.php';

// Load migration classes
require_once __DIR__ . '/src/Database/DB.php';
require_once __DIR__ . '/src/Migrations/MigrationLoader.php';
require_once __DIR__ . '/src/Migrations/Migrator.php';

// Set config in DB class
$config = require __DIR__ . '/config/database.php';
Pos\Database\DB::setConfig($config);

// Run migrations
echo "<h1>🔄 POS System Migration</h1>";
echo "<pre>";

try {
    $migrator = new Migrator(__DIR__ . '/migrations');
    $result = $migrator->up();
    
    if (empty($result)) {
        echo "✓ No new migrations to apply\n";
    } else {
        echo "✓ Applied " . count($result) . " migrations:\n";
        foreach ($result as $m) {
            echo "  - $m\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "</pre>";