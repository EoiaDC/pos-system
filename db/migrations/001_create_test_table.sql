-- Migration: 001_create_test_table
-- Description: Test migration to verify our system works
-- DO NOT KEEP - for testing only, remove after verification

CREATE TABLE IF NOT EXISTS `migration_test` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `migration_test` (`name`) VALUES ('Test Record 1'), ('Test Record 2');