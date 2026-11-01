<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/Autoloader.php';
require_once __DIR__ . '/../app/config/config.php';

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Core\Auth;
use App\Core\Session;

Session::start();
Session::destroy();
Session::start();

// 1. Guest Cart
$add1 = Cart::addItem(1, 2);
if (!$add1['success']) {
    echo "FAILED: Guest add item 1\n";
    exit(1);
}

$items = Cart::getItems();
if (count($items) !== 1 || $items[0]['quantity'] !== 2) {
    echo "FAILED: Guest cart items count or quantity mismatch\n";
    exit(1);
}

$totals = Cart::getTotals();
if ($totals['total_quantity'] !== 2 || $totals['subtotal'] <= 0) {
    echo "FAILED: Guest cart totals\n";
    exit(1);
}

// 2. Test Login & Merge
$user = User::findByEmail('user@store.com');
Auth::login($user);

// Now logged in: cart items should be in DB cart
$userItems = Cart::getItems();
if (count($userItems) !== 1 || $userItems[0]['product_id'] !== 1 || $userItems[0]['quantity'] !== 2) {
    echo "FAILED: Cart merge into DB on login\n";
    exit(1);
}

// 3. Update quantity in DB
$upd = Cart::updateQuantity(1, 3);
if (!$upd['success']) {
    echo "FAILED: Update quantity in DB\n";
    exit(1);
}

$userItems2 = Cart::getItems();
if ($userItems2[0]['quantity'] !== 3) {
    echo "FAILED: Quantity after update mismatch\n";
    exit(1);
}

// 4. Remove item
Cart::removeItem(1);
$userItems3 = Cart::getItems();
if (count($userItems3) !== 0) {
    echo "FAILED: Cart removeItem\n";
    exit(1);
}

Auth::logout();
echo "SUCCESS: Phase 4 cart (guest, user, and merge on login) verified!\n";
