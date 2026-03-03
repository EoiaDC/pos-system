<?php
namespace App\Admin;

use App\Core\View;
use App\Audit\AuditEvent;
use App\Audit\Auditor;

class UserRolesController {

    public static function index()
    {
        $userId = $_GET['user_id'] ?? 0;
        if (!$userId) {
            header('Location: ' . APP_BASE_PATH . '/admin/users');
            exit;
        }

        $pdo = db();

        // Get user info
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$user) {
            die("User not found");
        }

        // Get all roles
        $stmt = $pdo->query("SELECT id, name, label FROM roles ORDER BY name");
        $allRoles = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get roles currently assigned to user
        $stmt = $pdo->prepare("SELECT role_id FROM user_roles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $assignedRoleIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        return View::render('admin/user_roles', [
            'user' => $user,
            'allRoles' => $allRoles,
            'assignedRoleIds' => $assignedRoleIds
        ]);
    }

    public static function update()
    {
        $userId = $_POST['user_id'] ?? 0;
        $roleIds = $_POST['role_ids'] ?? [];

        if (!$userId) {
            header('Location: ' . APP_BASE_PATH . '/admin/users');
            exit;
        }

        $pdo = db();
        $pdo->beginTransaction();

        try {
            // Delete existing assignments
            $stmt = $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Insert new assignments
            $insertStmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            foreach ($roleIds as $roleId) {
                $insertStmt->execute([$userId, $roleId]);
            }

            // Get role names for audit log
            if (!empty($roleIds)) {
                $placeholders = implode(',', array_fill(0, count($roleIds), '?'));
                $stmt = $pdo->prepare("SELECT name FROM roles WHERE id IN ($placeholders)");
                $stmt->execute($roleIds);
                $roleNames = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            } else {
                $roleNames = [];
            }

            // Audit log
            $event = new AuditEvent('rbac.user_roles.updated', 'user_role');
            $event->actor_user_id = $_SESSION['user']['id'] ?? null;
            $event->entity_id = $userId;
            $event->meta = [
                'target_user_id' => $userId,
                'role_ids' => $roleIds,
                'role_names' => $roleNames
            ];
            Auditor::record($event);

            $pdo->commit();

            // Set flash message
            flash('success', 'Roles updated successfully.');

            // Redirect back to the same page
            header('Location: ' . APP_BASE_PATH . '/admin/user-roles?user_id=' . $userId);
            exit;

        } catch (\Exception $e) {
            $pdo->rollBack();
            flash('error', 'Error updating roles: ' . $e->getMessage());
            header('Location: ' . APP_BASE_PATH . '/admin/user-roles?user_id=' . $userId);
            exit;
        }
    }
}