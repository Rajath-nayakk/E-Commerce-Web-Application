<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Auth;
use App\Core\Session;

/**
 * Class Cart
 * 
 * Manages shopping cart operations:
 * - Session-based cart storage for anonymous guests
 * - Database-backed cart storage for authenticated users
 * - Automatic cart merging when a guest signs in
 * - Stock limit enforcement and live subtotal calculations
 */
class Cart extends Model
{
    protected static string $table = 'cart_items';
    private const GUEST_CART_SESSION_KEY = '_guest_cart';

    /**
     * Get all items in the cart (for current user or guest)
     */
    public static function getItems(): array
    {
        if (Auth::check()) {
            return self::getUserCartItems((int) Auth::id());
        }
        return self::getGuestCartItems();
    }

    /**
     * Get total quantity of items in cart (for navbar badge)
     */
    public static function getCount(): int
    {
        $items = self::getItems();
        $count = 0;
        foreach ($items as $item) {
            $count += (int) $item['quantity'];
        }
        return $count;
    }

    /**
     * Calculate cart totals: subtotal, estimated tax (0 for demo or standard), total
     */
    public static function getTotals(): array
    {
        $items = self::getItems();
        $subtotal = 0.0;

        foreach ($items as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
        }

        $shipping = ($subtotal > 0 && $subtotal < 999.0) ? 99.00 : 0.00; // Free express delivery across India on orders over ₹999
        $total = $subtotal + $shipping;

        return [
            'subtotal'       => round($subtotal, 2),
            'shipping'       => round($shipping, 2),
            'total'          => round($total, 2),
            'item_count'     => count($items),
            'total_quantity' => self::getCount()
        ];
    }

    /**
     * Add item to cart with stock validation
     */
    public static function addItem(int $productId, int $quantity = 1): array
    {
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Quantity must be at least 1.'];
        }

        $product = Product::find($productId);
        if (!$product || empty($product['is_active'])) {
            return ['success' => false, 'message' => 'Product is not available.'];
        }

