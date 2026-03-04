<?php

return new class
{
    public function up(PDO $db): void
    {
        $birPermissions = [
            ['bir.company_profile.manage', 'Manage Company Profile'],
            ['bir.registers.manage', 'Manage POS Registers'],
            ['bir.or_series.manage', 'Manage OR Series']
        ];
        
        // Insert BIR permissions if they don't exist
        foreach ($birPermissions as [$code, $label]) {
            $stmt = $db->prepare("INSERT IGNORE INTO permissions (code, label) VALUES (?, ?)");
            $stmt->execute([$code, $label]);
        }
        
        // Get admin role ID
        $stmt = $db->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->execute(['admin']);
        $adminRoleId = $stmt->fetchColumn();
        
        if ($adminRoleId) {
            // Assign BIR permissions to admin role
            foreach ($birPermissions as [$code, $label]) {
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