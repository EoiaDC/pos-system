<?php
// Load bootstrap first
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>✅ Simple System Test</h1>";

// Test 1: Check if bootstrap loaded
echo "<h2>1. Bootstrap Check</h2>";
echo "✓ bootstrap.php loaded<br>";

// Test 2: Try to include DB class directly
echo "<h2>2. Database Class Check</h2>";
$dbFile = __DIR__ . '/src/Database/DB.php';
if (file_exists($dbFile)) {
    echo "✓ DB.php file exists at: $dbFile<br>";
    require_once $dbFile;
    echo "✓ DB.php included successfully<br>";
    
    // Now try to use it
    if (class_exists('Pos\Database\DB')) {
        echo "✓ Pos\Database\DB class found<br>";
        try {
            $pdo = Pos\Database\DB::connect();
            echo "✓ Database connected successfully<br>";
            
            // List all tables
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo "✓ Database has " . count($tables) . " tables<br>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
            
        } catch (Exception $e) {
            echo "✗ Database error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "✗ Pos\Database\DB class not found after including file<br>";
    }
} else {
    echo "✗ DB.php not found at: $dbFile<br>";
}

// Test 3: Check if sales controllers exist
echo "<h2>3. Sales Controllers Check</h2>";
$salesDir = __DIR__ . '/src/Sales';
if (is_dir($salesDir)) {
    $files = glob($salesDir . '/*.php');
    echo "✓ Found " . count($files) . " controllers in Sales/<br>";
    foreach ($files as $file) {
        echo "  - " . basename($file) . "<br>";
    }
} else {
    echo "✗ Sales directory not found<br>";
}

echo "<h2>✅ Test Complete</h2>";
?>