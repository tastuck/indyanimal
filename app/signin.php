<?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: signin.php
 * Description:
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In - Indyanimal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<form method="POST" action="/signin" class="form-container" id="signin-form">
    <h2>Sign In to Indyanimal</h2>

    <input
            type="email"
            name="email"
            placeholder="Email address"
            required
            autofocus
    >

    <input
            type="password"
            name="password"
            placeholder="Password"
            required
    >

    <button type="submit">Sign In</button>

    <?php if (isset($_SESSION['signin_error'])): ?>
        <p class="error"><?php echo $_SESSION['signin_error']; ?></p>
        <?php unset($_SESSION['signin_error']); ?>
    <?php endif; ?>

</form>

<p class="form-note" style="text-align: center;">
    Don't have an account? <a href="/signup">Sign up with your invite code</a>
</p>

<?php include 'footer.php'; ?>

<script src="/js/signin.js"></script>

</body>
</html>



