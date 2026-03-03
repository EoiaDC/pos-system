<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/bootstrap.php';

$tables = [
    'users',
    'audit_logs',
    'company_profile',
    'pos_registers',
    'or_series'
];

echo "<h1>Database Table Status</h1>";
echo "<p>Database: " . DB_NAME . "</p>";

try {
    $pdo = db();
    
    // Get actual tables from database
    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
    echo "<tr><th>Table</th><th>Status</th><th>Rows</th></tr>";
    
    foreach ($tables as $table) {
        $exists = in_array($table, $existingTables);
        $status = $exists ? '✅ Exists' : '❌ Missing';
        $rows = '—';
        
        if ($exists) {
            $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
            $rows = $countStmt->fetchColumn();
        }
        
        echo "<tr>";
        echo "<td>$table</td>";
        echo "<td style='color: " . ($exists ? 'green' : 'red') . "; font-weight: bold;'>$status</td>";
        echo "<td>$rows</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Additional check for admin user
    if (in_array('users', $existingTables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        $adminExists = $stmt->fetchColumn();
        echo "<p>Admin user: " . ($adminExists ? '✅ Present' : '❌ Not found') . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>Database Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}