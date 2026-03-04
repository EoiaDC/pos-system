<?php

return new class
{
    public function up(PDO $db): void
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            item_code VARCHAR(50) UNIQUE NOT NULL,
            item_name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            category_id BIGINT UNSIGNED NULL,
            unit_id BIGINT UNSIGNED NULL,
            selling_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL,
            
            INDEX idx_category (category_id),
            INDEX idx_unit (unit_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $db->exec($sql);
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS items");
    }
};
