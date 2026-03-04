<?php
namespace App\Core;

class Auth
{
    private static ?array $user = null;
    
    /**
     * Start session if not already started
     */
    private static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Attempt to log in a user
     */
    public static function attempt(string $username, string $password): bool
    {
        $config = require __DIR__ . '/../../config/database.php';
        $db = new \PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
            $config['user'],
            $config['pass']
        );
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            self::initSession();
            $_SESSION['user'] = $user;
            self::$user = $user;
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if user is logged in
     */
    public static function check(): bool
    {
        self::initSession();
        return isset($_SESSION['user']);
    }
    
    /**
     * Get the currently logged in user
     */
    public static function user(): ?array
    {
        self::initSession();
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Get the current user's ID
     */
    public static function userId(): ?int
    {
        $user = self::user();
        return $user['id'] ?? null;
    }
    
    /**
     * Log out the current user
     */
    public static function logout(): void
    {
        self::initSession();
        $_SESSION = [];
        session_destroy();
        self::$user = null;
    }
    
    /**
     * Check if user has a specific permission
     */
    public static function hasPermission(string $permission): bool
    {
        if (!self::check()) {
            return false;
        }
        
        $user = self::user();
        
        // Admin has all permissions
        if ($user['username'] === 'admin') {
            return true;
        }
        
        // TODO: Implement proper RBAC permission checking
        return true; // Temporary for testing
    }
    
    /**
     * Require a specific permission or show 403
     */
    public static function requirePermission(string $permission): void
    {
        if (!self::hasPermission($permission)) {
            http_response_code(403);
            require __DIR__ . '/../../views/errors/403.php';
            exit;
        }
    }
}