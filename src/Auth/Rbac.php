<?php
class Rbac
{
    private static $userPermsCache = [];
    private static $userRolesCache = [];

    /**
     * Get role codes (names) for a user.
     * @param int $userId
     * @return string[]
     */
    public static function getUserRoleCodes(int $userId): array
    {
        if (isset(self::$userRolesCache[$userId])) {
            return self::$userRolesCache[$userId];
        }

        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT r.name
            FROM roles r
            JOIN user_roles ur ON r.id = ur.role_id
            WHERE ur.user_id = ?
        ");
        $stmt->execute([$userId]);
        $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        self::$userRolesCache[$userId] = $roles;
        return $roles;
    }

    /**
     * Get permission codes for a user (via roles).
     * @param int $userId
     * @return string[]
     */
    public static function getUserPermissions(int $userId): array
    {
        if (isset(self::$userPermsCache[$userId])) {
            return self::$userPermsCache[$userId];
        }

        $pdo = db();
        $stmt = $pdo->prepare("
            SELECT DISTINCT p.code
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            JOIN user_roles ur ON rp.role_id = ur.role_id
            WHERE ur.user_id = ?
        ");
        $stmt->execute([$userId]);
        $perms = $stmt->fetchAll(PDO::FETCH_COLUMN);
        self::$userPermsCache[$userId] = $perms;
        return $perms;
    }

    /**
     * Check if user has a specific role.
     */
    public static function userHasRole(int $userId, string $roleCode): bool
    {
        $roles = self::getUserRoleCodes($userId);
        return in_array($roleCode, $roles, true);
    }

    /**
     * Check if user has a specific permission.
     */
    public static function userHasPermission(int $userId, string $permCode): bool
    {
        $perms = self::getUserPermissions($userId);
        return in_array($permCode, $perms, true);
    }
}