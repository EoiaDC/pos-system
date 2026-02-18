#!/usr/bin/env php
<?php
/** @disregard P1009 This is a CLI file */

require_once __DIR__ . '/../src/Database/DB.php';
require_once __DIR__ . '/../src/Migrations/MigrationLoader.php';
require_once __DIR__ . '/../src/Migrations/Migrator.php';

// ... rest of your code

// Parse command line arguments
$command = $argv[1] ?? 'migrate';

echo "\n🚀 PH POS Migration Tool\n";
echo "========================\n\n";

try {
    $migrator = new Migrator(__DIR__ . '/../migrations');
    
    switch ($command) {
        case 'status':
            // Show migration status
            $rows = DB::fetchAll("SELECT filename, batch, applied_at FROM schema_migrations ORDER BY applied_at");
            echo "Applied migrations:\n";
            foreach ($rows as $row) {
                echo "  ✓ {$row['filename']} (batch {$row['batch']})\n";
            }
            break;
            
        case 'migrate':
        default:
            $result = $migrator->up();
            if (empty($result)) {
                echo "No migrations to apply.\n";
            } else {
                echo "Applied migrations:\n";
                foreach ($result as $migration) {
                    echo "  ✓ $migration\n";
                }
            }
            break;
    }
    
    echo "\n✅ Done.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}