<?php

require_once __DIR__ . '/../src/Database/Connection.php';
require_once __DIR__ . '/../src/Database/DB.php';

use POS\Database\Connection;
use POS\Database\DB;

$config = require __DIR__ . '/../config/database.php';
Connection::loadConfig($config);

$sql = "
CREATE TABLE IF NOT EXISTS items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL,
    barcode VARCHAR(64) NULL,
    name VARCHAR(150) NOT NULL,
    category_id BIGINT UNSIGNED NULL,
    uom_id BIGINT UNSIGNED NOT NULL,
    cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    taxable TINYINT(1) NOT NULL DEFAULT 1,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    
    INDEX idx_items_barcode (barcode),
    INDEX idx_items_category (category_id),
    INDEX idx_items_uom (uom_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    DB::execute($sql);
    echo "✅ Migration successful: items table created\n";
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}
