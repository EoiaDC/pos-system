<?php
/**
 * Migration: Create schema_migrations table
 */
return [
    'up' => function($pdo) {
        // Check if table already exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'schema_migrations'");
        $tableExists = $stmt->rowCount() > 0;
        
        if (!$tableExists) {
            $sql = "
                CREATE TABLE `schema_migrations` (
                    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `filename` VARCHAR(255) NOT NULL UNIQUE,
                    `batch` INT NOT NULL,
                    `applied_at` DATETIME NOT NULL,
                    INDEX `idx_batch` (`batch`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            return $pdo->exec($sql);
        }
        
        return 0; // Table already exists
    },
    
    'down' => function($pdo) {
        // WARNING: This would delete migration history!
        return true; // Placeholder
    }
];