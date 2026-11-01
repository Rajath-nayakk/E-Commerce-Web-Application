<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Class OrderStatusHistory
 * 
 * Tracks chronological status changes and notes for order lifecycle auditing.
 */
class OrderStatusHistory extends Model
{
    protected static string $table = 'order_status_history';

    public static function log(int $orderId, string $status, ?string $note = null): int
    {
        return static::create([
            'order_id'   => $orderId,
            'status'     => $status,
            'note'       => $note,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public static function getForOrder(int $orderId): array
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE order_id = :oid ORDER BY created_at ASC, id ASC";
        return static::db()->fetchAll($sql, ['oid' => $orderId]);
    }
}

