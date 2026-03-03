<?php
// Run this migration to create the users table
require_once __DIR__ . '/../config/bootstrap.php';

$sql = "
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'CASHIER',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    INDEX idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    db()->exec($sql);
    echo "Table 'users' created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating users table: " . $e->getMessage() . "\n";
}