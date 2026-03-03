<?php
require_once __DIR__ . '/../config/bootstrap.php';

$pdo = db();

// Check if column exists (optional – but migration can be idempotent with IF NOT EXISTS)
$stmt = $pdo->query("SHOW COLUMNS FROM sales_lines LIKE 'line_discount'");
$exists = $stmt->fetch();

if (!$exists) {
    $pdo->exec("ALTER TABLE sales_lines ADD COLUMN line_discount DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER unit_price");
    echo "Added line_discount column to sales_lines.\n";
} else {
    echo "line_discount column already exists.\n";
}