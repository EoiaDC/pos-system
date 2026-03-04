<?php
namespace Pos\Database;

class DB
{
    private static ?\PDO $connection = null;
    private static array $config = [];
    
    public static function setConfig(array $config): void
    {
        self::$config = $config;
    }
    
    public static function connect(): \PDO
    {
        if (self::$connection === null) {
            // Load config if not set
            if (empty(self::$config)) {
                self::$config = require __DIR__ . '/../../config/database.php';
            }
            
            $host = self::$config['host'];
            $port = self::$config['port'] ?? '3306';
            $dbname = self::$config['dbname'];
            $username = self::$config['user'];
            $password = self::$config['pass'];
            
            try {
                $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
                $pdo = new \PDO($dsn, $username, $password);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                
                // Create database if not exists
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` 
                          CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `$dbname`");
                
                self::$connection = $pdo;
            } catch (\PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
    
    // Update transaction to pass PDO to callback
public static function transaction(callable $callback)
{
    $pdo = self::connect();
    
    try {
        $pdo->beginTransaction();
        $result = $callback($pdo);  // ✅ Pass PDO to callback
        $pdo->commit();
        return $result;
    } catch (\Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
    
    // Keep all your other methods exactly the same
    public static function execute(string $sql, array $params = []): bool
    {
        $pdo = self::connect();
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    public static function fetch(string $sql, array $params = []): ?array
    {
        $pdo = self::connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public static function fetchAll(string $sql, array $params = []): array
    {
        $pdo = self::connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}