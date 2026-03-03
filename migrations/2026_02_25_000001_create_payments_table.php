<?php

return new class
{
    public function up(PDO $db): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS payments (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            sale_id BIGINT NOT NULL,
            method ENUM('CASH') NOT NULL DEFAULT 'CASH',
            amount DECIMAL(12,2) NOT NULL,
            paid_at DATETIME NOT NULL,
            notes VARCHAR(255) NULL,
            created_by INT NULL,
            created_at DATETIME NOT NULL,
            
            INDEX idx_sale (sale_id),
            INDEX idx_paid_at (paid_at)
            
            -- FOREIGN KEY temporarily removed
            -- Will be added in a later migration after schema stabilizes
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($sql);
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS payments");
    }
};