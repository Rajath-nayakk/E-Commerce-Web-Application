<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class PasswordReset
 * 
 * Manages cryptographically secure one-time password recovery tokens.
 */
class PasswordReset extends Model
{
    protected static string $table = 'password_resets';

    /**
     * Create a password reset token for a user (valid for 60 minutes)
     * Returns the raw token (to be sent via email or displayed in demo)
     */
    public static function createToken(int $userId): string
    {
        // Invalidate old tokens for this user
        static::db()->delete(static::$table, 'user_id = :uid', ['uid' => $userId]);

        $rawToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

        static::create([
            'user_id'    => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $rawToken;
    }

    /**
     * Find valid reset record by raw token
     */
    public static function findValidToken(string $rawToken): ?array
    {
        $tokenHash = hash('sha256', $rawToken);
        $now = date('Y-m-d H:i:s');

        $sql = "SELECT pr.*, u.id as user_id, u.email, u.name 
                FROM password_resets pr
                JOIN users u ON pr.user_id = u.id
                WHERE pr.token_hash = :hash AND pr.expires_at > :now
                LIMIT 1";

        return static::db()->fetchOne($sql, ['hash' => $tokenHash, 'now' => $now]);
    }

    /**
     * Delete token record after successful reset
     */
    public static function deleteToken(string $rawToken): void
    {
        $tokenHash = hash('sha256', $rawToken);
        static::db()->delete(static::$table, 'token_hash = :hash', ['hash' => $tokenHash]);
    }
}
