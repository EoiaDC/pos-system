<?php

return new class
{
    public function up(PDO $db): void
    {
        $db->beginTransaction();
        
        try {
            // Insert bir.readiness.view permission
            $stmt = $db->prepare("SELECT id FROM permissions WHERE code = ?");
            $stmt->execute(['bir.readiness.view']);
            $perm = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$perm) {
                $stmt = $db->prepare("INSERT INTO permissions (code, label) VALUES (?, ?)");
                $stmt->execute(['bir.readiness.view', 'View BIR Readiness']);
            }
            
            // Get admin role ID
            $stmt = $db->prepare("SELECT id FROM roles WHERE code = ?");
            $stmt->execute(['admin']);
            $adminRole = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($adminRole) {
                // Assign to admin role
                $stmt = $db->prepare("
                    INSERT IGNORE INTO role_permissions (role_id, permission_id)
                    SELECT ?, id FROM permissions WHERE code = ?
                ");
                $stmt->execute([$adminRole['id'], 'bir.readiness.view']);
            }
            
            $db->commit();
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function down(PDO $db): void
    {
        // Don't delete in down migration for safety
        echo "⚠️ Skipping BIR readiness permission deletion for safety\n";
    }
};