#!/usr/bin/env php
<?php
/**
 * PH POS System - Migration Runner CLI
 * Usage: php bin/migrate.php [command]
 * Commands: migrate, status, dry-run, help
 */

// Autoloader (simple for now)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Database;
use App\Core\Migrator;

// Parse command line arguments
$command = $argv[1] ?? 'migrate';
$validCommands = ['migrate', 'status', 'dry-run', 'help'];

if (!in_array($command, $validCommands)) {
    echo "Unknown command: {$command}\n";
    echo "Usage: php bin/migrate.php [migrate|status|dry-run|help]\n";
    exit(1);
}

if ($command === 'help') {
    echo "\nPH POS System - Database Migration Tool\n";
    echo "========================================\n\n";
    echo "Commands:\n";
    echo "  migrate   - Run pending migrations (default)\n";
    echo "  status    - Show current migration status\n";
    echo "  dry-run   - Preview what would be run without executing\n";
    echo "  help      - Show this help message\n\n";
    echo "Examples:\n";
    echo "  php bin/migrate.php\n";
    echo "  php bin/migrate.php status\n";
    echo "  php bin/migrate.php dry-run\n\n";
    exit(0);
}

try {
    echo "\n🚀 PH POS System - Migration Tool\n";
    echo "================================\n\n";
    
    $db = new Database();
    $migrator = new Migrator($db);
    
    switch ($command) {
        case 'status':
            $migrator->showStatus(false);
            break;
            
        case 'dry-run':
            $migrator->showStatus(true);
            break;
            
        case 'migrate':
            $migrator->migrate(false);
            break;
    }
    
    // Display all output
    foreach ($migrator->getOutput() as $line) {
        echo $line . "\n";
    }
    
    echo "\n✨ Done.\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}