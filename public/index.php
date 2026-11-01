<?php
declare(strict_types=1);

/**
 * ============================================================================
 * NovaTech Gear - E-Commerce Web Application Front Controller
 * ============================================================================
 * Clean PHP 8.x Architecture without external frameworks.
 * Pure PDO, prepared statements, CSRF protection, and RESTful routing.
 */

// 1. Load Autoloader & Core Config
require_once dirname(__DIR__) . '/app/core/Autoloader.php';
require_once dirname(__DIR__) . '/app/core/helpers.php';
require_once dirname(__DIR__) . '/app/config/config.php';

use App\Core\Session;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\OrderController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\ApiController;

// 2. Start Secure Session
Session::start();

// 3. Instantiate Request and Router
$request = new Request();
$router = new Router();

// ============================================================================
// Public Catalog & Storefront Routes
// ============================================================================
$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{slug}', [ProductController::class, 'show']);

// ============================================================================
// Shopping Cart Routes (Guest + User)
// ============================================================================
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/clear', [CartController::class, 'clear']);

// ============================================================================
// Checkout & Order Tracking Routes (Requires Auth)
// ============================================================================
$router->get('/checkout', [CheckoutController::class, 'index'], ['auth']);
$router->post('/checkout/process', [CheckoutController::class, 'process'], ['auth']);
$router->get('/orders/confirmation/{id}', [CheckoutController::class, 'confirmation'], ['auth']);
$router->get('/orders', [OrderController::class, 'index'], ['auth']);
$router->get('/orders/{id}', [OrderController::class, 'show'], ['auth']);

// ============================================================================
// Authentication & Password Recovery Routes
// ============================================================================
$router->get('/login', [AuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [AuthController::class, 'login'], ['guest']);
$router->post('/logout', [AuthController::class, 'logout'], ['auth']);

$router->get('/register', [AuthController::class, 'showRegister'], ['guest']);
$router->post('/register', [AuthController::class, 'register'], ['guest']);

$router->get('/forgot-password', [AuthController::class, 'showForgotPassword'], ['guest']);
$router->post('/forgot-password', [AuthController::class, 'sendResetLink'], ['guest']);
$router->get('/reset-password', [AuthController::class, 'showResetPassword'], ['guest']);
$router->post('/reset-password', [AuthController::class, 'resetPassword'], ['guest']);

// ============================================================================
// Admin Control Panel Routes (Requires 'admin' role middleware)
// ============================================================================
$router->group(['middleware' => ['admin']], function (Router $r) {
    // Dashboard overview
    $r->get('/admin', [AdminController::class, 'dashboard']);
    
    // Product management (CRUD)
    $r->get('/admin/products', [AdminController::class, 'products']);
    $r->get('/admin/products/create', [AdminController::class, 'createProduct']);
    $r->post('/admin/products', [AdminController::class, 'storeProduct']);
    $r->get('/admin/products/{id}/edit', [AdminController::class, 'editProduct']);
    $r->post('/admin/products/{id}', [AdminController::class, 'updateProduct']);
    $r->post('/admin/products/{id}/delete', [AdminController::class, 'deleteProduct']);

    // Category management (CRUD)
    $r->get('/admin/categories', [AdminController::class, 'categories']);
    $r->post('/admin/categories', [AdminController::class, 'storeCategory']);
    $r->post('/admin/categories/{id}', [AdminController::class, 'updateCategory']);
    $r->post('/admin/categories/{id}/delete', [AdminController::class, 'deleteCategory']);

    // Order management & status transitions
    $r->get('/admin/orders', [AdminController::class, 'orders']);
    $r->get('/admin/orders/{id}', [AdminController::class, 'showOrder']);
    $r->post('/admin/orders/{id}/status', [AdminController::class, 'updateOrderStatus']);

    // User management
    $r->get('/admin/users', [AdminController::class, 'users']);
});

// ============================================================================
// Backend JSON APIs under /api
// ============================================================================
$router->get('/api/csrf-token', [ApiController::class, 'csrfToken']);
$router->get('/api/products', [ApiController::class, 'products']);
$router->get('/api/products/{id}', [ApiController::class, 'productDetail']);

$router->get('/api/cart', [ApiController::class, 'cart']);
$router->post('/api/cart/add', [ApiController::class, 'cartAdd']);
$router->post('/api/cart/update', [ApiController::class, 'cartUpdate']);
$router->post('/api/cart/remove', [ApiController::class, 'cartRemove']);

$router->get('/api/orders', [ApiController::class, 'orders'], ['auth']);
$router->get('/api/orders/{id}', [ApiController::class, 'orderDetail'], ['auth']);

$router->post('/api/auth/login', [ApiController::class, 'login']);
$router->post('/api/auth/register', [ApiController::class, 'register']);

// 4. Dispatch Request
try {
    $router->dispatch($request);
} catch (\Throwable $e) {
    if ($request->isJson() || str_starts_with($request->uri(), '/api')) {
        Response::error($e->getMessage(), 500);
    } else {
        Response::status(500);
        \App\Core\View::render('errors/500', [
            'title'   => 'Server Error',
            'message' => $e->getMessage(),
            'trace'   => (require dirname(__DIR__) . '/app/config/config.php')['app_debug'] ? $e->getTraceAsString() : ''
        ]);
    }
}
