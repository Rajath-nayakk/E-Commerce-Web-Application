<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class Product
 * 
 * Manages product inventory, catalog filtering, search, sorting,
 * stock transactions, and category relations.
 */
class Product extends Model
{
    protected static string $table = 'products';

    /**
     * Get single product with category details by slug or ID
     */
    public static function findWithCategory(int|string $identifier): ?array
    {
        $column = is_numeric($identifier) ? 'p.id' : 'p.slug';
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE {$column} = :id
                LIMIT 1";
        return static::db()->fetchOne($sql, ['id' => $identifier]);
    }

    /**
     * Filter, search, sort, and paginate active catalog products
     */
    public static function getCatalog(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $where = ["p.is_active = 1"];
        $params = [];

        // Category filter
        if (!empty($filters['category'])) {
            if (is_numeric($filters['category'])) {
                $where[] = "p.category_id = :cat_id";
                $params['cat_id'] = (int) $filters['category'];
            } else {
                $where[] = "c.slug = :cat_slug";
                $params['cat_slug'] = $filters['category'];
            }
        }

        // Keyword Search
        if (!empty($filters['q'])) {
            $keyword = '%' . trim($filters['q']) . '%';
            $where[] = "(p.name LIKE :kw1 OR p.description LIKE :kw2)";
            $params['kw1'] = $keyword;
            $params['kw2'] = $keyword;
        }

        // In-stock filter
        if (!empty($filters['in_stock'])) {
            $where[] = "p.stock > 0";
        }

        // Price range filter
        if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
            $where[] = "p.price >= :min_price";
            $params['min_price'] = (float) $filters['min_price'];
        }
        if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
            $where[] = "p.price <= :max_price";
            $params['max_price'] = (float) $filters['max_price'];
        }

        // Deal filter
        if (!empty($filters['deal'])) {
            $where[] = "p.is_deal = 1";
        }

        // Brand filter
        if (!empty($filters['brand'])) {
            $where[] = "p.brand = :brand";
            $params['brand'] = $filters['brand'];
        }

        // Sorting
        $sortOrder = match ($filters['sort'] ?? 'newest') {
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name_asc'   => 'p.name ASC',
            'name_desc'  => 'p.name DESC',
            'stock_desc' => 'p.stock DESC',
            default      => 'p.id DESC',
        };

        $whereClause = implode(' AND ', $where);

        // Count total matching records for pagination
        $countSql = "SELECT COUNT(*) as total 
                     FROM products p 
                     JOIN categories c ON p.category_id = c.id 
                     WHERE {$whereClause}";
        $countRow = static::db()->fetchOne($countSql, $params);
        $totalItems = (int) ($countRow['total'] ?? 0);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        // Fetch paginated slice
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE {$whereClause}
                ORDER BY {$sortOrder}
                LIMIT {$perPage} OFFSET {$offset}";

        $items = static::db()->fetchAll($sql, $params);

        return [
            'items'       => $items,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'current_page'=> $page,
            'per_page'    => $perPage
        ];
    }

    /**
     * Get featured products for homepage hero showcase
     */
    public static function getFeatured(int $limit = 8): array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1
                ORDER BY p.is_featured DESC, p.id DESC
                LIMIT {$limit}";
        return static::db()->fetchAll($sql);
    }

    /**
     * Get top discounted deals of the day
     */
    public static function getDeals(int $limit = 6): array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND p.is_deal = 1
                ORDER BY p.rating DESC, p.id DESC
                LIMIT {$limit}";
        return static::db()->fetchAll($sql);
    }

    /**
     * Get related products within the same category
     */
    public static function getRelated(int $categoryId, int $excludeId, int $limit = 4): array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND p.category_id = :cat_id AND p.id != :exc_id
                ORDER BY p.rating DESC, p.id DESC
                LIMIT {$limit}";
        return static::db()->fetchAll($sql, ['cat_id' => $categoryId, 'exc_id' => $excludeId]);
    }

    /**
     * Deduct stock within a transaction
     */
    public static function deductStock(int $productId, int $quantity): bool
    {
        $sql = "UPDATE products 
                SET stock = stock - :qty 
                WHERE id = :id AND stock >= :qty_check";
        $stmt = static::db()->query($sql, [
            'qty'       => $quantity,
            'id'        => $productId,
            'qty_check' => $quantity
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Restore stock upon order cancellation
     */
    public static function restoreStock(int $productId, int $quantity): bool
    {
        $sql = "UPDATE products SET stock = stock + :qty WHERE id = :id";
        $stmt = static::db()->query($sql, [
            'qty' => $quantity,
            'id'  => $productId
        ]);
        return $stmt->rowCount() > 0;
    }

    public static function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        if ($slug === '') {
            $slug = 'product';
        }

        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM products WHERE slug = :slug";
            $params = ['slug' => $slug];
            if ($ignoreId !== null) {
                $sql .= " AND id != :id";
                $params['id'] = $ignoreId;
            }

            $exists = static::db()->fetchOne($sql, $params);
            if (!$exists) {
                break;
            }

            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public static function getLowStockAlerts(int $threshold = 5): array
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.stock <= :threshold AND p.is_active = 1
                ORDER BY p.stock ASC";
        return static::db()->fetchAll($sql, ['threshold' => $threshold]);
    }
}

