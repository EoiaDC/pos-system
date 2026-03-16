<?php
/**
 * Migration: Create payments table
 * 
 * This table stores all payments made against sales.
 * Week 1 supports only CASH payments, but schema allows future expansion.
 * 
 * @package POS\Migration
 */

// Get database connection
$config = require __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
        $config['user'],
        $config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "Creating payments table...\n";
    
    // Check if table already exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'payments'");
    if ($stmt->rowCount() > 0) {
        echo "? payments table already exists. Skipping creation.\n";
        exit(0);
    }
    
    // Create payments table
    $sql = "
    CREATE TABLE `payments` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `sale_id` BIGINT UNSIGNED NOT NULL,
        `payment_date` DATETIME NOT NULL,
        `payment_method` VARCHAR(30) NOT NULL DEFAULT 'CASH',
        `reference_no` VARCHAR(100) NULL,
        `amount` DECIMAL(12,2) NOT NULL,
        `notes` VARCHAR(255) NULL,
        `created_by` BIGINT UNSIGNED NOT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        INDEX `idx_payments_sale_id` (`sale_id`),
        INDEX `idx_payments_payment_date` (`payment_date`),
        INDEX `idx_payments_sale_created` (`sale_id`, `created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "? payments table created successfully\n";
    
    // Verify table structure
    $result = $pdo->query("DESCRIBE payments");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nTable structure verified:\n";
    echo str_repeat("-", 50) . "\n";
    foreach ($columns as $column) {
        echo "• {$column['Field']} - {$column['Type']}\n";
    }
    
    // Verify indexes
    $result = $pdo->query("SHOW INDEX FROM payments");
    $indexes = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nIndexes created:\n";
    echo str_repeat("-", 50) . "\n";
    $indexNames = [];
    foreach ($indexes as $index) {
        if (!in_array($index['Key_name'], $indexNames)) {
            echo "• {$index['Key_name']}\n";
            $indexNames[] = $index['Key_name'];
        }
    }
    
    echo "\n? Step 1 completed successfully!\n";
    
} catch (PDOException $e) {
    echo "? Error creating payments table: " . $e->getMessage() . "\n";
    exit(1);
}