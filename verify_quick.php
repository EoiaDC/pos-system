<?php
// Hardcode your working credentials (same as in .env)
$host = 'localhost';
$port = '3306';
$dbname = 'pos_integration';
$user = 'pos_user';
$pass = 'secure_password_here';   // <-- use your actual password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connected\n\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// Now include your DB classes (they need the connection, but we'll use the PDO object directly)
require_once 'src/Database/Connection.php';
require_once 'src/Database/DB.php';

use POS\Database\Connection;
use POS\Database\DB;

// Manually set the config (so DB:: methods work)
Connection::loadConfig([
    'host' => $host,
    'port' => $port,
    'name' => $dbname,
    'user' => $user,
    'pass' => $pass,
    'charset' => $charset
]);

echo "=== DATABASE VERIFICATION ===\n";

// Check essential tables
$tables = ['sales', 'payments', 'items', 'categories', 'units_of_measure'];
foreach ($tables as $table) {
    try {
        $result = DB::fetch("SELECT 1 FROM $table LIMIT 1");
        echo "✅ Table '$table' exists" . ($result ? " and contains data" : "") . "\n";
    } catch (Exception $e) {
        echo "❌ Table '$table' missing: " . $e->getMessage() . "\n";
    }
}

// Test insert (will roll back)
try {
    DB::begin();
    $now = date('Y-m-d H:i:s');
    DB::execute("INSERT INTO sales (total, status, created_at) VALUES (?, ?, ?)", [0.00, 'test', $now]);
    $saleId = DB::lastInsertId();
    DB::execute("INSERT INTO payments (sale_id, amount, created_at) VALUES (?, ?, ?)", [$saleId, 0.00, $now]);
    DB::rollBack();
    echo "✅ Test insert succeeded (rolled back)\n";
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Insert test failed: " . $e->getMessage() . "\n";
}

echo "\n=== READY FOR SMOKE TEST ===\n";
