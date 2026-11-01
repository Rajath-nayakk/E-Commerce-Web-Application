<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;

/**
 * Class AdminController
 * 
 * Handles all administrator workflows:
 * - Metrics dashboard
 * - Product catalog CRUD with secure image upload validation
 * - Category management
 * - Order oversight and status transition dispatching
 * - User management and spend metrics
 */
class AdminController extends Controller
{
    /**
     * Dashboard Overview
     */
    public function dashboard(Request $request): void
    {
        $metrics = Order::getDashboardMetrics();

        $this->view('admin/dashboard', [
            'title'   => 'Dashboard Overview',
            'metrics' => $metrics
        ], 'layouts/admin');
    }

    // ========================================================================
    // Product Management
    // ========================================================================

    public function products(Request $request): void
    {
        $q = trim((string) $request->get('q', ''));
        $categoryId = (int) $request->get('category_id', 0);
        $page = max(1, (int) $request->get('page', 1));

        $filters = ['q' => $q, 'sort' => 'newest'];
        if ($categoryId > 0) {
            $filters['category'] = $categoryId;
        }

        $catalog = Product::getCatalog($filters, $page, 15);
        $categories = Category::all('name ASC');

        $this->view('admin/products/index', [
            'title'      => 'Manage Products',
            'products'   => $catalog['items'],
            'totalItems' => $catalog['total_items'],
            'totalPages' => $catalog['total_pages'],
            'page'       => $catalog['current_page'],
            'categories' => $categories,
            'filters'    => $filters
        ], 'layouts/admin');
    }

    public function createProduct(Request $request): void
    {
        $categories = Category::all('name ASC');

        $this->view('admin/products/create', [
            'title'      => 'Add New Product',
            'categories' => $categories
        ], 'layouts/admin');
    }

