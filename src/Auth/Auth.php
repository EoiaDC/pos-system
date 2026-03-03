<?php
class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function userId(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function attempt(string $username, string $password): bool
    {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name, role FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'role' => $user['role']
            ];
            return true;
        }
        return false;
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . APP_BASE_PATH . '/login');
            exit;
        }
    }

    /**
     * Check if current logged-in user has a given permission.
     */
    public static function hasPermission(string $permCode): bool
    {
        $userId = self::userId();
        if (!$userId) {
            return false;
        }
        return Rbac::userHasPermission($userId, $permCode);
    }

    /**
     * Require that current user has a specific permission.
     * If not logged in, redirect to login.
     * If logged in but lacks permission, show 403 page.
     */
    public static function requirePermission(string $permCode): void
    {
        if (!self::check()) {
            header('Location: ' . APP_BASE_PATH . '/login');
            exit;
        }

        if (!self::hasPermission($permCode)) {
            http_response_code(403);
            // Load 403 view (to be created in Part 6)
            require __DIR__ . '/../../views/errors/403.php';
            exit;
        }
    }
}