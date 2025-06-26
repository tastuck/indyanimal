<?php
/**
 * Author: Taniya Tucker
 * Date: 6/25/25
 * File: mission.php
 * Description: Indyanimal mission page with conditional headers and guest links
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user']);
$role = $isLoggedIn ? $_SESSION['user']['role'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Indyanimal – Mission</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
if ($isLoggedIn) {
    if ($role === 'admin') {
        include __DIR__ . '/admin_header.php';
    } else {
        include __DIR__ . '/header.php';
    }
}
?>

<main class="mission-container">
    <h1>What Indyanimal Is</h1>

    <p>
        Indyanimal is a space for capturing what happened — not who was there. It’s built for local events, shared
        memory, and creative trust, without asking for names or tracking anything personal.
    </p>

    <p>
        Media gets uploaded, reviewed, and shown when it’s ready. Moments can be archived privately or shared with the
        public. Event pages are simple, ticketing is built in, and everything stays focused on the experience — not the
        algorithm.
    </p>

    <p>
        We use AI tools to flag suspicious content early, but nothing is posted without human eyes. Accounts work
        through invite codes only, and emails are stored as secure hashes. There’s no recovery, no ad IDs, and no feeds
        designed to keep you scrolling.
    </p>

    <p>
        This isn’t a platform for performance. It’s a way to remember and share real things with the people who were
        part of them.
    </p>

    <?php if (!$isLoggedIn): ?>
        <hr>
        <div class="guest-auth-links">
            <a href="/signup">Create an account</a> or <a href="/signin">Sign in</a>
        </div>
    <?php endif; ?>
</main>

<script src="/js/mission.js"></script>
</body>
</html>

