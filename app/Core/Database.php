<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Class Database
 * 
 * PDO wrapper with safe defaults and prepared statements.
 * DO NOT MODIFY without architectural review.
 */
final class Database
{
    private static ?PDO $connection = null;
    private static array $config = [];

    /**
     * Initialize database configuration
     * 
     * @param array $config Database connection parameters
     * @throws RuntimeException If required config keys missing
     */
    public static function initConfig(array $config): void
    {
        $required = ['host', 'port', 'name', 'user', 'pass', 'charset'];

        foreach ($required as $key) {
            if (!isset($config[$key])) {
                throw new RuntimeException(
                    sprintf('Missing required database config: %s', $key)
                );
            }
        }

        self::$config = $config;
    }

    /**
     * Get PDO connection (Singleton pattern)
     * 
     * @return PDO
     * @throws RuntimeException On connection failure
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = self::createConnection();
        }

        return self::$connection;
    }

    /**
     * Create new PDO connection with safe defaults
     * 
     * @return PDO
     * @throws RuntimeException On connection failure
     */
    private static function createConnection(): PDO
    {
        if (empty(self::$config)) {
            throw new RuntimeException(
                'Database configuration not initialized. Call Database::initConfig() first.'
            );
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            self::$config['host'],
            self::$config['port'],
            self::$config['name'],
            self::$config['charset']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,           // Throw exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Return associative arrays
            PDO::ATTR_EMULATE_PREPARES => false,                   // Real prepared statements
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',   // Force UTF8MB4
            PDO::ATTR_PERSISTENT => false,                        // No persistent connections
            PDO::ATTR_TIMEOUT => 3,                               // Fail fast if DB down
        ];

        try {
            $pdo = new PDO(
                $dsn,
                self::$config['user'],
                self::$config['pass'],
                $options
            );

            return $pdo;
        } catch (PDOException $e) {
            // Log the actual error (you'd use a logger here in production)
            error_log(sprintf(
                '[Database Connection Failed] %s | DSN: %s',
                $e->getMessage(),
                preg_replace('/pass=\S+/', 'pass=***', $dsn)
            ));

            throw new RuntimeException(
                'Database connection failed. Please check your credentials and try again.',
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Execute a prepared statement query
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return \PDOStatement
     * @throws RuntimeException On query failure
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log(sprintf(
                '[Database Query Failed] %s | SQL: %s',
                $e->getMessage(),
                $sql
            ));

            throw new RuntimeException(
                'Database query failed.',
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Test database connectivity
     * 
     * @return bool True if connection successful
     */
    public static function testConnection(): bool
    {
        try {
            $pdo = self::getConnection();
            $pdo->query('SELECT 1')->fetch();
            return true;
        } catch (PDOException | RuntimeException $e) {
            return false;
        }
    }

    /**
     * Get database version info
     * 
     * @return array|null Version information or null on failure
     */
    public static function getServerInfo(): ?array
    {
        try {
            $pdo = self::getConnection();

            return [
                'version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
                'connection' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS),
                'client_version' => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
            ];
        } catch (PDOException | RuntimeException $e) {
            return null;
        }
    }

    /**
     * Force close connection (primarily for testing)
     */
    public static function reset(): void
    {
        self::$connection = null;
    }
}
