<?php

declare(strict_types=1);

namespace POS\Database;

use PDO;
use PDOException;
use RuntimeException;

final class Connection
{
    private static ?PDO $instance = null;
    private static array $config = [];

    public static function loadConfig(array $config): void
    {
        $required = ['host', 'port', 'name', 'user', 'pass', 'charset'];

        foreach ($required as $key) {
            if (!isset($config[$key])) {
                throw new RuntimeException("Missing DB config: {$key}");
            }
        }

        self::$config = $config;
    }

    public static function pdo(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    private static function createConnection(): PDO
    {
        if (empty(self::$config)) {
            throw new RuntimeException('DB config not loaded. Call Connection::loadConfig() first.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            self::$config['host'],
            self::$config['port'],
            self::$config['name'],
            self::$config['charset']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_TIMEOUT => 3,
        ];

        try {
            $pdo = new PDO($dsn, self::$config['user'], self::$config['pass'], $options);

            // Set session timezone (Asia/Manila = +08:00)
            $pdo->exec("SET time_zone = '+08:00'");

            // Set strict SQL mode
            $pdo->exec("SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

            return $pdo;
        } catch (PDOException $e) {
            error_log('[DB Connection Failed] ' . $e->getMessage());
            throw new RuntimeException('Database connection failed. Check credentials.', 0, $e);
        }
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}
