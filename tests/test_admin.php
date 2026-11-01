<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/Autoloader.php';
require_once __DIR__ . '/../app/config/config.php';

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;

// 1. Dashboard metrics test
$metrics = Order::getDashboardMetrics();
if (!isset($metrics['total_sales']) || !isset($metrics['total_orders']) || !isset($metrics['total_products'])) {
    echo "FAILED: Dashboard metrics keys missing\n";
    exit(1);
}

// 2. Category creation test
$catId = Category::create([
    'name' => 'Test Gaming Gear',
    'slug' => Category::generateSlug('Test Gaming Gear')
]);
$cat = Category::find($catId);
if (!$cat || $cat['name'] !== 'Test Gaming Gear') {
    echo "FAILED: Category creation\n";
    exit(1);
}

// 3. Product creation test
$prodId = Product::create([
    'category_id' => $catId,
    'name'        => 'Pro RGB Mechanical Keycaps Set',
    'slug'        => Product::generateSlug('Pro RGB Mechanical Keycaps Set'),
    'description' => 'Double-shot PBT keycaps for custom builds',
    'price'       => 29.99,
    'stock'       => 50,
    'image'       => 'placeholder.svg',
    'is_active'   => 1
]);
$p = Product::find($prodId);
if (!$p || $p['price'] != 29.99) {
    echo "FAILED: Product creation\n";
    exit(1);
}

// Clean up test records
Product::delete($prodId);
Category::delete($catId);

// 4. Users with stats test
$users = User::getAllWithOrderStats();
if (empty($users)) {
    echo "FAILED: Users with stats empty\n";
    exit(1);
}

echo "SUCCESS: Phase 6 Admin metrics, product/category management, and user statistics verified!\n";
