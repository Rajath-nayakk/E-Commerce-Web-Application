<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Class Response
 * 
 * Manages HTTP response output, status codes, headers, JSON payloads, and redirects.
 */
class Response
{
    public static function status(int $code): void
    {
        http_response_code($code);
    }

    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    public static function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    public static function error(string $message, int $status = 400, array $errors = []): void
    {
        self::json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], $status);
    }

    public static function success(string $message, mixed $data = null, int $status = 200): void
    {
        $payload = [
            'success' => true,
            'message' => $message
        ];
        if ($data !== null) {
            $payload['data'] = $data;
        }
        self::json($payload, $status);
    }
}

