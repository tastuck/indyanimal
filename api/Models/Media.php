<?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: Media.php
 * Description:
 */

namespace api\Models;

use PDO;

class Media
{
    // get PDO connection (same pattern as AdminModel)
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

    // insert new media record and return its ID
    public static function insertMedia(array $data): int
    {
        $pdo = self::getPDO();

        $stmt = $pdo->prepare("
            INSERT INTO Media 
            (user_id, event_id, filepath, media_type, media_category, approved, is_flagged_ai, uploaded_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $data['user_id'],
            $data['event_id'],
            $data['filepath'],
            $data['media_type'],
            $data['media_category'],
            $data['approved'] ?? 0,
            $data['is_flagged_ai'] ?? 0
        ]);

        return (int)$pdo->lastInsertId();
    }

    // update AI flag column for a media item
    public static function updateAIFlag(int $mediaId, int $flag): void
    {
        $pdo = self::getPDO();

        $stmt = $pdo->prepare("UPDATE Media SET is_flagged_ai = ? WHERE media_id = ?");
        $stmt->execute([$flag, $mediaId]);
    }

    // get a single media item by ID
    public static function getById(int $mediaId): ?array
    {
        $pdo = self::getPDO();

        $stmt = $pdo->prepare("SELECT * FROM Media WHERE media_id = ?");
        $stmt->execute([$mediaId]);

        $media = $stmt->fetch();
        return $media ?: null;
    }

    // return all pending media with attached event details
    public static function getPendingMediaWithDetails(): array
    {
        $pdo = self::getPDO();

        $stmt = $pdo->query("
            SELECT 
                m.media_id, m.filepath, m.media_type, m.media_category, m.uploaded_at,
                m.user_id, e.title AS event_title, e.event_date
            FROM Media m
            JOIN Events e ON m.event_id = e.event_id
            WHERE m.approved = 0
            ORDER BY m.uploaded_at DESC
        ");

        return $stmt->fetchAll();
    }

    // approve a media item (set approved = 1)
    public static function approveMedia(int $mediaId): void
    {
        $pdo = self::getPDO();

        $stmt = $pdo->prepare("UPDATE Media SET approved = 1 WHERE media_id = ?");
        $stmt->execute([$mediaId]);
    }

    // reject a media item (delete from DB)
    public static function rejectMedia(int $mediaId): void
    {
        $pdo = self::getPDO();

        $stmt = $pdo->prepare("DELETE FROM Media WHERE media_id = ?");
        $stmt->execute([$mediaId]);
    }

    // return all approved media (used for public gallery)
    public static function getApprovedMedia(): array
    {
        $pdo = self::getPDO();
        $stmt = $pdo->query("SELECT * FROM Media WHERE approved = 1 ORDER BY uploaded_at DESC");
        return $stmt->fetchAll();
    }

    // associate a tag with a media item
    public static function addTagToMedia(int $mediaId, int $tagId): bool
    {
        $pdo = self::getPDO();

        $stmt = $pdo->prepare("INSERT IGNORE INTO Media_Tags (media_id, tag_id) VALUES (?, ?)");
        return $stmt->execute([$mediaId, $tagId]);
    }

    // search across media with filters
    public static function search(?string $eventTitle, ?string $eventDate, array $tagIds, bool $includeArchived): array
    {
        $pdo = self::getPDO();

        $query = "
            SELECT m.*, e.title AS event_title, e.event_date, e.is_archived
            FROM Media m
            JOIN Events e ON m.event_id = e.event_id
            LEFT JOIN Media_Tags mt ON m.media_id = mt.media_id
            WHERE m.approved = 1
        ";

        $params = [];

        if ($eventTitle) {
            $query .= " AND e.title LIKE ?";
            $params[] = '%' . $eventTitle . '%';
        }

        if ($eventDate) {
            $query .= " AND DATE(e.event_date) = ?";
            $params[] = $eventDate;
        }

        if (!$includeArchived) {
            $query .= " AND e.is_archived = 0";
        }

        if (!empty($tagIds)) {
            $placeholders = implode(',', array_fill(0, count($tagIds), '?'));
            $query .= " AND m.media_id IN (
                SELECT media_id FROM Media_Tags WHERE tag_id IN ($placeholders)
            )";
            $params = array_merge($params, $tagIds);
        }

        $query .= " GROUP BY m.media_id ORDER BY m.uploaded_at DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
