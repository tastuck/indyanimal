<?php
/**
 * Author: Taniya Tucker
 * Date: 6/23/25
 * File: AuthController.php
 * Description: Handles authentication for sign in, sign up, and logout.
 */

namespace api\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use api\Models\User;
use api\Authentication\SessionManager;

class AuthController
{
    // shows the signin form
    public function showSignin(Request $request, Response $response): Response
    {
        ob_start();
        include __DIR__ . '/../../app/signin.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    // handles signin form submission
    public function handleSignin(Request $request, Response $response): Response
    {
        ob_start();

        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = User::getByEmail($email);

        // if user not found or password is wrong
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['signin_error'] = 'Wrong email or password';
            return $response->withHeader('Location', '/signin')->withStatus(302);
        }

        // start session and store user info
        SessionManager::start($user['user_id']);

        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'role' => $user['role']
        ];

        ob_end_clean();

        // redirect to dashboard or admin
        return $response->withHeader('Location', $user['role'] === 'admin' ? '/admin' : '/dashboard')->withStatus(302);
    }

    // shows the signup form
    public function showSignup(Request $request, Response $response): Response
    {
        ob_start();
        include __DIR__ . '/../../app/signup.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    // handles signup form submission
    public function handleSignup(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $email = trim(strtolower($data['email'] ?? ''));
        $password = trim($data['password'] ?? '');
        $inviteCode = trim($data['invite'] ?? '');

        // check for missing fields
        if (!$email || !$password || !$inviteCode) {
            return $response->withHeader('Location', '/signup?error=missing')->withStatus(302);
        }

        // hash the password using python script
        $passwordHash = self::hashPasswordWithPython($password);
        if (!$passwordHash) {
            return $response->withHeader('Location', '/signup?error=internal')->withStatus(302);
        }

        // create user with invite code
        $userId = User::createWithInvite($email, $passwordHash, $inviteCode);
        if (!$userId) {
            error_log("Signup failed for $email with invite $inviteCode");
            return $response->withHeader('Location', '/signup?error=invite')->withStatus(302);
        }

        // start session and set user info
        session_start();
        SessionManager::start($userId);

        $_SESSION['user'] = [
            'user_id' => $userId,
            'role' => 'user'
        ];

        // redirect to dashboard
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    // hashes password using python script
    private static function hashPasswordWithPython(string $password): ?string
    {
        $scriptPath = __DIR__ . '/../../scripts/hash_password.py';
        $cmd = escapeshellcmd("python3 " . escapeshellarg($scriptPath) . " " . escapeshellarg($password));
        $output = shell_exec($cmd);

        file_put_contents(__DIR__ . '/../../log.txt',
            "HASH CMD: $cmd\nOUTPUT: $output\n\n",
            FILE_APPEND
        );

        return $output ? trim($output) : null;
    }

    // verifies password using python script
    private static function verifyPasswordWithPython(string $password, string $storedHash): bool
    {
        $passwordArg = escapeshellarg($password);
        $hashArg = escapeshellarg($storedHash);
        $python = '/Library/Frameworks/Python.framework/Versions/3.13/bin/python3';

        $scriptPath = __DIR__ . '/../../scripts/verify_password.py';
        $cmd = "$python " . escapeshellarg($scriptPath) . " $passwordArg $hashArg 2>&1";

        $output = shell_exec($cmd);

        file_put_contents(__DIR__ . '/../../log.txt',
            "VERIFY CMD: $cmd\n" .
            "PASSWORD: [$password]\n" .
            "HASH: [$storedHash]\n" .
            "OUTPUT: [$output]\n\n",
            FILE_APPEND
        );

        return trim($output) === 'true';
    }

    // logs the user out
    public function logout(Request $request, Response $response): Response
    {
        SessionManager::logout();
        return $response->withHeader('Location', '/signin')->withStatus(302);
    }
}
