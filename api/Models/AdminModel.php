<?php
/**
 * Author: Taniya Tucker
 * Date: 6/24/25
 * File: Admin.php
 * Description:
 */


namespace api\Models;

use PDO;

class AdminModel
{
    // create a reusable PDO connection
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

    // create a single-use invite code, max 80 per month per user
    public static function createInvite(int $userId): ?string
    {
        $pdo = self::getPDO();

        // check how many invites the user already made this month
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM Invites
            WHERE created_by_user_id = ?
              AND MONTH(created_at) = MONTH(CURRENT_DATE)
              AND YEAR(created_at) = YEAR(CURRENT_DATE)
        ");
        $stmt->execute([$userId]);

        if ((int)$stmt->fetchColumn() >= 80) {
            return null;
        }

        // generate a hash-based code using the salt
        $salt = $_ENV['EMAIL_SALT'] ?? '';
        $code = substr(hash('sha256', $salt . $userId . time()), 0, 16);

        // insert the invite into the database
        $stmt = $pdo->prepare("INSERT INTO Invites (invite_code, created_by_user_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$code, $userId]);

        return $code;
    }

    // create a new event
    public static function createEvent(string $title, string $date, int $adminId, int $priceCents): void
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("
            INSERT INTO Events (title, event_date, admin_user_id, price_cents, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$title, $date, $adminId, $priceCents]);
    }

    // list all events for display or selection
    public static function listEvents(): array
    {
        $pdo = self::getPDO();
        $stmt = $pdo->query("SELECT event_id, title, event_date, price_cents FROM Events ORDER BY event_date DESC");
        return $stmt->fetchAll();
    }


    // update an event’s info and price
    public static function updateEvent(int $id, string $title, string $date, int $priceCents): void
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("UPDATE Events SET title = ?, event_date = ?, price_cents = ? WHERE event_id = ?");
        $stmt->execute([$title, $date, $priceCents, $id]);
    }

    // create a new stage for a given event
    public static function createStage(int $eventId, string $name, ?string $description): void
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("INSERT INTO Stages (event_id, name, description) VALUES (?, ?, ?)");
        $stmt->execute([$eventId, $name, $description]);
    }

    // count how many events an admin has created
    public static function countActiveEventsForAdmin(int $adminId): int
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Events WHERE admin_user_id = ?");
        $stmt->execute([$adminId]);
        return (int)$stmt->fetchColumn();
    }

}



