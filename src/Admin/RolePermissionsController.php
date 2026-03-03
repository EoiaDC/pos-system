<?php
namespace App\Admin;

use App\Core\View;

class RolePermissionsController
{
    public static function index()
    {
        $roleId = $_GET['role_id'] ?? 0;
        if (!$roleId) {
            header('Location: ' . APP_BASE_PATH . '/admin/roles');
            exit;
        }

        $pdo = db();

        // Get role info
        $stmt = $pdo->prepare("SELECT id, name, label FROM roles WHERE id = ?");
        $stmt->execute([$roleId]);
        $role = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$role) {
            die("Role not found");
        }

        // Get permissions for this role
        $stmt = $pdo->prepare("
            SELECT p.code, p.label
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            WHERE rp.role_id = ?
            ORDER BY p.code
        ");
        $stmt->execute([$roleId]);
        $permissions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return View::render('admin/role_permissions', [
            'role' => $role,
            'permissions' => $permissions
        ]);
    }
}