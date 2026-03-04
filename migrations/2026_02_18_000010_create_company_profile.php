<?php

return new class
{
    public function up(PDO $db): void
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS company_profile (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            registered_name VARCHAR(150) NOT NULL,
            trade_name VARCHAR(150) NULL,
            tin VARCHAR(30) NULL,
            address VARCHAR(255) NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $db->exec($sql);
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS company_profile");
    }
};