<?php
namespace Pos\Database;

class DB
{
    private static ?\PDO $connection = null;
    
    public static function connect(): \PDO
    {
        if (self::$connection === null) {
            $host = 'localhost';
            $dbname = 'pos_system';
            $username = 'root';
            $password = '';
            
            try {
                $dsn = "mysql:host=$host;charset=utf8mb4";
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
}