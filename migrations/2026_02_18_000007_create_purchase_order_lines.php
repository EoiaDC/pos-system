<?php

return new class
{
    public function up(PDO $db): void
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS purchase_order_lines (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            po_id BIGINT UNSIGNED NOT NULL,
            item_id BIGINT UNSIGNED NULL,
            item_name VARCHAR(255) NOT NULL,
            quantity DECIMAL(15,2) NOT NULL,
            unit_price DECIMAL(15,2) NOT NULL,
            line_total DECIMAL(15,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
            notes TEXT NULL,
            created_at DATETIME NOT NULL,
            
            INDEX idx_po (po_id),
            INDEX idx_item (item_id),
            
            FOREIGN KEY (po_id) REFERENCES purchase_orders(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $db->exec($sql);
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS purchase_order_lines");
    }
};