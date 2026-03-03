<?php
require_once __DIR__ . '/../config/bootstrap.php';

$pdo = db();

try {
    $pdo->beginTransaction();

    // ---- Seed roles ----
    $roles = [
        ['name' => 'admin', 'label' => 'Administrator'],
        ['name' => 'cashier', 'label' => 'Cashier'],
        ['name' => 'manager', 'label' => 'Manager']
    ];
    $roleIds = [];
    foreach ($roles as $r) {
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->execute([$r['name']]);
        $existing = $stmt->fetch();
        if (!$existing) {
            $stmt = $pdo->prepare("INSERT INTO roles (name, label) VALUES (?, ?)");
            $stmt->execute([$r['name'], $r['label']]);
            $roleIds[$r['name']] = $pdo->lastInsertId();
        } else {
            $roleIds[$r['name']] = $existing['id'];
        }
    }

    // ---- Seed permissions ----
    $perms = [
        // Admin
        ['code' => 'admin.access', 'label' => 'Access admin area'],
        ['code' => 'admin.users.manage', 'label' => 'Manage users'],
        ['code' => 'admin.roles.manage', 'label' => 'Manage roles'],
        // Reports
        ['code' => 'reports.view', 'label' => 'View reports'],
        // Sales
        ['code' => 'sales.view', 'label' => 'View sales'],
        ['code' => 'sales.create', 'label' => 'Create sale'],
        ['code' => 'sales.void', 'label' => 'Void sale'],
        ['code' => 'sales.refund', 'label' => 'Refund sale'],
        // Inventory
        ['code' => 'inventory.view', 'label' => 'View inventory'],
        ['code' => 'inventory.items.manage', 'label' => 'Manage items'],
        ['code' => 'inventory.categories.manage', 'label' => 'Manage categories'],
        ['code' => 'inventory.uom.manage', 'label' => 'Manage UOMs'],
        // Purchasing
        ['code' => 'purchasing.view', 'label' => 'View purchasing'],
        ['code' => 'purchasing.suppliers.manage', 'label' => 'Manage suppliers'],
        ['code' => 'purchasing.po.create', 'label' => 'Create purchase order'],
        ['code' => 'purchasing.po.approve', 'label' => 'Approve purchase order'],
        ['code' => 'purchasing.receiving.post', 'label' => 'Post receiving']
    ];
    $permIds = [];
    foreach ($perms as $p) {
        $stmt = $pdo->prepare("SELECT id FROM permissions WHERE code = ?");
        $stmt->execute([$p['code']]);
        $existing = $stmt->fetch();
        if (!$existing) {
            $stmt = $pdo->prepare("INSERT INTO permissions (code, label) VALUES (?, ?)");
            $stmt->execute([$p['code'], $p['label']]);
            $permIds[$p['code']] = $pdo->lastInsertId();
        } else {
            $permIds[$p['code']] = $existing['id'];
        }
    }

    // ---- Give admin role ALL permissions ----
    $adminRoleId = $roleIds['admin'];
    // Clear existing (in case we rerun) – delete all role_permissions for admin
    $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?")->execute([$adminRoleId]);
    // Insert all permissions for admin
    foreach ($permIds as $permId) {
        $pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)")
            ->execute([$adminRoleId, $permId]);
    }

    // ---- Assign admin user (username = 'admin') to admin role ----
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminUser = $stmt->fetch();
    if ($adminUser) {
        // Remove any existing role for this user (optional, but safe)
        $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$adminUser['id']]);
        // Assign admin role
        $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")
            ->execute([$adminUser['id'], $adminRoleId]);
        echo "Admin user assigned to admin role.\n";
    } else {
        echo "Warning: admin user not found.\n";
    }

    $pdo->commit();
    echo "RBAC seed completed successfully.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error seeding RBAC: " . $e->getMessage() . "\n";
}