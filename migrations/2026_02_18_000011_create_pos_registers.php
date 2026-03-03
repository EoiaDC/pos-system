<?php
require_once __DIR__ . '/../config/bootstrap.php';

$sql = "
CREATE TABLE IF NOT EXISTS pos_registers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    register_code VARCHAR(30) UNIQUE NOT NULL,
    machine_name VARCHAR(100) NULL,
    serial_no VARCHAR(100) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    db()->exec($sql);
    echo "Table 'pos_registers' created successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}