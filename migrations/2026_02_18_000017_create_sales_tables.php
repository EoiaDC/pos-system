<?php

return new class
{
    public function up(PDO $db): void
    {
        // First create sales_headers table (without foreign keys for now)
        $sql1 = "CREATE TABLE IF NOT EXISTS sales_headers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            transaction_number VARCHAR(50) NOT NULL UNIQUE,
            register_id INT NULL,
            or_series_id INT NULL,
            or_number VARCHAR(50) NULL,
            customer_name VARCHAR(255) NULL,
            customer_tin VARCHAR(50) NULL,
            customer_address TEXT NULL,
            
            -- Financial fields (VAT-ready)
            subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            vat_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            vat_exempt_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            vat_rate DECIMAL(5,2) NOT NULL DEFAULT 12.00,
            grand_total DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            amount_tendered DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            change_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            payment_method ENUM('cash', 'card', 'gcash', 'maya', 'others') DEFAULT 'cash',
            
            -- Status fields
            status ENUM('draft', 'posted', 'voided', 'refunded') DEFAULT 'draft',
            void_reason TEXT NULL,
            voided_by INT NULL,
            voided_at DATETIME NULL,
            
            -- Audit fields
            created_by INT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            -- Indexes
            INDEX idx_register (register_id),
            INDEX idx_or_series (or_series_id),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at),
            INDEX idx_transaction (transaction_number)
            
            -- FOREIGN KEYS REMOVED TEMPORARILY
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($sql1);
        
        // Then create sales_lines table
        $sql2 = "CREATE TABLE IF NOT EXISTS sales_lines (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sale_id INT NOT NULL,
            item_id INT NULL,
            description VARCHAR(200) NOT NULL,
            qty DECIMAL(12,3) NOT NULL DEFAULT 0.000,
            unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            line_discount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            line_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            
            INDEX idx_sale (sale_id),
            INDEX idx_item (item_id),
            
            FOREIGN KEY (sale_id) REFERENCES sales_headers(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($sql2);
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS sales_lines");
        $db->exec("DROP TABLE IF EXISTS sales_headers");
    }
};