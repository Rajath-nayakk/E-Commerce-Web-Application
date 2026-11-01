<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/Autoloader.php';
require_once __DIR__ . '/../app/config/config.php';

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderStatusHistory;

$user = User::findByEmail('user@store.com');
$product = Product::find(2); // Thunderbolt dock
$initialStock = $product['stock'];

// 1. Create order
$orderItems = [
    [
        'product_id' => $product['id'],
        'price'      => $product['price'],
        'quantity'   => 2
    ]
];

$orderId = Order::createOrder(
    (int) $user['id'],
    [
        'name'    => 'Jane Customer',
        'address' => '123 Tech Boulevard',
        'phone'   => '555-1234'
    ],
    'cod',
    $orderItems,
    ((float) $product['price']) * 2
);

// Verify stock deducted
$productAfter = Product::find(2);
if ($productAfter['stock'] !== $initialStock - 2) {
    echo "FAILED: Stock was not deducted correctly. Expected " . ($initialStock - 2) . " got {$productAfter['stock']}\n";
    exit(1);
}

// Verify order record & items
$order = Order::findWithDetails($orderId);
if (!$order || count($order['items']) !== 1 || $order['status'] !== 'pending') {
    echo "FAILED: Order details retrieval\n";
    exit(1);
}

// Verify initial timeline log
if (count($order['timeline']) < 1 || $order['timeline'][0]['status'] !== 'pending') {
    echo "FAILED: Order status timeline initial log\n";
    exit(1);
}

// 2. Test status update: pending -> processing -> shipped
Order::updateStatus($orderId, 'processing', 'Package prepared at fulfillment hub');
Order::updateStatus($orderId, 'shipped', 'Carrier tracking #NVT-982187');

$orderShipped = Order::findWithDetails($orderId);
if ($orderShipped['status'] !== 'shipped' || count($orderShipped['timeline']) !== 3) {
    echo "FAILED: Status transitions to shipped\n";
    exit(1);
}

// 3. Test cancellation stock restore
Order::updateStatus($orderId, 'cancelled', 'Customer requested cancellation before delivery');
$productRestored = Product::find(2);
if ($productRestored['stock'] !== $initialStock) {
    echo "FAILED: Stock was not restored upon cancellation. Expected {$initialStock} got {$productRestored['stock']}\n";
    exit(1);
}

echo "SUCCESS: Phase 5 checkout, transactional stock deduction, order tracking, and timeline verified!\n";

