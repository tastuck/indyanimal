<?php
/**
 * Author: Taniya Tucker
 * Date: 6/24/25
 * File: Event.php
 * Description:
 */




namespace api\Models;

use PDO;

class Event
{
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

    public static function getById(int $eventId): ?array
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM Events WHERE event_id = ?");
        $stmt->execute([$eventId]);
        $event = $stmt->fetch();
        return $event ?: null;
    }

    public static function isBuyable(array $event): bool
    {
        return !$event['is_cancelled'] && !$event['is_postponed'] && !$event['is_archived'];
    }

    public static function getUpcomingEvents(): array
    {
        $pdo = new \PDO(
            'mysql:host=127.0.0.1;dbname=indyanimal;charset=utf8mb4',
            'root', '',
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );

        $stmt = $pdo->query("SELECT event_id, title, event_date FROM Events WHERE event_date >= CURDATE() ORDER BY event_date ASC");
        return $stmt->fetchAll();
    }

    public static function searchEvents(?string $title = null, ?string $date = null): array
    {
        $pdo = self::getPDO();

        $query = "SELECT * FROM Events WHERE 1";
        $params = [];

        if ($title) {
            $query .= " AND title LIKE ?";
            $params[] = '%' . $title . '%';
        }

        if ($date) {
            $query .= " AND DATE(event_date) = ?";
            $params[] = $date;
        }

        $query .= " ORDER BY event_date DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }


}
