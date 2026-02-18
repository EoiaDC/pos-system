<?php

return new class
{
    public function up(PDO $db): void
    {
        // First create purchase_orders table
        $sql1 = "CREATE TABLE IF NOT EXISTS purchase_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            po_number VARCHAR(50) NOT NULL UNIQUE,
            supplier_name VARCHAR(255) NOT NULL,
            supplier_address TEXT NULL,
            supplier_contact VARCHAR(100) NULL,
            order_date DATE NOT NULL,
            expected_date DATE NULL,
            status ENUM('draft', 'pending', 'approved', 'received', 'cancelled') DEFAULT 'draft',
            subtotal DECIMAL(15,2) DEFAULT 0.00,
            tax DECIMAL(15,2) DEFAULT 0.00,
            total DECIMAL(15,2) DEFAULT 0.00,
            notes TEXT NULL,
            created_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($sql1);
        
        // Then create purchase_order_items table (references units_of_measure)
        $sql2 = "CREATE TABLE IF NOT EXISTS purchase_order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            po_id INT NOT NULL,
            item_name VARCHAR(255) NOT NULL,
            item_code VARCHAR(50) NULL,
            unit_id INT NULL,
            quantity DECIMAL(15,2) NOT NULL,
            unit_price DECIMAL(15,2) NOT NULL,
            total_price DECIMAL(15,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
            notes TEXT NULL,
            FOREIGN KEY (po_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
            FOREIGN KEY (unit_id) REFERENCES units_of_measure(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($sql2);
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS purchase_order_items");
        $db->exec("DROP TABLE IF EXISTS purchase_orders");
    }
};