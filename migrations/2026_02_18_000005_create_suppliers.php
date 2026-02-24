<?php
/**
 * Migration: Create suppliers table
 * Date: 2026-02-18
 */

use Pos\Database\DB;  // ADD THIS LINE

return [
    'up' => function() {
        $sql = "
            CREATE TABLE IF NOT EXISTS suppliers (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                tin VARCHAR(30) NULL,
                address VARCHAR(255) NULL,
                phone VARCHAR(50) NULL,
                email VARCHAR(150) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NULL,
                INDEX idx_suppliers_name (name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        DB::execute($sql);
    },
    
    'down' => function() {
        // WARNING: This would delete supplier data!
        DB::execute("DROP TABLE IF EXISTS suppliers");
    }
];