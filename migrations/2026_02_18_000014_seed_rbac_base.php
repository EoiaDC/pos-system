<?php

return new class
{
    public function up(PDO $db): void
    {
        $db->beginTransaction();
        
        try {
            // Seed roles
            $roles = [
                ['admin', 'Administrator'],
                ['cashier', 'Cashier'],
                ['manager', 'Manager']
            ];
            
            $roleIds = [];
            foreach ($roles as [$code, $label]) {
                $stmt = $db->prepare("SELECT id FROM roles WHERE code = ?");
                $stmt->execute([$code]);
                $role = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$role) {
                    $stmt = $db->prepare("INSERT INTO roles (code, label) VALUES (?, ?)");
                    $stmt->execute([$code, $label]);
                    $roleIds[$code] = $db->lastInsertId();
                } else {
                    $roleIds[$code] = $role['id'];
                }
            }
            
            // Seed permissions
            $permissions = [
                // Admin permissions
                ['admin.access', 'Admin Dashboard Access'],
                ['admin.users.manage', 'Manage Users'],
                ['admin.roles.manage', 'Manage Roles'],
                
                // Reports
                ['reports.view', 'View Reports'],
                
                // Sales permissions
                ['sales.view', 'View Sales'],
                ['sales.create', 'Create Sales'],
                ['sales.void', 'Void Sales'],
                ['sales.refund', 'Process Refunds'],
                ['sales.payments.view', 'View Payments'],
                ['sales.payments.manage', 'Manage Payments'],
                
                // Inventory permissions
                ['inventory.view', 'View Inventory'],
                ['inventory.items.manage', 'Manage Items'],
                ['inventory.categories.manage', 'Manage Categories'],
                ['inventory.uom.manage', 'Manage Units of Measure'],
                
                // Purchasing permissions
                ['purchasing.view', 'View Purchasing'],
                ['purchasing.suppliers.manage', 'Manage Suppliers'],
                ['purchasing.po.create', 'Create Purchase Orders'],
                ['purchasing.po.approve', 'Approve Purchase Orders'],
                ['purchasing.receiving.post', 'Post Receiving'],
                
                // BIR permissions
                ['bir.company_profile.manage', 'Manage Company Profile'],
                ['bir.registers.manage', 'Manage POS Registers'],
                ['bir.or_series.manage', 'Manage OR Series'],
                ['bir.readiness.view', 'View BIR Readiness']
            ];
            
            $permIds = [];
            foreach ($permissions as [$code, $label]) {
                $stmt = $db->prepare("SELECT id FROM permissions WHERE code = ?");
                $stmt->execute([$code]);
                $perm = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$perm) {
                    $stmt = $db->prepare("INSERT INTO permissions (code, label) VALUES (?, ?)");
                    $stmt->execute([$code, $label]);
                    $permIds[$code] = $db->lastInsertId();
                } else {
                    $permIds[$code] = $perm['id'];
                }
            }
            
            // Assign all permissions to admin role
            if (isset($roleIds['admin'])) {
                $adminRoleId = $roleIds['admin'];
                
                foreach ($permIds as $permId) {
                    $stmt = $db->prepare("
                        INSERT IGNORE INTO role_permissions (role_id, permission_id)
                        VALUES (?, ?)
                    ");
                    $stmt->execute([$adminRoleId, $permId]);
                }
            }
            
            // Assign admin role to admin user
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute(['admin']);
            $adminUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($adminUser && isset($roleIds['admin'])) {
                $stmt = $db->prepare("
                    INSERT IGNORE INTO user_roles (user_id, role_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$adminUser['id'], $roleIds['admin']]);
            }
            
            $db->commit();
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function down(PDO $db): void
    {
        // Don't delete data in down migration for safety
        echo "⚠️ Skipping RBAC data deletion for safety\n";
    }
};