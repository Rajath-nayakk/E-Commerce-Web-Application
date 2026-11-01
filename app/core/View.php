<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Class View
 * 
 * Secure template renderer. Escapes HTML by default, supports master layouts,
 * partials, component alerts, and helper functions for links and assets.
 */
class View
{
    /**
     * Render a view file with optional data and layout
     */
    public static function render(string $viewPath, array $data = [], ?string $layout = 'layouts/main'): void
    {
        // Extract data variables into local scope
        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__) . '/views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "<h1>View Not Found</h1><p>Missing template: " . htmlspecialchars($viewFile) . "</p>";
            return;
        }

        // Buffer the view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // If layout requested, wrap inside layout
        if ($layout !== null) {
            $layoutFile = dirname(__DIR__) . '/views/' . str_replace('.', '/', $layout) . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
                return;
            }
        }

        echo $content;
    }
}

// Global helper functions for views
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
