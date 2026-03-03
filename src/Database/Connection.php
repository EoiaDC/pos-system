<?php

namespace POS\Database;

use PDO;
use PDOException;
use RuntimeException;

class Connection
{
    private static ?PDO $pdo = null;
    private static array $config = [];

    public static function loadConfig(array $config): void
    {
        self::$config = $config;
    }

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = self::createConnection();
        }
        return self::$pdo;
    }

    private static function createConnection(): PDO
    {
        if (empty(self::$config)) {
            throw new RuntimeException('DB config not loaded. Call Connection::loadConfig() first.');
        }

        $host = self::$config['host'] ?? 'localhost';
        $port = self::$config['port'] ?? '3306';
        $dbname = self::$config['name'] ?? '';
        $charset = self::$config['charset'] ?? 'utf8mb4';
        $user = self::$config['user'] ?? '';
        $pass = self::$config['pass'] ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);

            // Set session timezone
            $pdo->exec("SET time_zone = '+08:00'");

            // Set strict SQL mode
            $pdo->exec("SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

            return $pdo;
        } catch (PDOException $e) {
            // Clear error message - no password, no DSN details
            throw new RuntimeException(
                "DB connection failed. Check DB_HOST/DB_NAME/DB_USER/DB_PASS in .env"
            );
        }
    }
}
