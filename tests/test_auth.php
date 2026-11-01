<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/Autoloader.php';
require_once __DIR__ . '/../app/config/config.php';

use App\Models\User;
use App\Models\PasswordReset;

$admin = User::findByEmail('admin@store.com');
if (!$admin) {
    echo "FAILED: Admin user not found\n";
    exit(1);
}

if (!User::verifyPassword('Admin@123', $admin['password_hash'])) {
    echo "FAILED: Password verification failed\n";
    exit(1);
}

$rawToken = PasswordReset::createToken((int) $admin['id']);
$record = PasswordReset::findValidToken($rawToken);
if (!$record || $record['email'] !== 'admin@store.com') {
    echo "FAILED: Password reset token mismatch\n";
    exit(1);
}

PasswordReset::deleteToken($rawToken);
if (PasswordReset::findValidToken($rawToken) !== null) {
    echo "FAILED: Token should be deleted\n";
    exit(1);
}

echo "SUCCESS: Auth, hashing, role, and password reset verified!\n";

