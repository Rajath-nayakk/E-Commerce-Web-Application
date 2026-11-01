<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Class Model
 * 
 * Abstract base active-record/repository pattern for PDO database interaction.
 * Keeps controllers clean and encapsulates SQL queries and business logic.
 */
abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function db(): Database
    {
        return Database::getInstance();
    }

    public static function find(int $id): ?array
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        $sql = "SELECT * FROM `{$table}` WHERE `{$pk}` = :id LIMIT 1";
        return static::db()->fetchOne($sql, ['id' => $id]);
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        $table = static::$table;
        $sql = "SELECT * FROM `{$table}` ORDER BY {$orderBy}";
        return static::db()->fetchAll($sql);
    }

    public static function where(string $column, mixed $value, string $orderBy = 'id DESC'): array
    {
        $table = static::$table;
        $sql = "SELECT * FROM `{$table}` WHERE `{$column}` = :val ORDER BY {$orderBy}";
        return static::db()->fetchAll($sql, ['val' => $value]);
    }

    public static function firstWhere(string $column, mixed $value): ?array
    {
        $table = static::$table;
        $sql = "SELECT * FROM `{$table}` WHERE `{$column}` = :val LIMIT 1";
        return static::db()->fetchOne($sql, ['val' => $value]);
    }

    public static function create(array $data): int
    {
        return static::db()->insert(static::$table, $data);
    }

    public static function update(int $id, array $data): int
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        return static::db()->update($table, $data, "`{$pk}` = :id", ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        return static::db()->delete($table, "`{$pk}` = :id", ['id' => $id]);
    }

    public static function count(string $where = '1=1', array $params = []): int
    {
        $table = static::$table;
        $sql = "SELECT COUNT(*) as total FROM `{$table}` WHERE {$where}";
        $row = static::db()->fetchOne($sql, $params);
        return (int) ($row['total'] ?? 0);
    }
}

