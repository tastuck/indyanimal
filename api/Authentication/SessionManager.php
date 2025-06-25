<?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: SessionManager.php
 * Description:
 */

namespace api\Authentication;

class SessionManager
{
    // create a new session after logging in or signing up
    public static function start(int $userId): void
    {
        // connect to the database
        $pdo = new \PDO(
            'mysql:host=127.0.0.1;dbname=indyanimal;charset=utf8mb4',
            'root', '',
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );

        // create a random session token
        $token = bin2hex(random_bytes(32));

        // set the expiration for one week from now
        $expiresAt = date('Y-m-d H:i:s', time() + 604800);

        // insert the new session into the database
        $stmt = $pdo->prepare("INSERT INTO Sessions (session_token, user_id, expires_at, created_at)
                           VALUES (?, ?, ?, NOW())");
        $stmt->execute([$token, $userId, $expiresAt]);

        // send the session token to the browser as a cookie
        setcookie('session', $token, [
            'expires' => time() + 604800,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }


    // figure out who's logged in based on their session cookie
    public static function getUserId(): ?int
    {
        if (!isset($_COOKIE['session'])) return null;

        $token = $_COOKIE['session'];

        $pdo = new \PDO(
            'mysql:host=127.0.0.1;dbname=indyanimal;charset=utf8mb4',
            'root', '',
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );


        // look up the session token and make sure it hasn’t expired
        $stmt = $pdo->prepare("SELECT user_id FROM Sessions WHERE session_token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);

        $row = $stmt->fetch();
        return $row['user_id'] ?? null;
    }

    // log out by deleting the session from the database and the browser
    public static function logout(): void
    {
        if (isset($_COOKIE['session'])) {
            $pdo = new \PDO(
                'mysql:host=127.0.0.1;dbname=indyanimal;charset=utf8mb4',
                'root', '',
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                ]
            );

            $stmt = $pdo->prepare("DELETE FROM Sessions WHERE session_token = ?");
            $stmt->execute([$_COOKIE['session']]);

            // clear the cookie
            setcookie('session', '', time() - 3600, '/');
        }
    }
}
