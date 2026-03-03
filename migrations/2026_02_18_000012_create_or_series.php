<?php
require_once __DIR__ . '/../config/bootstrap.php';

$sql = "
CREATE TABLE IF NOT EXISTS or_series (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    register_id BIGINT UNSIGNED NOT NULL,
    series_code VARCHAR(30) NOT NULL,
    start_no BIGINT UNSIGNED NOT NULL,
    end_no BIGINT UNSIGNED NOT NULL,
    current_no BIGINT UNSIGNED NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    INDEX idx_or_register (register_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    db()->exec($sql);
    echo "Table 'or_series' created successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}