<?php

return new class
{
    public function up(PDO $db): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS units_of_measure (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(10) NOT NULL UNIQUE,
            name VARCHAR(50) NOT NULL,
            description TEXT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($sql);
        
        // Insert default units
        $defaults = [
            ['pc', 'Piece'],
            ['kg', 'Kilogram'],
            ['g', 'Gram'],
            ['box', 'Box'],
            ['pack', 'Pack'],
            ['m', 'Meter'],
            ['l', 'Liter'],
            ['ml', 'Milliliter']
        ];
        
        $stmt = $db->prepare("INSERT INTO units_of_measure (code, name) VALUES (?, ?)");
        foreach ($defaults as $unit) {
            $stmt->execute($unit);
        }
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS units_of_measure");
    }
};