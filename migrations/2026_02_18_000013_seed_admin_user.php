<?php
require_once __DIR__ . '/../config/bootstrap.php';

$username = 'admin';
$password = 'admin123!';
$hash = password_hash($password, PASSWORD_DEFAULT);
$full_name = 'Administrator';
$role = 'ADMIN';
$created_at = date('Y-m-d H:i:s');

$pdo = db();
// Check if admin already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if (!$stmt->fetch()) {
    $sql = "INSERT INTO users (username, password_hash, full_name, role, is_active, created_at) VALUES (?, ?, ?, ?, 1, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $hash, $full_name, $role, $created_at]);
    echo "Admin user seeded successfully.\n";
} else {
    echo "Admin user already exists.\n";
}