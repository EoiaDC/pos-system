<?php

return new class
{
    public function up(PDO $db): void
    {
        // Insert bir.readiness.view permission
        $stmt = $db->prepare("INSERT IGNORE INTO permissions (code, label) VALUES (?, ?)");
        $stmt->execute(['bir.readiness.view', 'View BIR Readiness']);
        
        // Get admin role ID
        $stmt = $db->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->execute(['admin']);
        $adminRoleId = $stmt->fetchColumn();
        
        if ($adminRoleId) {
            // Assign to admin role
            $stmt = $db->prepare("
                INSERT IGNORE INTO role_permissions (role_id, permission_id)
                SELECT ?, id FROM permissions WHERE code = ?
            ");
            $stmt->execute([$adminRoleId, 'bir.readiness.view']);
        }
    }

    public function down(PDO $db): void
    {
        // Don't delete in down migration
    }
};