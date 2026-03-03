<?php
require_once __DIR__ . '/../config/bootstrap.php';

$sql = "
CREATE TABLE IF NOT EXISTS company_profile (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registered_name VARCHAR(150) NOT NULL,
    trade_name VARCHAR(150) NULL,
    tin VARCHAR(30) NULL,
    address VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    db()->exec($sql);
    echo "Table 'company_profile' created successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}