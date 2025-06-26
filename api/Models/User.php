<?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: User.php
 * Description:
 */

namespace api\Models;

use PDO;

class User
{
    // fetch user by email (hashed with salt)
    public static function getByEmail(string $email): ?array
    {
        $pdo = new PDO(
            'mysql:host=127.0.0.1;dbname=indyanimal;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        $salt = $_ENV['EMAIL_SALT'] ?? '';
        $emailHash = hash('sha256', $salt . strtolower(trim($email)));

        $stmt = $pdo->prepare("SELECT user_id, password_hash, role FROM Users WHERE email_hash = ?");
        $stmt->execute([$emailHash]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    // create a new user account using a valid invite code
    public static function createWithInvite(string $email, string $passwordHash, string $inviteCode): ?int
    {
        $pdo = new PDO(
            'mysql:host=127.0.0.1;dbname=indyanimal;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        $salt = $_ENV['EMAIL_SALT'] ?? '';
        $emailHash = hash('sha256', $salt . strtolower(trim($email)));

        file_put_contents(__DIR__ . '/../../log.txt',
            "TRYING SIGNUP\nEmail: $email\nHash: $emailHash\nInvite: [$inviteCode]\n",
            FILE_APPEND
        );

        $stmt = $pdo->prepare("SELECT invite_code, used_by_user_id FROM Invites WHERE invite_code = ?");
        $stmt->execute([$inviteCode]);
        $invite = $stmt->fetch();

        if (!$invite) {
            file_put_contents(__DIR__ . '/../../log.txt', "NO MATCH FOUND FOR INVITE\n", FILE_APPEND);
        } elseif ($invite['used_by_user_id']) {
            file_put_contents(__DIR__ . '/../../log.txt', "INVITE ALREADY USED\n", FILE_APPEND);
        }

        if (!$invite || $invite['used_by_user_id']) {
            return null;
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO Users (email_hash, password_hash, role, created_at, last_login)
                                   VALUES (?, ?, 'user', NOW(), NOW())");
            $stmt->execute([$emailHash, $passwordHash]);
            $userId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("UPDATE Invites SET used_by_user_id = ?, used_at = NOW() WHERE invite_code = ?");
            $stmt->execute([$userId, $inviteCode]);

            $pdo->commit();
            return (int)$userId;

        } catch (\Exception $e) {
            $pdo->rollBack();
            file_put_contents(__DIR__ . '/../../log.txt', "EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND);
            return null;
        }
    }
}
