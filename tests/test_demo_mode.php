<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/Autoloader.php';
require_once __DIR__ . '/../app/core/helpers.php';
require_once __DIR__ . '/../app/config/config.php';

use App\Core\View;

// Test 1: Rendering login view with isDemo = false
ob_start();
View::render('auth/login', [
    'title'  => 'Sign In',
    'isDemo' => false
], null);
$htmlNoDemo = ob_get_clean();

if (str_contains($htmlNoDemo, '1-Click Demo Login') || str_contains($htmlNoDemo, 'fillLogin(')) {
    echo "FAILED: 1-Click Demo Login button rendered when isDemo=false\n";
    exit(1);
}

// Test 2: Rendering login view with isDemo = true
ob_start();
View::render('auth/login', [
    'title'  => 'Sign In',
    'isDemo' => true
], null);
$htmlWithDemo = ob_get_clean();

if (!str_contains($htmlWithDemo, '1-Click Demo Login') || !str_contains($htmlWithDemo, 'fillLogin(')) {
    echo "FAILED: 1-Click Demo Login button NOT rendered when isDemo=true\n";
    exit(1);
}

echo "SUCCESS: Phase 9 Demo-mode login verified!\n";