    public function storeProduct(Request $request): void
    {
        $this->validateCsrf($request);

        $name = trim((string) $request->post('name', ''));
        $categoryId = (int) $request->post('category_id', 0);
        $price = (float) $request->post('price', 0);
        $stock = (int) $request->post('stock', 0);
        $description = trim((string) $request->post('description', ''));
        $isActive = (int) $request->post('is_active', 1);

        $this->validate([
            'name'        => $name,
            'category_id' => $categoryId,
            'price'       => $price,
            'stock'       => $stock,
            'description' => $description
        ], [
            'name'        => 'required|min:2|max:150',
            'category_id' => 'required|integer',
            'price'       => 'required|numeric|min:0.01',
            'stock'       => 'required|integer|min:0',
            'description' => 'required|min:5'
        ], $request, '/admin/products/create');

        // Handle Image Upload Validation
        $imageFilename = 'placeholder.svg';
        $uploadedFile = $request->file('image');

        if ($uploadedFile && !empty($uploadedFile['name']) && $uploadedFile['error'] === UPLOAD_ERR_OK) {
            $imageFilename = $this->handleImageUpload($uploadedFile, '/admin/products/create');
        }

        $slug = Product::generateSlug($name);

        Product::create([
            'category_id' => $categoryId,
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'price'       => $price,
            'stock'       => $stock,
            'image'       => $imageFilename,
            'is_active'   => $isActive,
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        Session::flash('success', "Product '{$name}' created successfully!");
        $this->redirect('/admin/products');
    }

    public function editProduct(Request $request, string $id): void
    {
        $productId = (int) $id;
        $product = Product::find($productId);

        if (!$product) {
            Session::flash('danger', 'Product not found.');
            $this->redirect('/admin/products');
        }

        $categories = Category::all('name ASC');

        $this->view('admin/products/edit', [
            'title'      => "Edit Product: {$product['name']}",
            'product'    => $product,
            'categories' => $categories
        ], 'layouts/admin');
    }

    public function updateProduct(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $productId = (int) $id;

        $product = Product::find($productId);
        if (!$product) {
            Session::flash('danger', 'Product not found.');
            $this->redirect('/admin/products');
        }

        $name = trim((string) $request->post('name', ''));
        $categoryId = (int) $request->post('category_id', 0);
        $price = (float) $request->post('price', 0);
        $stock = (int) $request->post('stock', 0);
        $description = trim((string) $request->post('description', ''));
        $isActive = (int) $request->post('is_active', 1);

        $this->validate([
            'name'        => $name,
            'category_id' => $categoryId,
            'price'       => $price,
            'stock'       => $stock,
            'description' => $description
        ], [
            'name'        => 'required|min:2|max:150',
            'category_id' => 'required|integer',
            'price'       => 'required|numeric|min:0.01',
            'stock'       => 'required|integer|min:0',
            'description' => 'required|min:5'
        ], $request, "/admin/products/{$productId}/edit");

        $data = [
            'category_id' => $categoryId,
            'name'        => $name,
            'description' => $description,
            'price'       => $price,
            'stock'       => $stock,
            'is_active'   => $isActive,
        ];

        // Check if name changed to update slug
        if ($name !== $product['name']) {
            $data['slug'] = Product::generateSlug($name, $productId);
        }

        // Handle Image Upload if new file provided
        $uploadedFile = $request->file('image');
        if ($uploadedFile && !empty($uploadedFile['name']) && $uploadedFile['error'] === UPLOAD_ERR_OK) {
            $data['image'] = $this->handleImageUpload($uploadedFile, "/admin/products/{$productId}/edit");
        }

        Product::update($productId, $data);

        Session::flash('success', "Product '{$name}' updated successfully!");
        $this->redirect('/admin/products');
    }

    public function deleteProduct(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $productId = (int) $id;

        $product = Product::find($productId);
        if (!$product) {
            Session::flash('danger', 'Product not found.');
            $this->redirect('/admin/products');
        }

        // Check if product is in order_items
        $isInOrders = Product::db()->fetchOne(
            "SELECT id FROM order_items WHERE product_id = :id LIMIT 1",
            ['id' => $productId]
        );

        if ($isInOrders) {
            // Cannot hard delete due to historical foreign key integrity, so deactivate
            Product::update($productId, ['is_active' => 0]);
            Session::flash('info', "Product '{$product['name']}' has previous order history, so it was deactivated rather than deleted.");
        } else {
            Product::delete($productId);
            Session::flash('success', "Product '{$product['name']}' removed permanently.");
        }

        $this->redirect('/admin/products');
    }

    // ========================================================================
    // Category Management
    // ========================================================================

    public function categories(Request $request): void
    {
        $categories = Category::allWithCount();

        $this->view('admin/categories/index', [
            'title'      => 'Manage Categories',
            'categories' => $categories
        ], 'layouts/admin');
    }

    public function storeCategory(Request $request): void
    {
        $this->validateCsrf($request);

        $name = trim((string) $request->post('name', ''));
        $this->validate(['name' => $name], ['name' => 'required|min:2|max:100'], $request, '/admin/categories');

        $slug = Category::generateSlug($name);

        Category::create([
            'name' => $name,
            'slug' => $slug
        ]);

        Session::flash('success', "Category '{$name}' created successfully!");
        $this->redirect('/admin/categories');
    }

    public function updateCategory(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $categoryId = (int) $id;

        $name = trim((string) $request->post('name', ''));
        $this->validate(['name' => $name], ['name' => 'required|min:2|max:100'], $request, '/admin/categories');

        $slug = Category::generateSlug($name, $categoryId);

        Category::update($categoryId, [
            'name' => $name,
            'slug' => $slug
        ]);

        Session::flash('success', "Category updated to '{$name}'.");
        $this->redirect('/admin/categories');
    }

    public function deleteCategory(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $categoryId = (int) $id;

        // Check if any products exist in this category
        $count = Product::count('category_id = :cid', ['cid' => $categoryId]);
        if ($count > 0) {
            Session::flash('danger', "Cannot delete category: {$count} products are currently assigned to it. Reassign or remove them first.");
            $this->redirect('/admin/categories');
        }

        Category::delete($categoryId);
        Session::flash('success', 'Category deleted.');
        $this->redirect('/admin/categories');
    }

    // ========================================================================
    // Order Management
    // ========================================================================

    public function orders(Request $request): void
    {
        $status = trim((string) $request->get('status', ''));
        $q = trim((string) $request->get('q', ''));

        $where = ['1=1'];
        $params = [];

        if (!empty($status)) {
            $where[] = 'o.status = :status';
            $params['status'] = $status;
        }

        if (!empty($q)) {
            $where[] = '(o.id = :qid OR o.shipping_name LIKE :qname OR u.email LIKE :qmail)';
            $params['qid'] = is_numeric($q) ? (int) $q : 0;
            $params['qname'] = "%{$q}%";
            $params['qmail'] = "%{$q}%";
        }

        $whereSql = implode(' AND ', $where);
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email,
                       COUNT(oi.id) as item_count
                FROM orders o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE {$whereSql}
                GROUP BY o.id
                ORDER BY o.created_at DESC";

        $orders = Order::db()->fetchAll($sql, $params);

        $this->view('admin/orders/index', [
            'title'          => 'Manage Customer Orders',
            'orders'         => $orders,
            'statusFilter'   => $status,
            'searchQuery'    => $q,
            'statusOptions'  => Order::getStatuses()
        ], 'layouts/admin');
    }

    public function showOrder(Request $request, string $id): void
    {
        $orderId = (int) $id;
        $order = Order::findWithDetails($orderId);

        if (!$order) {
            Session::flash('danger', 'Order not found.');
            $this->redirect('/admin/orders');
        }

        $this->view('admin/orders/show', [
            'title'         => "Order Details #{$orderId}",
            'order'         => $order,
            'statusOptions' => Order::getStatuses()
        ], 'layouts/admin');
    }

    public function updateOrderStatus(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $orderId = (int) $id;

        $newStatus = trim((string) $request->post('status', ''));
        $note = trim((string) $request->post('note', ''));

        $validStatuses = array_keys(Order::getStatuses());
        if (!in_array($newStatus, $validStatuses, true)) {
            Session::flash('danger', 'Invalid order status selected.');
            $this->redirect("/admin/orders/{$orderId}");
        }

        Order::updateStatus($orderId, $newStatus, $note ?: null);

        Session::flash('success', "Order #{$orderId} status updated to " . ucfirst($newStatus) . "!");
        $this->redirect("/admin/orders/{$orderId}");
    }

    // ========================================================================
    // User Management
    // ========================================================================

    public function users(Request $request): void
    {
        $users = User::getAllWithOrderStats();

        $this->view('admin/users/index', [
            'title' => 'Registered Users & Customers',
            'users' => $users
        ], 'layouts/admin');
    }

    // ========================================================================
    // Secure Image Upload Handler
    // ========================================================================

    private function handleImageUpload(array $file, string $redirectBackUrl): string
    {
        $config = require dirname(__DIR__) . '/config/config.php';
        $maxBytes = $config['upload_max'] ?? 5242880; // 5MB
        $allowedMimes = $config['allowed_types'] ?? ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];

        // Size check
        if ($file['size'] > $maxBytes) {
            Session::flash('danger', 'Image size exceeds maximum allowed limit (5MB).');
            $this->redirect($redirectBackUrl);
        }

        // Validate MIME type with finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes, true)) {
            Session::flash('danger', 'Invalid image format. Allowed formats: JPG, PNG, WEBP, SVG.');
            $this->redirect($redirectBackUrl);
        }

        // Determine safe extension
        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            default => 'jpg'
        };

        // Randomized cryptographically secure filename
        $filename = 'product_' . bin2hex(random_bytes(12)) . '.' . $ext;
        $destinationDir = $config['paths']['uploads'] . DIRECTORY_SEPARATOR . 'products';

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }

        $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            Session::flash('danger', 'Failed to save uploaded image file.');
            $this->redirect($redirectBackUrl);
        }

        return $filename;
    }
}

