<?php
declare(strict_types=1);

namespace App\Models;

use Exception;

/**
 * Class Order
 * 
 * Manages orders, checkout transactions, inventory reservation,
 * status timeline audits, and administrative reporting.
 */
class Order extends Model
{
    protected static string $table = 'orders';

    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED    = 'shipped';
    public const STATUS_DELIVERED  = 'delivered';
    public const STATUS_CANCELLED  = 'cancelled';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED    => 'Shipped',
            self::STATUS_DELIVERED  => 'Delivered',
            self::STATUS_CANCELLED  => 'Cancelled',
        ];
    }

    /**
     * Create an order atomically inside a database transaction
     * Deducts stock and logs initial status
     */
    public static function createOrder(
        int $userId,
        array $shippingData,
        string $paymentMethod,
        array $cartItems,
        float $totalAmount
    ): int {
        return self::db()->transaction(function () use ($userId, $shippingData, $paymentMethod, $cartItems, $totalAmount) {
            // 1. Verify availability and deduct stock for every item
            foreach ($cartItems as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];

                $product = self::db()->fetchOne(
                    "SELECT id, name, stock, price FROM products WHERE id = :id",
                    ['id' => $productId]
                );

                if (!$product) {
                    throw new Exception("Product ID #{$productId} no longer exists.");
                }

                if ($product['stock'] < $quantity) {
                    throw new Exception("Insufficient stock for '{$product['name']}'. Only {$product['stock']} available.");
                }

                // Deduct stock safely with distinct parameter names for MySQL compatibility
                $updated = self::db()->query(
                    "UPDATE products SET stock = stock - :qty WHERE id = :id AND stock >= :min_stock",
                    ['qty' => $quantity, 'id' => $productId, 'min_stock' => $quantity]
                )->rowCount();

                if ($updated === 0) {
                    throw new Exception("Stock update failed for '{$product['name']}'. Please try again.");
                }
            }

            // 2. Insert main Order record
            $orderId = self::create([
                'user_id'          => $userId,
                'total'            => $totalAmount,
                'status'           => self::STATUS_PENDING,
                'payment_method'   => $paymentMethod,
                'shipping_name'    => trim($shippingData['name']),
                'shipping_address' => trim($shippingData['address']),
                'shipping_phone'   => trim($shippingData['phone']),
                'created_at'       => date('Y-m-d H:i:s'),
            ]);

            // 3. Insert line items (snapshotting current purchase price)
            $stmtItem = self::db()->pdo()->prepare(
                "INSERT INTO order_items (order_id, product_id, price_at_purchase, quantity) 
                 VALUES (:order_id, :product_id, :price, :qty)"
            );

            foreach ($cartItems as $item) {
                $stmtItem->execute([
                    'order_id'   => $orderId,
                    'product_id' => (int) $item['product_id'],
                    'price'      => (float) $item['price'],
                    'qty'        => (int) $item['quantity'],
                ]);
            }

            // 4. Record initial status history event
            OrderStatusHistory::log(
                $orderId,
                self::STATUS_PENDING,
                "Order placed successfully via " . strtoupper($paymentMethod) . "."
            );

            // 5. Empty user shopping cart
            Cart::clear();

            return $orderId;
        });
    }

    /**
     * Get order with line items, product info, and customer info
     */
    public static function findWithDetails(int $orderId, ?int $userId = null): ?array
    {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = :id";
        $params = ['id' => $orderId];

        if ($userId !== null) {
            $sql .= " AND o.user_id = :uid";
            $params['uid'] = $userId;
        }

        $order = self::db()->fetchOne($sql, $params);
        if (!$order) {
            return null;
        }

        // Fetch line items
        $itemSql = "SELECT oi.*, p.name as product_name, p.slug as product_slug, 
                           p.image as product_image, c.name as category_name
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    JOIN categories c ON p.category_id = c.id
                    WHERE oi.order_id = :oid";
        $order['items'] = self::db()->fetchAll($itemSql, ['oid' => $orderId]);

        // Fetch status history timeline
        $order['timeline'] = OrderStatusHistory::getForOrder($orderId);

        return $order;
    }

    /**
     * Fetch all orders for a specific user ("My Orders")
     */
    public static function getUserOrders(int $userId): array
    {
        $sql = "SELECT o.*, COUNT(oi.id) as item_count
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = :uid
                GROUP BY o.id
                ORDER BY o.created_at DESC";
        $orders = self::db()->fetchAll($sql, ['uid' => $userId]);

        // Attach items snippet to each order
        foreach ($orders as &$ord) {
            $ord['items'] = self::db()->fetchAll(
                "SELECT oi.*, p.name as product_name, p.image as product_image 
                 FROM order_items oi 
                 JOIN products p ON oi.product_id = p.id 
                 WHERE oi.order_id = :oid",
                ['oid' => $ord['id']]
            );
        }
        return $orders;
    }

    /**
     * Update order status with timeline event and optional stock replenishment on cancellation
     */
    public static function updateStatus(int $orderId, string $newStatus, ?string $note = null): bool
    {
        $validStatuses = array_keys(self::getStatuses());
        if (!in_array($newStatus, $validStatuses, true)) {
            return false;
        }

        $order = self::find($orderId);
        if (!$order) {
            return false;
        }

        $oldStatus = $order['status'];
        if ($oldStatus === $newStatus) {
            return true;
        }

        return self::db()->transaction(function () use ($orderId, $order, $oldStatus, $newStatus, $note) {
            // If transitioning to cancelled from non-cancelled, restore stock!
            if ($newStatus === self::STATUS_CANCELLED && $oldStatus !== self::STATUS_CANCELLED) {
                $items = self::db()->fetchAll("SELECT product_id, quantity FROM order_items WHERE order_id = :oid", ['oid' => $orderId]);
                foreach ($items as $item) {
                    Product::restoreStock((int) $item['product_id'], (int) $item['quantity']);
                }
            }

            // Update status in orders table
            self::update($orderId, ['status' => $newStatus]);

            // Append status timeline log
            OrderStatusHistory::log(
                $orderId,
                $newStatus,
                $note ?: "Status updated from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus) . "."
            );

            return true;
        });
    }

    /**
     * Dashboard statistics for administrative overview
     */
    public static function getDashboardMetrics(): array
    {
        // Total sales from non-cancelled orders
        $salesRow = self::db()->fetchOne("SELECT COALESCE(SUM(total), 0) as total_sales FROM orders WHERE status != 'cancelled'");
        $totalSales = (float) ($salesRow['total_sales'] ?? 0);

        // Total order count
        $orderCount = self::count();

        // Total product count
        $productCount = Product::count();

        // Total user count
        $userCount = User::count("role = 'user'");

        // Recent orders (last 5)
        $recentOrdersSql = "SELECT o.*, u.name as customer_name, u.email as customer_email 
                            FROM orders o 
                            JOIN users u ON o.user_id = u.id 
                            ORDER BY o.created_at DESC 
                            LIMIT 5";
        $recentOrders = self::db()->fetchAll($recentOrdersSql);

        // Low stock products (stock <= 5)
        $lowStockProducts = Product::getLowStockAlerts(5);

        // Order status breakdown
        $statusBreakdownSql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $statusBreakdown = self::db()->fetchAll($statusBreakdownSql);

        return [
            'total_sales'        => $totalSales,
            'total_orders'       => $orderCount,
            'total_products'     => $productCount,
            'total_users'        => $userCount,
            'recent_orders'      => $recentOrders,
            'low_stock_products' => $lowStockProducts,
            'status_breakdown'   => $statusBreakdown
        ];
    }
}

