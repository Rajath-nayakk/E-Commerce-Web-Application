<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/Autoloader.php';
require_once __DIR__ . '/../app/config/config.php';

use App\Models\Product;
use App\Models\Cart;

// 1. Products list
$catalog = Product::getCatalog([], 1, 5);
if (count($catalog['items']) !== 5) {
    echo "FAILED: API Catalog fetch\n";
    exit(1);
}

// 2. Product detail
$p = Product::findWithCategory(1);
if (!$p || (int) $p['id'] !== 1) {
    echo "FAILED: API Product detail fetch\n";
    exit(1);
}

// 3. Cart endpoints simulation
Cart::clear();
$add = Cart::addItem(1, 1);
if (!$add['success']) {
    echo "FAILED: API Cart add\n";
    exit(1);
}
$totals = Cart::getTotals();
if ($totals['total_quantity'] !== 1) {
    echo "FAILED: API Cart totals\n";
    exit(1);
}
Cart::clear();

echo "SUCCESS: Phase 7 JSON APIs verified!\n";

