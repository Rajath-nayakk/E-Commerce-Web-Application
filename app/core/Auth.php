<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Models\Cart;

/**
 * Class Auth
 * 
 * Central authentication, authorization, role middleware, and security guardian.
 * Handles rate-limiting on login, session regeneration, role verification,
 * and cart synchronization upon login.
 */
class Auth
{
    private const SESSION_USER_ID = '_auth_user_id';
    private const SESSION_USER_ROLE = '_auth_user_role';
    private const SESSION_USER_DATA = '_auth_user_data';
    private const RATE_LIMIT_PREFIX = '_rate_limit_login_';

    /**
     * Check if a user is currently logged in
     */
    public static function check(): bool
    {
        Session::start();
        return Session::has(self::SESSION_USER_ID);
    }

    /**
     * Check if current visitor is a guest
     */
    public static function isGuest(): bool
    {
        return !self::check();
    }

    /**
     * Get logged-in user ID
     */
    public static function id(): ?int
    {
        Session::start();
        $id = Session::get(self::SESSION_USER_ID);
        return $id !== null ? (int) $id : null;
    }

    /**
     * Get logged-in user role ('user' | 'admin')
     */
    public static function role(): ?string
    {
        Session::start();
        return Session::get(self::SESSION_USER_ROLE);
    }

    /**
     * Check if logged-in user is an administrator
     */
    public static function isAdmin(): bool
    {
        return self::check() && self::role() === 'admin';
    }

    /**
     * Get current user entity array
     */
    public static function user(): ?array
    {
        Session::start();
        return Session::get(self::SESSION_USER_DATA);
    }

    /**
     * Authenticate a user into session, regenerate session ID, and merge guest cart
     */
    public static function login(array $user): void
    {
        Session::start();
        // Prevent session fixation attack
        Session::regenerate(true);

        // Sanitize sensitive fields from session
        unset($user['password_hash']);

        Session::set(self::SESSION_USER_ID, (int) $user['id']);
        Session::set(self::SESSION_USER_ROLE, $user['role']);
        Session::set(self::SESSION_USER_DATA, $user);

        // Clear failed login attempts on success
        self::clearLoginAttempts($user['email']);

        // Merge guest session cart into database cart
        Cart::mergeGuestCart((int) $user['id']);
    }

    /**
     * Log out current user, clear session, and regenerate session token
     */
    public static function logout(): void
    {
        Session::start();
        Session::remove(self::SESSION_USER_ID);
        Session::remove(self::SESSION_USER_ROLE);
        Session::remove(self::SESSION_USER_DATA);
        Session::destroy();
        Session::start();
        Session::regenerate(true);
    }

    /**
     * Reusable middleware check: require authenticated user
     */
    public static function requireLogin(Request $request): void
    {
        if (!self::check()) {
            if ($request->isJson() || str_starts_with($request->uri(), '/api')) {
                Response::error('Authentication required. Please sign in.', 401);
                return;
            }
            Session::flash('warning', 'Please sign in to access that page.');
            Response::redirect('/login');
        }
    }

    /**
     * Reusable middleware check: require administrator privileges
     */
    public static function requireAdmin(Request $request): void
    {
        if (!self::check()) {
            if ($request->isJson() || str_starts_with($request->uri(), '/api')) {
                Response::error('Authentication required.', 401);
                return;
            }
            Session::flash('warning', 'Please sign in with administrator credentials.');
            Response::redirect('/login');
        }

        if (!self::isAdmin()) {
            if ($request->isJson() || str_starts_with($request->uri(), '/api')) {
                Response::error('Access denied. Administrator privileges required.', 403);
                return;
            }
            Response::status(403);
            View::render('errors/403', ['title' => 'Access Denied: Admin Only']);
            exit;
        }
    }

    /**
     * Reusable middleware check: require guest (redirect logged-in users away from /login, /register)
     */
    public static function requireGuest(Request $request): void
    {
        if (self::check()) {
            if (self::isAdmin()) {
                Response::redirect('/admin');
            }
            Response::redirect('/');
        }
    }

    /**
     * Rate-limiting: Check if IP or email is currently locked out
     */
    public static function isRateLimited(string $identifier): bool
    {
        Session::start();
        $key = self::RATE_LIMIT_PREFIX . md5(strtolower(trim($identifier)));
        $record = Session::get($key);

        if (!$record) {
            return false;
        }

        $config = require dirname(__DIR__) . '/config/config.php';
        $maxAttempts = $config['login_max_att'] ?? 5;
        $lockoutMinutes = $config['login_lockout'] ?? 15;

        if ($record['attempts'] >= $maxAttempts) {
            $lockoutRemaining = ($record['last_attempt'] + ($lockoutMinutes * 60)) - time();
            if ($lockoutRemaining > 0) {
                return true;
            } else {
                // Lockout expired, reset
                Session::remove($key);
                return false;
            }
        }

        return false;
    }

    /**
     * Rate-limiting: Increment failed login attempt counter
     */
    public static function incrementLoginAttempts(string $identifier): int
    {
        Session::start();
        $key = self::RATE_LIMIT_PREFIX . md5(strtolower(trim($identifier)));
        $record = Session::get($key, ['attempts' => 0, 'last_attempt' => time()]);

        $record['attempts']++;
        $record['last_attempt'] = time();

        Session::set($key, $record);
        return $record['attempts'];
    }

    /**
     * Rate-limiting: Clear login attempts upon successful authentication
     */
    public static function clearLoginAttempts(string $identifier): void
    {
        Session::start();
        $key = self::RATE_LIMIT_PREFIX . md5(strtolower(trim($identifier)));
        Session::remove($key);
    }

    /**
     * Get remaining lockout seconds
     */
    public static function getLockoutRemaining(string $identifier): int
    {
        Session::start();
        $key = self::RATE_LIMIT_PREFIX . md5(strtolower(trim($identifier)));
        $record = Session::get($key);
        if (!$record) {
            return 0;
        }

        $config = require dirname(__DIR__) . '/config/config.php';
        $lockoutMinutes = $config['login_lockout'] ?? 15;
        $remaining = ($record['last_attempt'] + ($lockoutMinutes * 60)) - time();
        return max(0, $remaining);
    }
}

