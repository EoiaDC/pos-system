<?php

class Connection
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $dbConfig = require __DIR__ . '/../../config/database.php';

        $host = $dbConfig['host'] ?? '127.0.0.1';
        $dbname = $dbConfig['dbname'] ?? '';
        $charset = $dbConfig['charset'] ?? 'utf8mb4';
        $user = $dbConfig['user'] ?? '';
        $pass = $dbConfig['pass'] ?? '';

        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        self::$pdo = new PDO($dsn, $user, $pass, $options);

        self::$pdo->exec("SET time_zone = '+08:00'");
        self::$pdo->exec("SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

        return self::$pdo;
    }
}