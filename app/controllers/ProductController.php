<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Models\Category;

/**
 * Class ProductController
 * 
 * Manages product catalog grid, search queries, multi-facet filtering,
 * sorting, pagination, and product detail displays.
 */
class ProductController extends Controller
{
    /**
     * Display Catalog with filters, search, sorting, and pagination
     */
    public function index(Request $request): void
    {
        $q = trim((string) $request->get('q', ''));
        $category = trim((string) $request->get('category', ''));
        $sort = trim((string) $request->get('sort', 'newest'));
        $inStock = (int) $request->get('in_stock', 0);
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $brand = trim((string) $request->get('brand', ''));
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 9;

        $filters = [
            'q'         => $q,
            'category'  => $category,
            'sort'      => $sort,
            'in_stock'  => $inStock,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'brand'     => $brand
        ];

        $catalog = Product::getCatalog($filters, $page, $perPage);
        $categories = Category::allWithCount();

        $currentCategory = null;
        if (!empty($category)) {
            $currentCategory = Category::findBySlug($category);
        }

        $this->view('products/index', [
            'title'           => $currentCategory ? "{$currentCategory['name']} | Catalog" : 'Browse Catalog',
            'products'        => $catalog['items'],
            'totalItems'      => $catalog['total_items'],
            'totalPages'      => $catalog['total_pages'],
            'currentPage'     => $catalog['current_page'],
            'categories'      => $categories,
            'currentCategory' => $currentCategory,
            'filters'         => $filters
        ]);
    }

    /**
     * Display single product detail view
     */
    public function show(Request $request, string $slug): void
    {
        $product = Product::findWithCategory($slug);

        if (!$product || empty($product['is_active'])) {
            Response::status(404);
            $this->view('errors/404', ['title' => 'Product Not Found']);
            return;
        }

        // Fetch up to 4 related products in same category
        $relatedSql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                       FROM products p
                       JOIN categories c ON p.category_id = c.id
                       WHERE p.category_id = :cat_id AND p.id != :pid AND p.is_active = 1
                       LIMIT 4";
        $relatedProducts = Product::db()->fetchAll($relatedSql, [
            'cat_id' => $product['category_id'],
            'pid'    => $product['id']
        ]);

        $this->view('products/show', [
            'title'           => $product['name'],
            'product'         => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
}

