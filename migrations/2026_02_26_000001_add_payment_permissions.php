<?php

return new class
{
    public function up(PDO $db): void
    {
        $paymentPermissions = [
            ['sales.payments.view', 'View Payments'],
            ['sales.payments.manage', 'Manage Payments']
        ];
        
        $db->beginTransaction();
        
        try {
            // Insert payment permissions if they don't exist
            foreach ($paymentPermissions as [$code, $label]) {
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
                // Assign payment permissions to admin role
                foreach ($paymentPermissions as [$code, $label]) {
                    $stmt = $db->prepare("
                        INSERT IGNORE INTO role_permissions (role_id, permission_id)
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
        echo "⚠️ Skipping payment permissions deletion for safety\n";
    }
};