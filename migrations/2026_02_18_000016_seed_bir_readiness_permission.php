<?php
require_once __DIR__ . '/../config/bootstrap.php';

$pdo = db();
$pdo->beginTransaction();

try {
    // Insert permission if not exists
    $permCode = 'bir.readiness.view';
    $permLabel = 'View BIR Readiness Checklist';

    $stmt = $pdo->prepare("SELECT id FROM permissions WHERE code = ?");
    $stmt->execute([$permCode]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO permissions (code, label) VALUES (?, ?)");
        $stmt->execute([$permCode, $permLabel]);
        $permId = $pdo->lastInsertId();

        // Assign to admin role
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'admin'");
        $stmt->execute();
        $adminRoleId = $stmt->fetchColumn();

        if ($adminRoleId) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            $stmt->execute([$adminRoleId, $permId]);
        }
    }

    $pdo->commit();
    echo "Permission 'bir.readiness.view' seeded successfully.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}