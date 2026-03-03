<?php
require_once __DIR__ . '/../config/bootstrap.php';

$pdo = db();

// Drop tables if exist (reverse order)
$pdo->exec("DROP TABLE IF EXISTS sales_lines");
$pdo->exec("DROP TABLE IF EXISTS sales_headers");

// Create sales_headers
$pdo->exec("
CREATE TABLE sales_headers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_uuid VARCHAR(36) UNIQUE NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
    pos_register_id BIGINT UNSIGNED NULL,
    or_series_id BIGINT UNSIGNED NULL,
    or_no INT NULL,
    customer_name VARCHAR(120) NULL,
    vatable_sales DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    vat_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    vat_exempt_sales DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    zero_rated_sales DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    discount_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    grand_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    INDEX idx_register (pos_register_id),
    FOREIGN KEY (pos_register_id) REFERENCES pos_registers(id) ON DELETE SET NULL,
    FOREIGN KEY (or_series_id) REFERENCES or_series(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Create sales_lines (without item FK for now)
$pdo->exec("
CREATE TABLE sales_lines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    item_id BIGINT UNSIGNED NULL,
    description VARCHAR(200) NULL,
    qty DECIMAL(12,3) NOT NULL DEFAULT 0.000,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    line_discount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    line_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sale (sale_id),
    INDEX idx_item (item_id),
    FOREIGN KEY (sale_id) REFERENCES sales_headers(id) ON DELETE CASCADE
    -- Item FK omitted for now; will be added later by DEV B
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

echo "Sales tables created successfully.\n";