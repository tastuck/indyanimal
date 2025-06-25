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

    public static function createInvite(int $userId): ?string
    {
        $pdo = self::getPDO();

        // max 80 invites per month per user
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

        // use the EMAIL_SALT already loaded for hashing
        $salt = $_ENV['EMAIL_SALT'] ?? '';
        $code = substr(hash('sha256', $salt . $userId . time()), 0, 16);

        $stmt = $pdo->prepare("INSERT INTO Invites (invite_code, created_by_user_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$code, $userId]);

        return $code;
    }

    public static function createEvent(string $title, string $date, int $adminId, int $priceCents): void
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("
        INSERT INTO Events (title, event_date, admin_user_id, price_cents, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
        $stmt->execute([$title, $date, $adminId, $priceCents]);
    }


    public static function listEvents(): array
    {
        $pdo = self::getPDO();
        $stmt = $pdo->query("SELECT event_id, title, event_date FROM Events ORDER BY event_date DESC");
        return $stmt->fetchAll();
    }

    public static function updateEvent(int $id, string $title, string $date, int $priceCents): void
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("UPDATE Events SET title = ?, event_date = ?, price_cents = ? WHERE event_id = ?");
        $stmt->execute([$title, $date, $priceCents, $id]);
    }


    public static function createStage(int $eventId, string $name, ?string $description): void
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("INSERT INTO Stages (event_id, name, description) VALUES (?, ?, ?)");
        $stmt->execute([$eventId, $name, $description]);
    }
}


//more methods than user so get pdo is used