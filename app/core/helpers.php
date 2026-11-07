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
        if (str_starts_with($filename, 'http://') || str_starts_with($filename, 'https://')) {
            return $filename;
        }

        $cleanFile = ltrim($filename, '/');
        $cleanFolder = trim($folder, '/');
        $publicPath = dirname(__DIR__, 2) . '/public';

        // Check if file exists in public/images/
        if (file_exists($publicPath . '/images/' . $cleanFolder . '/' . $cleanFile)) {
            return '/images/' . $cleanFolder . '/' . $cleanFile;
        }

        // Check if file exists in public/uploads/
        if (file_exists($publicPath . '/uploads/' . $cleanFolder . '/' . $cleanFile)) {
            return '/uploads/' . $cleanFolder . '/' . $cleanFile;
        }

        return '/images/' . $cleanFolder . '/' . $cleanFile;
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

if (!function_exists('format_inr')) {
    /**
     * Format numeric values into Indian Rupees (INR) with Indian comma notation (₹X,XX,XXX.XX)
     */
    function format_inr(float|int|string|null $amount, bool $includeDecimals = true): string
    {
        $val = (float) ($amount ?? 0);
        $isNegative = $val < 0;
        $val = abs($val);

        $formatted = number_format($val, 2, '.', '');
        [$integerPart, $decimalPart] = explode('.', $formatted);

        // Indian numbering grouping: last 3 digits, then groups of 2
        if (strlen($integerPart) > 3) {
            $lastThree = substr($integerPart, -3);
            $remaining = substr($integerPart, 0, -3);
            $groups = [];
            while (strlen($remaining) > 2) {
                $groups[] = substr($remaining, -2);
                $remaining = substr($remaining, 0, -2);
            }
            if ($remaining !== '') {
                $groups[] = $remaining;
            }
            $groups = array_reverse($groups);
            $integerPart = implode(',', $groups) . ',' . $lastThree;
        }

        $result = '₹' . ($isNegative ? '-' : '') . $integerPart;
        if ($includeDecimals) {
            $result .= '.' . $decimalPart;
        }
        return $result;
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Centralized Currency Helper for NovaTech Gear
     */
    function formatCurrency(float|int|string|null $amount, bool $includeDecimals = true): string
    {
        return format_inr($amount, $includeDecimals);
    }
}

if (!function_exists('format_price')) {
    /**
     * Standard price formatting helper alias
     */
    function format_price(float|int|string|null $amount, bool $includeDecimals = true): string
    {
        return format_inr($amount, $includeDecimals);
    }
}

if (!function_exists('discount_percent')) {
    /**
     * Calculate discount percentage from MRP and selling price
     */
    function discount_percent(float|int|string|null $originalPrice, float|int|string|null $sellingPrice): int
    {
        $mrp = (float) ($originalPrice ?? 0);
        $sell = (float) ($sellingPrice ?? 0);
        if ($mrp <= 0 || $sell >= $mrp) {
            return 0;
        }
        return (int) round((($mrp - $sell) / $mrp) * 100);
    }
}

if (!function_exists('render_stars')) {
    /**
     * Render star rating icons for product reviews
     */
    function render_stars(float|int|string|null $rating, int $max = 5): string
    {
        $val = round((float) ($rating ?? 4.5) * 2) / 2; // round to nearest 0.5
        $html = '';
        for ($i = 1; $i <= $max; $i++) {
            if ($val >= $i) {
                $html .= '<i class="bi bi-star-fill text-warning"></i>';
            } elseif ($val >= $i - 0.5) {
                $html .= '<i class="bi bi-star-half text-warning"></i>';
            } else {
                $html .= '<i class="bi bi-star text-muted opacity-50"></i>';
            }
        }
        return $html;
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return Session::get('_old_input')[$key] ?? $default;
    }
}

