<?php
require_once __DIR__ . '/../config/bootstrap.php';

$pdo = db();
$pdo->beginTransaction();

try {
    $permissions = [
        ['code' => 'sales.payments.record', 'label' => 'Record Payments'],
        ['code' => 'sales.finalize',        'label' => 'Finalize Sales'],
        ['code' => 'sales.payments.view',   'label' => 'View Payments'],
    ];

    // Insert permissions if missing
    $permIds = [];
    foreach ($permissions as $perm) {
        $stmt = $pdo->prepare("SELECT id FROM permissions WHERE code = ?");
        $stmt->execute([$perm['code']]);
        $existing = $stmt->fetch();
        if (!$existing) {
            $stmt = $pdo->prepare("INSERT INTO permissions (code, label) VALUES (?, ?)");
            $stmt->execute([$perm['code'], $perm['label']]);
            $permIds[$perm['code']] = $pdo->lastInsertId();
            echo "Inserted permission: {$perm['code']}\n";
        } else {
            $permIds[$perm['code']] = $existing['id'];
            echo "Permission already exists: {$perm['code']}\n";
        }
    }

    // Get admin role
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'admin'");
    $stmt->execute();
    $adminRoleId = $stmt->fetchColumn();
    if (!$adminRoleId) {
        throw new Exception("Admin role not found.");
    }

    // Assign all to admin
    foreach ($permIds as $code => $permId) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        $stmt->execute([$adminRoleId, $permId]);
        echo "Assigned $code to admin.\n";
    }

    $pdo->commit();
    echo "Permissions migration completed.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}