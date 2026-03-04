<?php

return new class
{
    public function up(PDO $db): void
    {
        // Check if admin user already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute(['admin']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            // Create admin user with default password
            $hashedPassword = password_hash('admin123!', PASSWORD_DEFAULT);
            $now = date('Y-m-d H:i:s');
            
            $stmt = $db->prepare("
                INSERT INTO users (username, password, email, is_active, created_at)
                VALUES (?, ?, ?, 1, ?)
            ");
            $stmt->execute(['admin', $hashedPassword, 'admin@pos-system.local', $now]);
            
            echo "✅ Admin user created (username: admin, password: admin123!)\n";
        }
    }

    public function down(PDO $db): void
    {
        // Don't delete admin user in down migration for safety
        // Just log a message
        echo "⚠️ Skipping admin user deletion for safety\n";
    }
};