<?php
require_once __DIR__ . '/../config/bootstrap.php';

$pdo = db();
$pdo->beginTransaction();

try {
    // Define new permissions
    $birPerms = [
        ['code' => 'bir.company_profile.manage', 'label' => 'Manage Company Profile'],
        ['code' => 'bir.registers.manage', 'label' => 'Manage POS Registers'],
        ['code' => 'bir.or_series.manage', 'label' => 'Manage OR Series'],
    ];

    // Insert permissions if they don't exist
    $permIds = [];
    foreach ($birPerms as $perm) {
        $stmt = $pdo->prepare("SELECT id FROM permissions WHERE code = ?");
        $stmt->execute([$perm['code']]);
        $existing = $stmt->fetch();
        if (!$existing) {
            $stmt = $pdo->prepare("INSERT INTO permissions (code, label) VALUES (?, ?)");
            $stmt->execute([$perm['code'], $perm['label']]);
            $permIds[$perm['code']] = $pdo->lastInsertId();
        } else {
            $permIds[$perm['code']] = $existing['id'];
        }
    }

    // Get admin role id
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'admin'");
    $stmt->execute();
    $adminRoleId = $stmt->fetchColumn();
    if (!$adminRoleId) {
        throw new Exception("Admin role not found");
    }

    // Assign all new permissions to admin role
    foreach ($permIds as $permId) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        $stmt->execute([$adminRoleId, $permId]);
    }

    $pdo->commit();
    echo "BIR permissions seeded successfully.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error seeding BIR permissions: " . $e->getMessage() . "\n";
}