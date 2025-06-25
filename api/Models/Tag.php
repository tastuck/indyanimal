<?php
/**
 * Author: Taniya Tucker
 * Date: 6/24/25
 * File: Tag.php
 * Description:
 */


namespace api\Models;

use PDO;

class Tag
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

    public static function getValidTagIds(array $tagIds): array
    {
        if (empty($tagIds)) {
            return [];
        }

        $pdo = self::getPDO();

        $placeholders = implode(',', array_fill(0, count($tagIds), '?'));
        $stmt = $pdo->prepare("SELECT tag_id FROM Tags WHERE tag_id IN ($placeholders)");
        $stmt->execute($tagIds);

        $rows = $stmt->fetchAll();

        return array_column($rows, 'tag_id');
    }

    // Optional: fetch all tags ordered by type and name
    public static function getAllTags(): array
    {
        $pdo = self::getPDO();
        $stmt = $pdo->query("SELECT * FROM Tags ORDER BY tag_type, name");
        return $stmt->fetchAll();
    }
}
