<?php
/**
 * Author: Taniya Tucker
 * Date: 6/24/25
 * File: Order.php
 * Description:
 */

namespace api\Models;

use PDO;

class Order
{
    // get PDO connection (same pattern as other models)
    private static function getPDO(): PDO
    {
        return new PDO(
            'mysql:host=127.0.0.1;dbname=indyanimal;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    // create a new order and return its ID
    public static function create(array $data): int
    {
        $pdo = self::getPDO();

        $stmt = $pdo->prepare("
            INSERT INTO Orders 
            (user_id, event_id, admin_user_id, amount, platform_fee, provider_order_id, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $data['user_id'],
            $data['event_id'],
            $data['admin_user_id'],
            $data['amount'],
            $data['platform_fee'],
            $data['provider_order_id'],
            $data['status']
        ]);

        return (int)$pdo->lastInsertId();
    }

    // get all orders, most recent first
    public static function getAll(): array
    {
        $pdo = self::getPDO();
        $stmt = $pdo->query("SELECT * FROM Orders ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    // get all orders for a specific user
    public static function getByUser(int $userId): array
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM Orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // mark an order complete using its provider ID
    public static function markAsComplete(string $providerOrderId): void
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("UPDATE Orders SET status = 'complete', updated_at = NOW() WHERE provider_order_id = ?");
        $stmt->execute([$providerOrderId]);
    }
}
