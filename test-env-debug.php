<?php
require __DIR__ . '/config/bootstrap.php';

echo "<h1>Environment Debug</h1>";

echo "<h2>Raw env_get() values:</h2>";
echo "<pre>";
echo "DB_HOST: " . env_get('DB_HOST', 'NOT FOUND') . "\n";
echo "DB_PORT: " . env_get('DB_PORT', 'NOT FOUND') . "\n";
echo "DB_DATABASE: " . env_get('DB_DATABASE', 'NOT FOUND') . "\n";
echo "DB_USERNAME: " . env_get('DB_USERNAME', 'NOT FOUND') . "\n";
echo "DB_PASSWORD: " . (env_get('DB_PASSWORD', 'NOT FOUND') ? '[HIDDEN]' : 'empty') . "\n";
echo "</pre>";

echo "<h2>Database config array:</h2>";
echo "<pre>";
$db = require __DIR__ . '/config/database.php';
print_r($db);
echo "</pre>";

echo "<h2>Test Connection:</h2>";
try {
    $pdo = new PDO(
        "mysql:host={$db['host']};port={$db['port']}",
        $db['user'],
        $db['pass']
    );
    echo "<p style='color:green'>✅ Can connect to MySQL server</p>";
    
    // Try to select database
    $pdo->exec("USE {$db['dbname']}");
    echo "<p style='color:green'>✅ Can select database '{$db['dbname']}'</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ " . $e->getMessage() . "</p>";
}
?>