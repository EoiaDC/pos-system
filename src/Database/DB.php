<?php

declare(strict_types=1);

namespace POS\Database;

use PDO;
use PDOStatement;
use PDOException;
use RuntimeException;
use Throwable;

final class DB
{
    public static function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $pdo = Connection::pdo();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('[DB Query Failed] ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Database query failed.', 0, $e);
        }
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

    public static function begin(): void
    {
        Connection::pdo()->beginTransaction();
    }

    public static function commit(): void
    {
        Connection::pdo()->commit();
    }

    public static function rollBack(): void
    {
        Connection::pdo()->rollBack();
    }

    public static function transaction(callable $fn): mixed
    {
        self::begin();

        try {
            $result = $fn();
            self::commit();
            return $result;
        } catch (Throwable $e) {
            self::rollBack();
            throw $e;
        }
    }

    public static function lastInsertId(): string
    {
        return Connection::pdo()->lastInsertId();
    }
}
