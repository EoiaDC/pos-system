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
        
        $db->beginTransaction();
        
        try {
            // Insert BIR permissions if they don't exist
            foreach ($birPermissions as [$code, $label]) {
                $stmt = $db->prepare("SELECT id FROM permissions WHERE code = ?");
                $stmt->execute([$code]);
                $perm = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$perm) {
                    $stmt = $db->prepare("INSERT INTO permissions (code, label) VALUES (?, ?)");
                    $stmt->execute([$code, $label]);
                }
            }
            
            // Get admin role ID
            $stmt = $db->prepare("SELECT id FROM roles WHERE code = ?");
            $stmt->execute(['admin']);
            $adminRole = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($adminRole) {
                // Assign BIR permissions to admin role
                foreach ($birPermissions as [$code, $label]) {
                    $stmt = $db->prepare("
                        INSERT INTO role_permissions (role_id, permission_id)
                        SELECT ?, id FROM permissions WHERE code = ?
                    ");
                    $stmt->execute([$adminRole['id'], $code]);
                }
            }
            
            $db->commit();
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function down(PDO $db): void
    {
        // Don't delete permissions in down migration for safety
        echo "⚠️ Skipping BIR permissions deletion for safety\n";
    }
};