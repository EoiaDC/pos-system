<?php

return new class
{
    public function up(PDO $db): void
    {
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
        
        $db->exec($sql);
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS suppliers");
    }
};