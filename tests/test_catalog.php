<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/Autoloader.php';
require_once __DIR__ . '/../app/config/config.php';

use App\Models\Product;
use App\Models\Category;

// Test catalog querying
$all = Product::getCatalog([], 1, 9);
if (count($all['items']) !== 9 || $all['total_items'] !== 24) {
    echo "FAILED: Catalog default fetch\n";
    exit(1);
}

// Test search filter
$search = Product::getCatalog(['q' => 'Monitor'], 1, 9);
if (count($search['items']) < 1) {
    echo "FAILED: Search for Monitor\n";
    exit(1);
}

// Test category filter
$catFilter = Product::getCatalog(['category' => 'electronics'], 1, 9);
if (count($catFilter['items']) !== 4) {
    echo "FAILED: Category electronics count should be 4, got " . count($catFilter['items']) . "\n";
    exit(1);
}

// Test product detail by slug
$p = Product::findWithCategory('ultraslim-4k-oled-monitor-27');
if (!$p || $p['name'] !== 'UltraSlim 4K OLED Monitor 27"') {
    echo "FAILED: Product findWithCategory slug\n";
    exit(1);
}

echo "SUCCESS: Phase 3 catalog, search, category filter, and product detail verified!\n";
