<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use Exception;

/**
 * Class Database
 * 
 * Thread-safe Singleton managing the PDO database connection.
 * Supports prepared statements, transactions, and fallback between MySQL & SQLite.
 * Perfect for college projects: clean, readable, robust.
 */
class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;
    private string $activeDriver = 'mysql';

    private function __construct()
    {
        $this->connect();
    }

    /**
     * Get the singleton Database instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the active PDO connection
     */
    public static function pdo(): PDO
    {
        return self::getInstance()->getConnection();
    }

    public function getConnection(): PDO
    {
        if ($this->pdo === null) {
            $this->connect();
        }
        return $this->pdo;
    }

    public function getActiveDriver(): string
    {
        return $this->activeDriver;
    }

    /**
     * Establish the PDO connection according to app config
     */
    private function connect(): void
    {
        $config = require dirname(__DIR__) . '/config/database.php';
        $driver = $config['default'] ?? 'mysql';

        if ($driver === 'mysql') {
            try {
                $mc = $config['connections']['mysql'];
                $dsn = "mysql:host={$mc['host']};port={$mc['port']};dbname={$mc['database']};charset={$mc['charset']}";
                $this->pdo = new PDO($dsn, $mc['username'], $mc['password'], $mc['options']);
                $this->activeDriver = 'mysql';
                return;
            } catch (PDOException $e) {
                // If MySQL fails and fallback_sqlite is enabled, switch to SQLite
                if (!empty($config['fallback_sqlite'])) {
                    $this->connectSqlite($config);
                    return;
                }
                throw new Exception("Database Connection Failed: " . $e->getMessage());
            }
        } else {
            $this->connectSqlite($config);
        }
    }

    /**
     * Establish SQLite connection as fallback or primary
     */
    private function connectSqlite(array $config): void
    {
        $sc = $config['connections']['sqlite'];
        $dbPath = $sc['database'];

        // Ensure database folder exists
        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $dsn = "sqlite:{$dbPath}";
        $this->pdo = new PDO($dsn, null, null, $sc['options']);
        $this->pdo->exec("PRAGMA foreign_keys = ON;");
        $this->activeDriver = 'sqlite';

        // Auto-initialize tables if store.sqlite is newly created
        $this->ensureSqliteSchema($dbPath);
    }

    /**
     * Automatically ensure SQLite tables exist if running on SQLite
     */
    private function ensureSqliteSchema(string $dbPath): void
    {
        $check = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        if (!$check->fetch()) {
            // Run SQLite migration script
            require_once dirname(__DIR__, 2) . '/database/migrate.php';
            runSqliteMigration($this->pdo);
        }
    }

    /**
     * Execute a prepared query and return the statement
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single record
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    /**
     * Fetch all records
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Execute an INSERT query and return last inserted ID
     */
    public function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $fields = implode(', ', array_map(fn($col) => "`{$col}`", $columns));
        $placeholders = implode(', ', array_map(fn($col) => ":{$col}", $columns));

        $sql = "INSERT INTO `{$table}` ({$fields}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Execute an UPDATE query with WHERE clause
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setParts = [];
        $params = [];

        foreach ($data as $col => $val) {
            $setParts[] = "`{$col}` = :set_{$col}";
            $params["set_{$col}"] = $val;
        }

        $setString = implode(', ', $setParts);
        $sql = "UPDATE `{$table}` SET {$setString} WHERE {$where}";
        
        $mergedParams = array_merge($params, $whereParams);
        $stmt = $this->query($sql, $mergedParams);
        return $stmt->rowCount();
    }

    /**
     * Execute a DELETE query with WHERE clause
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Transaction helper
     */
    public function transaction(callable $callback): mixed
    {
        $this->pdo->beginTransaction();
        try {
            $result = $callback($this);
            $this->pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    // Prevent cloning and un-serialization
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

