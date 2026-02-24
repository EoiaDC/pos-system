<?php

namespace POS\Database;

use PDO;
use PDOStatement;

class DB
{
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $pdo = Connection::pdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    public static function lastInsertId(): string
    {
        return Connection::pdo()->lastInsertId();
    }
}
