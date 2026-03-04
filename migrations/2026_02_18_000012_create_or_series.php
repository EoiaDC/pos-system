<?php

return new class
{
    public function up(PDO $db): void
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS or_series (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            register_id BIGINT UNSIGNED NOT NULL,
            series_code VARCHAR(30) NOT NULL,
            start_no BIGINT UNSIGNED NOT NULL,
            end_no BIGINT UNSIGNED NOT NULL,
            current_no BIGINT UNSIGNED NOT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            INDEX idx_or_register (register_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $db->exec($sql);
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS or_series");
    }
};