        if (Auth::check()) {
            return self::addUserCartItem((int) Auth::id(), $product, $quantity);
        } else {
            return self::addGuestCartItem($product, $quantity);
        }
    }

    /**
     * Update item quantity in cart
     */
    public static function updateQuantity(int $productId, int $quantity): array
    {
        if ($quantity <= 0) {
            return self::removeItem($productId);
        }

        $product = Product::find($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        if ($quantity > $product['stock']) {
            return [
                'success' => false, 
                'message' => "Only {$product['stock']} units available in stock.",
                'available_stock' => $product['stock']
            ];
        }

        if (Auth::check()) {
            self::db()->update(
                self::$table,
                ['quantity' => $quantity],
                'user_id = :uid AND product_id = :pid',
                ['uid' => Auth::id(), 'pid' => $productId]
            );
        } else {
            Session::start();
            $guestCart = Session::get(self::GUEST_CART_SESSION_KEY, []);
            if (isset($guestCart[$productId])) {
                $guestCart[$productId]['quantity'] = $quantity;
                Session::set(self::GUEST_CART_SESSION_KEY, $guestCart);
            }
        }

        return ['success' => true, 'message' => 'Cart updated.'];
    }

    /**
     * Remove item from cart
     */
    public static function removeItem(int $productId): array
    {
        if (Auth::check()) {
            self::db()->delete(
                self::$table,
                'user_id = :uid AND product_id = :pid',
                ['uid' => Auth::id(), 'pid' => $productId]
            );
        } else {
            Session::start();
            $guestCart = Session::get(self::GUEST_CART_SESSION_KEY, []);
            unset($guestCart[$productId]);
            Session::set(self::GUEST_CART_SESSION_KEY, $guestCart);
        }

        return ['success' => true, 'message' => 'Item removed from cart.'];
    }

    /**
     * Clear all cart items
     */
    public static function clear(): void
    {
        if (Auth::check()) {
            self::db()->delete(self::$table, 'user_id = :uid', ['uid' => Auth::id()]);
        } else {
            Session::start();
            Session::remove(self::GUEST_CART_SESSION_KEY);
        }
    }

    /**
     * Merge guest session cart into database cart when logging in
     */
    public static function mergeGuestCart(int $userId): void
    {
        Session::start();
        $guestCart = Session::get(self::GUEST_CART_SESSION_KEY, []);
        if (empty($guestCart)) {
            return;
        }

        foreach ($guestCart as $productId => $item) {
            $product = Product::find((int) $productId);
            if (!$product || $product['stock'] <= 0) {
                continue;
            }

            $desiredQty = (int) $item['quantity'];

            // Check if user already has this product in DB cart
            $existing = self::db()->fetchOne(
                "SELECT id, quantity FROM " . self::$table . " WHERE user_id = :uid AND product_id = :pid",
                ['uid' => $userId, 'pid' => $productId]
            );

            if ($existing) {
                $newQty = min($product['stock'], ((int) $existing['quantity']) + $desiredQty);
                self::db()->update(
                    self::$table,
                    ['quantity' => $newQty],
                    'id = :id',
                    ['id' => $existing['id']]
                );
            } else {
                $insertQty = min($product['stock'], $desiredQty);
                self::db()->insert(self::$table, [
                    'user_id'    => $userId,
                    'product_id' => $productId,
                    'quantity'   => $insertQty
                ]);
            }
        }

        // Empty guest cart once transferred
        Session::remove(self::GUEST_CART_SESSION_KEY);
    }

    // ------------------------------------------------------------------------
    // Private Helpers
    // ------------------------------------------------------------------------

    private static function getUserCartItems(int $userId): array
    {
        $sql = "SELECT c.id as cart_item_id, c.quantity, p.id as product_id, p.name,
                       p.slug, p.price, p.stock, p.image, cat.name as category_name
                FROM cart_items c
                JOIN products p ON c.product_id = p.id
                JOIN categories cat ON p.category_id = cat.id
                WHERE c.user_id = :uid AND p.is_active = 1
                ORDER BY c.id ASC";
        return self::db()->fetchAll($sql, ['uid' => $userId]);
    }

    private static function getGuestCartItems(): array
    {
        Session::start();
        $guestCart = Session::get(self::GUEST_CART_SESSION_KEY, []);
        if (empty($guestCart)) {
            return [];
        }

        $items = [];
        foreach ($guestCart as $productId => $item) {
            $product = Product::find((int) $productId);
            if ($product && !empty($product['is_active'])) {
                $items[] = [
                    'cart_item_id'  => 'guest_' . $product['id'],
                    'product_id'    => $product['id'],
                    'name'          => $product['name'],
                    'slug'          => $product['slug'],
                    'price'         => (float) $product['price'],
                    'stock'         => (int) $product['stock'],
                    'image'         => $product['image'],
                    'quantity'      => (int) $item['quantity'],
                    'category_name' => Category::find((int) $product['category_id'])['name'] ?? 'Gadget'
                ];
            }
        }
        return $items;
    }

    private static function addUserCartItem(int $userId, array $product, int $quantity): array
    {
        $productId = (int) $product['id'];
        $existing = self::db()->fetchOne(
            "SELECT id, quantity FROM " . self::$table . " WHERE user_id = :uid AND product_id = :pid",
            ['uid' => $userId, 'pid' => $productId]
        );

        $currentQty = $existing ? (int) $existing['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product['stock']) {
            return [
                'success' => false,
                'message' => "Cannot add {$quantity} more. Stock limit of {$product['stock']} reached."
            ];
        }

        if ($existing) {
            self::db()->update(
                self::$table,
                ['quantity' => $newQty],
                'id = :id',
                ['id' => $existing['id']]
            );
        } else {
            self::db()->insert(self::$table, [
                'user_id'    => $userId,
                'product_id' => $productId,
                'quantity'   => $newQty
            ]);
        }

        return ['success' => true, 'message' => "{$product['name']} added to cart!"];
    }

    private static function addGuestCartItem(array $product, int $quantity): array
    {
        Session::start();
        $guestCart = Session::get(self::GUEST_CART_SESSION_KEY, []);
        $productId = (int) $product['id'];

        $currentQty = isset($guestCart[$productId]) ? (int) $guestCart[$productId]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product['stock']) {
            return [
                'success' => false,
                'message' => "Cannot add {$quantity} more. Stock limit of {$product['stock']} reached."
            ];
        }

        $guestCart[$productId] = [
            'quantity' => $newQty
        ];

        Session::set(self::GUEST_CART_SESSION_KEY, $guestCart);
        return ['success' => true, 'message' => "{$product['name']} added to cart!"];
    }
}

