<?php
namespace App\Core;

class Database
{
    private \PDO $pdo;
    
    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'pos_system';
        $username = 'root';  // XAMPP default
        $password = '';      // XAMPP default
        
        try {
            $dsn = "mysql:host=$host;charset=utf8mb4";
            $this->pdo = new \PDO($dsn, $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Create database if not exists
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` 
                              CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->pdo->exec("USE `$dbname`");
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}