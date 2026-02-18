<?php

require_once __DIR__ . '/../src/Database/Connection.php';
require_once __DIR__ . '/../src/Database/DB.php';

use POS\Database\Connection;
use POS\Database\DB;

$config = require __DIR__ . '/../config/database.php';
Connection::loadConfig($config);

$sql = "
CREATE TABLE IF NOT EXISTS units_of_measure (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    DB::execute($sql);
    echo "✅ Migration successful: units_of_measure table created\n";
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}
