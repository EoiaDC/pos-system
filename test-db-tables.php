<?php
require __DIR__ . '/config/bootstrap.php';

echo "<h1>Database Table Check</h1>";
$config = require __DIR__ . '/config/database.php';

try {
    $db = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']}",
        $config['user'],
        $config['pass']
    );
    
    $tables = ['sales_headers', 'sales_lines', 'payments'];
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Table</th><th>Status</th><th>Rows</th></tr>";
    
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        $exists = $result->rowCount() > 0;
        
        if ($exists) {
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<tr>";
            echo "<td>$table</td>";
            echo "<td style='color:green'>✅ EXISTS</td>";
            echo "<td>$count rows</td>";
            echo "</tr>";
        } else {
            echo "<tr>";
            echo "<td>$table</td>";
            echo "<td style='color:red'>❌ MISSING</td>";
            echo "<td>-</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>