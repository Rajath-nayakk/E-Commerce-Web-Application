<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class User
 * 
 * Manages user accounts, authentication credential verification,
 * role authorization, and password hashing with BCRYPT.
 */
class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        return static::firstWhere('email', strtolower(trim($email)));
    }

    public static function register(string $name, string $email, string $password, string $role = 'user'): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        return static::create([
            'name'          => trim($name),
            'email'         => strtolower(trim($email)),
            'password_hash' => $hash,
            'role'          => in_array($role, ['admin', 'user'], true) ? $role : 'user',
        ]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function updatePassword(int $userId, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 10]);
        return (bool) static::update($userId, ['password_hash' => $hash]);
    }

    public static function getAllWithOrderStats(): array
    {
        $sql = "SELECT u.id, u.name, u.email, u.role, u.created_at,
                       COUNT(DISTINCT o.id) as total_orders,
                       COALESCE(SUM(o.total), 0) as total_spent
                FROM users u
                LEFT JOIN orders o ON u.id = o.user_id
                GROUP BY u.id, u.name, u.email, u.role, u.created_at
                ORDER BY u.created_at DESC";
        return static::db()->fetchAll($sql);
    }
}

