<?php

return new class
{
    public function up(PDO $db): void
    {
        $paymentPermissions = [
            ['sales.payments.view', 'View Payments'],
            ['sales.payments.manage', 'Manage Payments']
        ];
        
        // Insert payment permissions if they don't exist
        foreach ($paymentPermissions as [$code, $label]) {
            $stmt = $db->prepare("INSERT IGNORE INTO permissions (code, label) VALUES (?, ?)");
            $stmt->execute([$code, $label]);
        }
        
        // Get admin role ID
        $stmt = $db->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->execute(['admin']);
        $adminRoleId = $stmt->fetchColumn();
        
        if ($adminRoleId) {
            // Assign payment permissions to admin role
            foreach ($paymentPermissions as [$code, $label]) {
                $stmt = $db->prepare("
                    INSERT IGNORE INTO role_permissions (role_id, permission_id)
                    SELECT ?, id FROM permissions WHERE code = ?
                ");
                $stmt->execute([$adminRoleId, $code]);
            }
        }
    }

    public function down(PDO $db): void
    {
        // Don't delete permissions in down migration
    }
};