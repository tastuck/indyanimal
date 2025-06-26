<?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: signup.php
 * Description:
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up – Indyanimal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>



<form method="POST" action="/signup" id="signup-form" class="form-container">
    <h2>Create an Account</h2>

    <input type="email" name="email" placeholder="Email address" required autofocus>
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="invite" placeholder="Invite Code" required>

    <button type="submit">Sign Up</button>
    <div class="spinner-container" style="display: none;">
        <div class="spinner"></div>
    </div>


    <?php if (isset($_GET['error'])): ?>
        <p class="error">
            <?php
            switch ($_GET['error']) {
                case 'missing': echo 'All fields are required.'; break;
                case 'invite': echo 'Invite code is invalid or already used.'; break;
                case 'internal': echo 'Something went wrong. Please try again.'; break;
                default: echo 'Signup failed.';
            }
            ?>
        </p>
    <?php endif; ?>
</form>

<?php include 'footer.php'; ?>

<script src="/js/signup.js"></script>

</body>
</html>
