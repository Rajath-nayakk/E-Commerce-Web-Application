<?php
declare(strict_types=1);

/**
 * ----------------------------------------------------------------------------
 * Environment & Application Configuration
 * ----------------------------------------------------------------------------
 * Loads environment variables from .env if present and provides safe defaults.
 * Keeps sensitive credentials out of the codebase for college viva compliance.
 */

// Simple robust .env loader without third-party dependencies
(function () {
    $envFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
    if (!file_exists($envFile)) {
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);

            // Strip surrounding quotes
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = substr($val, 1, -1);
            }

            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $val;
                putenv("{$key}={$val}");
            }
        }
    }
})();

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        $val = $_ENV[$key] ?? getenv($key);
        if ($val === false || $val === null) {
            return $default;
        }

        return match (strtolower((string) $val)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $val,
        };
    }
}

return [
    'app_name'      => env('APP_NAME', 'NovaTech Gear'),
    'app_env'       => env('APP_ENV', 'development'),
    'app_debug'     => env('APP_DEBUG', true),
    'app_url'       => rtrim(env('APP_URL', 'http://localhost:8000'), '/'),
    'app_secret'    => env('APP_SECRET', 'super-secret-key-32-chars-minimum'),
    'currency'      => '$',
    'session_name'  => 'novatech_sess',
    'session_life'  => (int) env('SESSION_LIFETIME', 7200),
    'login_max_att' => (int) env('LOGIN_MAX_ATTEMPTS', 5),
    'login_lockout' => (int) env('LOGIN_LOCKOUT_MINUTES', 15),
    'upload_max'    => (int) env('UPLOAD_MAX_BYTES', 5242880), // 5MB
    'allowed_types' => explode(',', env('ALLOWED_IMAGE_TYPES', 'image/jpeg,image/png,image/webp,image/svg+xml')),
    'paths' => [
        'base'     => dirname(__DIR__, 2),
        'app'      => dirname(__DIR__),
        'public'   => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public',
        'uploads'  => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads',
        'database' => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'database',
    ]
];
