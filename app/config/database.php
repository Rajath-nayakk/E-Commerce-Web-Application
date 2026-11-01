<?php
declare(strict_types=1);

/**
 * ----------------------------------------------------------------------------
 * Database Connection Configuration
 * ----------------------------------------------------------------------------
 * Supports MySQL/MariaDB (primary for XAMPP/WAMP production & grading) and
 * SQLite (convenience fallback for instant zero-setup demonstration).
 */

require_once __DIR__ . '/config.php';

return [
    'default' => env('DB_DRIVER', 'mysql'),
    
    'connections' => [
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'port'      => (int) env('DB_PORT', 3306),
            'database'  => env('DB_NAME', 'ecommerce_db'),
            'username'  => env('DB_USER', 'root'),
            'password'  => env('DB_PASS', ''),
            'charset'   => env('DB_CHARSET', 'utf8mb4'),
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        ],
        
        'sqlite' => [
            'driver'    => 'sqlite',
            'database'  => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . env('DB_SQLITE_PATH', 'database/store.sqlite'),
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        ]
    ]
];

