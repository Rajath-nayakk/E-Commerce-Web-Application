<?php
declare(strict_types=1);

/**
 * ----------------------------------------------------------------------------
 * Global Helper Functions
 * ----------------------------------------------------------------------------
 * Available throughout all templates, controllers, and application code.
 */

use App\Core\Csrf;
use App\Core\Session;

if (!function_exists('e')) {
    /**
     * Escape HTML output against XSS attacks
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $path = '/' . ltrim($path, '/');
        return $path;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('upload_url')) {
    function upload_url(?string $filename, string $folder = 'products'): string
    {
        if (empty($filename)) {
            return '/assets/img/placeholder.svg';
        }
        return '/uploads/' . trim($folder, '/') . '/' . ltrim($filename, '/');
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::getToken();
    }
}

if (!function_exists('format_price')) {
    function format_price(float|int|string|null $amount): string
    {
        return '$' . number_format((float) ($amount ?? 0), 2);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return Session::get('_old_input')[$key] ?? $default;
    }
}

