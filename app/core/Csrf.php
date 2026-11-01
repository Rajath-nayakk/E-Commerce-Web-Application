<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Class Csrf
 * 
 * Provides Cross-Site Request Forgery (CSRF) protection with cryptographically
 * secure token generation, timing-attack-safe validation (hash_equals),
 * and support for both HTML form fields and AJAX X-CSRF-TOKEN headers.
 */
class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    /**
     * Get existing CSRF token or generate a new one
     */
    public static function getToken(): string
    {
        Session::start();
        $token = Session::get(self::SESSION_KEY);
        if (!$token || !is_string($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }
        return $token;
    }

    /**
     * Generate an HTML hidden input tag for forms
     */
    public static function field(): string
    {
        $token = htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Validate an incoming token against session token using timing-safe comparison
     */
    public static function validate(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        Session::start();
        $known = Session::get(self::SESSION_KEY);
        if (!$known || !is_string($known)) {
            return false;
        }

        return hash_equals($known, $token);
    }

    /**
     * Validate from standard POST, JSON body, or HTTP Request Headers
     */
    public static function validateRequest(?Request $request = null): bool
    {
        // 1. Check standard POST body
        if (isset($_POST['csrf_token']) && self::validate((string) $_POST['csrf_token'])) {
            return true;
        }

        // 2. Check Request object input or parsed JSON payload
        if ($request !== null) {
            $token = $request->post('csrf_token') ?? $request->input('csrf_token');
            if ($token && is_string($token) && self::validate($token)) {
                return true;
            }
        } else {
            // Direct inspection of JSON input stream if Request object was not provided
            $rawInput = @file_get_contents('php://input');
            if (!empty($rawInput)) {
                $decoded = @json_decode($rawInput, true);
                if (is_array($decoded) && !empty($decoded['csrf_token']) && is_string($decoded['csrf_token'])) {
                    if (self::validate($decoded['csrf_token'])) {
                        return true;
                    }
                }
            }
        }

        // 3. Check X-CSRF-TOKEN or X-XSRF-TOKEN HTTP request headers
        $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['HTTP_X_XSRF_TOKEN'] ?? null;
        if ($headerToken && is_string($headerToken) && self::validate($headerToken)) {
            return true;
        }

        return false;
    }
}

