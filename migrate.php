<?php
// Turn on errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include everything directly
require_once __DIR__ . '/src/Database/DB.php';
require_once __DIR__ . '/src/Migrations/MigrationLoader.php';
require_once __DIR__ . '/src/Migrations/Migrator.php';

// Run migrations
$migrator = new Migrator(__DIR__ . '/migrations');
$result = $migrator->up();

// Simple output
echo "<h2>Migration Result</h2>";
if (empty($result)) {
    echo "<p>✓ No migrations to apply</p>";
} else {
    echo "<p>✓ Applied:</p><ul>";
    foreach ($result as $m) {
        echo "<li>$m</li>";
    }
    echo "</ul>";
}