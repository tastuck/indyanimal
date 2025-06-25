<?php

namespace api\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


use api\Models\User;
use api\Authentication\SessionManager;

class AuthController
{

    // Show signin page
    public function showSignin(Request $request, Response $response): Response
    {
        ob_start();
        include __DIR__ . '/../../app/signin.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    // Handle login
    public function handleSignin(Request $request, Response $response): Response
    {
        session_start();
        ob_start();

        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = \api\Models\User::getByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $response->getBody()->write('Wrong email or password');
            return $response->withStatus(401);
        }

        // create a session in the db
        \api\Authentication\SessionManager::start($user['user_id']);

        file_put_contents(__DIR__ . '/../../log.txt', "ROLE FROM DB: " . $user['role'] . "\n", FILE_APPEND);

        // store user info in session
        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'role' => $user['role']
        ];

        // clear any accidental output before redirect
        ob_end_clean();

        file_put_contents(__DIR__ . '/../../log.txt', print_r($_SESSION, true), FILE_APPEND);

        // redirect based on role
        if ($user['role'] === 'admin') {
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }

        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    // Show signup page
    public function showSignup(Request $request, Response $response): Response
    {
        ob_start();
        include __DIR__ . '/../../app/signup.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    // Handle signup
    public function handleSignup(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $email = trim(strtolower($data['email'] ?? ''));
        $password = trim($data['password'] ?? '');
        $inviteCode = trim($data['invite'] ?? '');

        if (!$email || !$password || !$inviteCode) {
            return $response->withHeader('Location', '/signup?error=missing')->withStatus(302);
        }

        //  Ask Python to hash the password
        $passwordHash = self::hashPasswordWithPython($password);
        if (!$passwordHash) {
            return $response->withHeader('Location', '/signup?error=internal')->withStatus(302);
        }

        $userId = User::createWithInvite($email, $passwordHash, $inviteCode);
        if (!$userId) {
            error_log("Signup failed for $email with invite $inviteCode");
            return $response->withHeader('Location', '/signup?error=invite')->withStatus(302);

        }

        SessionManager::start($userId);
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    // Python hash function
    private static function hashPasswordWithPython(string $password): ?string
    {
        $scriptPath = __DIR__ . '/../../scripts/hash_password.py';
        $cmd = escapeshellcmd("python3 " . escapeshellarg($scriptPath) . " " . escapeshellarg($password));
        $output = shell_exec($cmd);

        // TEMP: write debugging output
        file_put_contents(__DIR__ . '/../../log.txt',
            "HASH CMD: $cmd\nOUTPUT: $output\n\n",
            FILE_APPEND
        );

        return $output ? trim($output) : null;
    }




    // Python verify function
    private static function verifyPasswordWithPython(string $password, string $storedHash): bool
    {
        $passwordArg = escapeshellarg($password);
        $hashArg = escapeshellarg($storedHash);
        $python = '/Library/Frameworks/Python.framework/Versions/3.13/bin/python3'; // full path to Python

        // test
        $scriptPath = __DIR__ . '/../../scripts/verify_password.py';
        $cmd = "$python " . escapeshellarg($scriptPath) . " $passwordArg $hashArg 2>&1";

        //$cmd = "$python verify_password.py $passwordArg $hashArg 2>&1";
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

    //  logout
    public function logout(Request $request, Response $response): Response
    {
        SessionManager::logout();
        return $response->withHeader('Location', '/signin')->withStatus(302);
    }
}
