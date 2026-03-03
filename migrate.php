<?php
require __DIR__ . '/config/bootstrap.php';

$dbConfig = require __DIR__ . '/config/database.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>POS System Migration Runner</title>
    <style>
        body { font-family: 'Segoe UI', monospace; padding: 20px; background: #1e1e2f; color: #fff; }
        .container { max-width: 800px; margin: 0 auto; background: #2d2d44; padding: 20px; border-radius: 10px; }
        h1 { color: #a5d6ff; border-bottom: 2px solid #4a4a6a; padding-bottom: 10px; }
        .success { color: #9bff9b; }
        .error { color: #ff9b9b; }
        .warning { color: #ffd966; }
        pre { background: #1a1a2e; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px; border-bottom: 1px solid #4a4a6a; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #aaa; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 POS System — Migration Runner</h1>
        <p>Lead: DEV A | Date: <?= date('Y-m-d H:i:s') ?></p>
        
        <pre>
<?php
try {
    // Connect without database selected first
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};charset={$dbConfig['charset']}",
        $dbConfig['user'],
        $dbConfig['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to MySQL server\n";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['dbname']}` 
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '{$dbConfig['dbname']}' ready\n";
    
    // Select the database
    $pdo->exec("USE `{$dbConfig['dbname']}`");
    
    // Create migrations table to track what's been run
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        batch INT NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Migrations tracking table ready\n\n";
    
    // Get current batch number
    $stmt = $pdo->query("SELECT MAX(batch) FROM migrations");
    $currentBatch = (int)$stmt->fetchColumn() + 1;
    
    // Check if migrations folder exists
    $migrationsDir = __DIR__ . '/migrations';
    if (!is_dir($migrationsDir)) {
        mkdir($migrationsDir, 0777, true);
        echo "📁 Created migrations folder\n";
    }
    
    // Get all migration files
    $files = glob($migrationsDir . '/*.php');
    
    if (empty($files)) {
        echo "⚠️ No migration files found in /migrations\n";
        echo "   Add files like: 2026_02_18_000001_create_something.php\n";
    } else {
        sort($files); // Run in order
        
        // Get already run migrations
        $run = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
        
        $newMigrations = array_filter($files, function($file) use ($run) {
            return !in_array(basename($file), $run);
        });
        
        if (empty($newMigrations)) {
            echo "✅ No new migrations to run\n";
        } else {
            echo "📦 Found " . count($newMigrations) . " new migration(s)\n";
            echo str_repeat('-', 50) . "\n";
            
            foreach ($newMigrations as $file) {
                $name = basename($file);
                echo "⚙️  Running: $name... ";
                
                try {
                    $migration = require $file;
                    
                    if (is_object($migration) && method_exists($migration, 'up')) {
                        $migration->up($pdo);
                        
                        // Record migration
                        $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                        $stmt->execute([$name, $currentBatch]);
                        
                        echo "<span class='success'>✅ DONE</span>\n";
                    } else {
                        echo "<span class='error'>❌ Invalid migration format</span>\n";
                    }
                } catch (Exception $e) {
                    echo "<span class='error'>❌ FAILED: " . $e->getMessage() . "</span>\n";
                }
            }
            
            echo str_repeat('-', 50) . "\n";
            echo "<span class='success'>✅✅ Batch #$currentBatch completed!</span>\n";
        }
    }
    
} catch (PDOException $e) {
    echo "<span class='error'>❌ Database error: " . $e->getMessage() . "</span>\n";
} catch (Exception $e) {
    echo "<span class='error'>❌ Error: " . $e->getMessage() . "</span>\n";
}
?>
        </pre>
        
        <div class="footer">
            <p>📋 Migration complete | <a href="test-all.php" style="color: #a5d6ff;">Run Integration Test →</a></p>
        </div>
    </div>
</body>
</html>