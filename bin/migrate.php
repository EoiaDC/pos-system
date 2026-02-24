#!/usr/bin/env php
<?php
/**
 * PH POS System - CLI Migration Runner
 */

require_once __DIR__ . '/../src/Database/DB.php';
require_once __DIR__ . '/../src/Migrations/MigrationLoader.php';
require_once __DIR__ . '/../src/Migrations/Migrator.php';

echo "\n🚀 PH POS Migration Tool\n";
echo "========================\n\n";

try {
    $migrator = new Migrator(__DIR__ . '/../migrations');
    $result = $migrator->up();
    
    if (empty($result)) {
        echo "📦 No migrations to apply.\n";
    } else {
        echo "✅ Applied migrations:\n";
        foreach ($result as $migration) {
            echo "  • " . $migration . "\n";
        }
    }
    
    echo "\n✅ Done.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}