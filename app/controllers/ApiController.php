<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;

/**
 * Class ApiController
 * 
 * Provides clean RESTful JSON endpoints under /api with proper HTTP status codes
 * (200, 201, 400, 401, 403, 404, 422, 429, 500).
 */
class ApiController extends Controller
{
    /**
     * GET /api/products
     * Query params: q, category, sort, in_stock, page, per_page
     */
    public function products(Request $request): void
    {
        $filters = [
            'q'        => trim((string) $request->get('q', '')),
            'category' => trim((string) $request->get('category', '')),
            'sort'     => trim((string) $request->get('sort', 'newest')),
            'in_stock' => (int) $request->get('in_stock', 0)
        ];

        $page = max(1, (int) $request->get('page', 1));
        $perPage = max(1, min(50, (int) $request->get('per_page', 12)));

        $catalog = Product::getCatalog($filters, $page, $perPage);

        Response::json([
            'success'    => true,
            'data'       => $catalog['items'],
            'pagination' => [
                'current_page' => $catalog['current_page'],
                'total_pages'  => $catalog['total_pages'],
                'total_items'  => $catalog['total_items'],
                'per_page'     => $catalog['per_page']
            ]
        ], 200);
    }

    /**
     * GET /api/products/{id}
     */
    public function productDetail(Request $request, string $id): void
    {
        $product = Product::findWithCategory($id);

        if (!$product || empty($product['is_active'])) {
            Response::error('Product not found or currently inactive.', 404);
            return;
        }

        Response::success('Product retrieved.', $product, 200);
    }

    /**
     * GET /api/cart
     */
    public function cart(Request $request): void
    {
        $items = Cart::getItems();
        $totals = Cart::getTotals();

        Response::json([
            'success' => true,
            'data'    => [
                'items'  => $items,
                'totals' => $totals
            ]
        ], 200);
    }

    /**
     * POST /api/cart/add
     */
    public function cartAdd(Request $request): void
    {
        $productId = (int) $request->post('product_id', 0);
        $quantity = max(1, (int) $request->post('quantity', 1));

        if ($productId <= 0) {
            Response::error('product_id is required.', 422);
            return;
        }

        $result = Cart::addItem($productId, $quantity);
        $totals = Cart::getTotals();

        Response::json([
            'success'    => $result['success'],
            'message'    => $result['message'],
            'cart_count' => $totals['total_quantity'],
            'totals'     => $totals
        ], $result['success'] ? 200 : 400);
    }

    /**
     * POST /api/cart/update
     */
    public function cartUpdate(Request $request): void
    {
        $productId = (int) $request->post('product_id', 0);
        $quantity = (int) $request->post('quantity', 1);

        if ($productId <= 0) {
            Response::error('product_id is required.', 422);
            return;
        }

        $result = Cart::updateQuantity($productId, $quantity);
        $totals = Cart::getTotals();

        Response::json([
            'success'    => $result['success'],
            'message'    => $result['message'],
            'cart_count' => $totals['total_quantity'],
            'totals'     => $totals
        ], $result['success'] ? 200 : 400);
    }

    /**
     * POST /api/cart/remove
     */
    public function cartRemove(Request $request): void
    {
        $productId = (int) $request->post('product_id', 0);
        if ($productId <= 0) {
            Response::error('product_id is required.', 422);
            return;
        }

        $result = Cart::removeItem($productId);
        $totals = Cart::getTotals();

        Response::json([
            'success'    => true,
            'message'    => $result['message'],
            'cart_count' => $totals['total_quantity'],
            'totals'     => $totals
        ], 200);
    }

    /**
     * GET /api/orders
     */
    public function orders(Request $request): void
    {
        if (!Auth::check()) {
            Response::error('Authentication required.', 401);
            return;
        }

        $orders = Order::getUserOrders((int) Auth::id());
        Response::success('Orders retrieved.', $orders, 200);
    }

    /**
     * GET /api/orders/{id}
     */
    public function orderDetail(Request $request, string $id): void
    {
        if (!Auth::check()) {
            Response::error('Authentication required.', 401);
            return;
        }

        $orderId = (int) $id;
        $userId = Auth::isAdmin() ? null : (int) Auth::id();
        $order = Order::findWithDetails($orderId, $userId);

        if (!$order) {
            Response::error('Order not found or permission denied.', 404);
            return;
        }

        Response::success('Order details and tracking timeline retrieved.', $order, 200);
    }

    /**
     * POST /api/auth/login
     */
    public function login(Request $request): void
    {
        $email = strtolower(trim((string) $request->post('email', '')));
        $password = (string) $request->post('password', '');

        if (Auth::isRateLimited($email)) {
            $remaining = ceil(Auth::getLockoutRemaining($email) / 60);
            Response::error("Account temporarily locked due to too many failed attempts. Try again in {$remaining} minutes.", 429);
            return;
        }

        if (empty($email) || empty($password)) {
            Response::error('Email and password are required.', 422);
            return;
        }

        $user = User::findByEmail($email);
        if (!$user || !User::verifyPassword($password, $user['password_hash'])) {
            $attempts = Auth::incrementLoginAttempts($email);
            Response::error('Invalid email or password.', 401, ['attempts' => $attempts]);
            return;
        }

        Auth::login($user);

        unset($user['password_hash']);
        Response::json([
            'success' => true,
            'message' => "Welcome, {$user['name']}!",
            'user'    => $user
        ], 200);
    }

    /**
     * POST /api/auth/register
     */
    public function register(Request $request): void
    {
        $name = trim((string) $request->post('name', ''));
        $email = strtolower(trim((string) $request->post('email', '')));
        $password = (string) $request->post('password', '');

        $errors = [];
        if (mb_strlen($name) < 2) {
            $errors['name'][] = 'Name must be at least 2 characters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Valid email is required.';
        } elseif (User::findByEmail($email)) {
            $errors['email'][] = 'Email is already registered.';
        }
        if (mb_strlen($password) < 6) {
            $errors['password'][] = 'Password must be at least 6 characters.';
        }

        if (!empty($errors)) {
            Response::error('Validation failed.', 422, $errors);
            return;
        }

        $userId = User::register($name, $email, $password, 'user');
        $user = User::find($userId);
        Auth::login($user);

        unset($user['password_hash']);
        Response::json([
            'success' => true,
            'message' => 'Account created successfully.',
            'user'    => $user
        ], 201);
    }
}

