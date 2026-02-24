<?php
/**
 * Migration: Create purchase_order_lines table
 * Date: 2026-02-18
 */

use Pos\Database\DB;

return [
    'up' => function() {
        $sql = "
            CREATE TABLE IF NOT EXISTS purchase_order_lines (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                purchase_order_id BIGINT UNSIGNED NOT NULL,
                item_id BIGINT UNSIGNED NOT NULL,
                qty DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                line_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                created_at DATETIME NOT NULL,
                INDEX idx_pol_po (purchase_order_id),
                INDEX idx_pol_item (item_id),
                CONSTRAINT fk_pol_purchase_order FOREIGN KEY (purchase_order_id) 
                    REFERENCES purchase_orders(id) ON DELETE CASCADE
                -- ITEMS TABLE NOT YET CREATED - WILL ADD FOREIGN KEY LATER
                -- CONSTRAINT fk_pol_item FOREIGN KEY (item_id) 
                --    REFERENCES items(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        DB::execute($sql);
    },
    
    'down' => function() {
        DB::execute("DROP TABLE IF EXISTS purchase_order_lines");
    }
];