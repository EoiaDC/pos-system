<?php
// Load environment helpers FIRST
require_once __DIR__ . '/config/env.php';

// Load database classes
require_once __DIR__ . '/src/Database/Connection.php';
require_once __DIR__ . '/src/Database/DB.php';

use POS\Database\Connection;
use POS\Database\DB;

$config = require __DIR__ . '/config/database.php';
Connection::loadConfig($config);

echo "=== DATABASE VERIFICATION ===\n";

// Check essential tables
$tables = ['sales', 'payments', 'items', 'categories', 'units_of_measure'];
foreach ($tables as $table) {
    try {
        $result = DB::fetch("SELECT 1 FROM $table LIMIT 1");
        echo "✅ Table '$table' exists" . ($result ? " and contains data" : "") . "\n";
    } catch (Exception $e) {
        echo "❌ Table '$table' missing or not accessible: " . $e->getMessage() . "\n";
    }
}

// Check foreign key relationship (payments → sales)
try {
    $fkCheck = DB::fetch("
        SELECT COUNT(*) as cnt 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'payments' 
          AND COLUMN_NAME = 'sale_id'
          AND REFERENCED_TABLE_NAME = 'sales'
    ");
    if ($fkCheck['cnt'] > 0) {
        echo "✅ Foreign key payments.sale_id → sales.id exists\n";
    } else {
        echo "⚠️ Foreign key payments.sale_id not found (may not be required)\n";
    }
} catch (Exception $e) {
    echo "⚠️ Could not verify foreign keys: " . $e->getMessage() . "\n";
}

// Test insert into sales and payments (will rollback)
try {
    DB::begin(); // start transaction

    $now = date('Y-m-d H:i:s');
    DB::execute(
        "INSERT INTO sales (total, status, created_at) VALUES (?, ?, ?)",
        [0.00, 'test', $now]
    );
    $saleId = DB::lastInsertId();
    echo "✅ Test sale inserted (ID: $saleId)\n";

    DB::execute(
        "INSERT INTO payments (sale_id, amount, created_at) VALUES (?, ?, ?)",
        [$saleId, 0.00, $now]
    );
    echo "✅ Test payment inserted\n";

    DB::rollBack(); // undo everything
    echo "✅ Both inserts rolled back successfully – database untouched\n";
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Insert test failed: " . $e->getMessage() . "\n";
}

echo "\n=== READY FOR SMOKE TEST ===\n";
