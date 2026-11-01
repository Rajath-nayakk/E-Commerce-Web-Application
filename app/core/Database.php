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
        $driver = strtolower(trim((string) ($config['default'] ?? 'mysql')));

        if ($driver === 'mysql') {
            $mc = $config['connections']['mysql'];
            try {
                $dsn = "mysql:host={$mc['host']};port={$mc['port']};dbname={$mc['database']};charset={$mc['charset']}";
                $this->pdo = new PDO($dsn, $mc['username'], $mc['password'], $mc['options']);
                $this->activeDriver = 'mysql';
                return;
            } catch (PDOException $e) {
                // NEVER silently switch database drivers!
                // Report explicit error naming the exact problem.
                $this->handleConnectionFailure($e, $mc);
            }
        } elseif ($driver === 'sqlite') {
            $this->connectSqlite($config);
        } else {
            throw new Exception("Unsupported database driver: '{$driver}'. Use 'mysql' or 'sqlite'.");
        }
    }

    private function handleConnectionFailure(PDOException $e, array $mc): void
    {
        $message = "Cannot connect to MySQL database '{$mc['database']}' on {$mc['host']}:{$mc['port']}. Error: " . $e->getMessage();

        if (php_sapi_name() === 'cli') {
            throw new Exception($message, (int) $e->getCode(), $e);
        }

        // Web Request: Check if JSON or HTML requested
        $isJson = (!empty($_SERVER['CONTENT_TYPE']) && str_contains(strtolower($_SERVER['CONTENT_TYPE']), 'application/json')) ||
                  (!empty($_SERVER['HTTP_ACCEPT']) && str_contains(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json')) ||
                  (!empty($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], '/api'));

        http_response_code(500);

        if ($isJson) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error'   => 'Database connection failed',
                'message' => $message,
                'hint'    => "Verify MySQL is running on {$mc['host']}:{$mc['port']}, or set DB_DRIVER=sqlite in .env for explicit SQLite mode."
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $errorMessage = $e->getMessage();
        $dbName = $mc['database'];
        $dbHost = $mc['host'];
        $dbPort = $mc['port'];

        $viewFile = dirname(__DIR__) . '/views/errors/db_error.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "<h1>Database Connection Failed</h1><p>" . htmlspecialchars($message) . "</p>";
        }
        exit;
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

