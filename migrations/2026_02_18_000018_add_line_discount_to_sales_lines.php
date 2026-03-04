<?php

return new class
{
    public function up(PDO $db): void
    {
        // Check if column already exists
        $stmt = $db->query("SHOW COLUMNS FROM sales_lines LIKE 'line_discount'");
        $columnExists = $stmt->fetch();
        
        if (!$columnExists) {
            $db->exec("ALTER TABLE sales_lines ADD COLUMN line_discount DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER unit_price");
            echo "line_discount column added.\n";
        } else {
            echo "line_discount column already exists.\n";
        }
    }

    public function down(PDO $db): void
    {
        $db->exec("ALTER TABLE sales_lines DROP COLUMN IF EXISTS line_discount");
    }
};