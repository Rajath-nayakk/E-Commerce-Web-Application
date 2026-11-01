<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Class Request
 * 
 * Encapsulates the HTTP request: URI routing, query parameters, POST data,
 * JSON body payloads, file uploads, and client metadata.
 */
class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private ?array $json = null;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;

        // Parse JSON payloads if application/json header is sent
        $contentType = $this->server['CONTENT_TYPE'] ?? $this->server['HTTP_CONTENT_TYPE'] ?? '';
        if (str_contains(strtolower($contentType), 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->json = $decoded;
            }
        }
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        // Strip query string
        $pos = strpos($uri, '?');
        if ($pos !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        $uri = '/' . trim($uri, '/');
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        // Support method spoofing for PUT/DELETE
        if ($method === 'POST') {
            if (isset($this->post['_method'])) {
                return strtoupper($this->post['_method']);
            }
            if (isset($this->server['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                return strtoupper($this->server['HTTP_X_HTTP_METHOD_OVERRIDE']);
            }
        }
        return $method;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        if ($this->json !== null && array_key_exists($key, $this->json)) {
            return $this->json[$key];
        }
        return $this->post[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post($key, $this->get($key, $default));
    }

    public function all(): array
    {
        if ($this->json !== null) {
            return array_merge($this->get, $this->json);
        }
        return array_merge($this->get, $this->post);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function isAjax(): bool
    {
        return !empty($this->server['HTTP_X_REQUESTED_WITH']) &&
            strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function isJson(): bool
    {
        $contentType = $this->server['CONTENT_TYPE'] ?? $this->server['HTTP_CONTENT_TYPE'] ?? '';
        $accept = $this->server['HTTP_ACCEPT'] ?? '';
        return str_contains(strtolower($contentType), 'application/json') || str_contains(strtolower($accept), 'application/json');
    }

    public function ip(): string
    {
        return $this->server['HTTP_X_FORWARDED_FOR'] ?? $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function header(string $name, ?string $default = null): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$key] ?? $default;
    }
}

