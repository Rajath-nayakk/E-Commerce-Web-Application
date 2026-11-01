<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class Category
 * 
 * Manages product taxonomy, categories, slugs, and catalog grouping.
 */
class Category extends Model
{
    protected static string $table = 'categories';

    public static function findBySlug(string $slug): ?array
    {
        return static::firstWhere('slug', $slug);
    }

    public static function allWithCount(): array
    {
        $sql = "SELECT c.id, c.name, c.slug, COUNT(p.id) as product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
                GROUP BY c.id, c.name, c.slug
                ORDER BY c.name ASC";
        return static::db()->fetchAll($sql);
    }

    public static function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        if ($slug === '') {
            $slug = 'category';
        }

        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM categories WHERE slug = :slug";
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
}

