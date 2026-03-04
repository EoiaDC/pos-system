<?php

return new class
{
    public function up(PDO $db): void
    {
        // Seed roles - using 'name' column (from your error, roles uses 'name')
        $roles = [
            ['admin', 'Administrator'],
            ['cashier', 'Cashier'],
            ['manager', 'Manager']
        ];
        
        $roleIds = [];
        foreach ($roles as [$roleName, $label]) {
            // roles table uses 'name' column
            $stmt = $db->prepare("INSERT IGNORE INTO roles (name, label) VALUES (?, ?)");
            $stmt->execute([$roleName, $label]);
            
            // Get role ID
            $stmt = $db->prepare("SELECT id FROM roles WHERE name = ?");
            $stmt->execute([$roleName]);
            $roleIds[$roleName] = $stmt->fetchColumn();
        }
        
        // Seed permissions - using 'code' column (from your error, permissions uses 'code')
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
        foreach ($permissions as [$permCode, $label]) {
            // permissions table uses 'code' column
            $stmt = $db->prepare("INSERT IGNORE INTO permissions (code, label) VALUES (?, ?)");
            $stmt->execute([$permCode, $label]);
            
            // Get permission ID
            $stmt = $db->prepare("SELECT id FROM permissions WHERE code = ?");
            $stmt->execute([$permCode]);
            $permIds[$permCode] = $stmt->fetchColumn();
        }
        
        // Assign all permissions to admin role
        if (isset($roleIds['admin'])) {
            foreach ($permIds as $permId) {
                $stmt = $db->prepare("
                    INSERT IGNORE INTO role_permissions (role_id, permission_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$roleIds['admin'], $permId]);
            }
        }
        
        // Assign admin role to admin user
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute(['admin']);
        $adminUser = $stmt->fetchColumn();
        
        if ($adminUser && isset($roleIds['admin'])) {
            $stmt = $db->prepare("
                INSERT IGNORE INTO user_roles (user_id, role_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$adminUser, $roleIds['admin']]);
        }
    }

    public function down(PDO $db): void
    {
        // Don't delete data in down migration
    }
};