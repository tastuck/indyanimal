<?php
/**
 * Author: Taniya Tucker
 * Date: 6/24/25
 * File: admin_header.php
 * Description:
 */
?>

<?php
// deny access unless user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /signin');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Indyanimal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header>
    <h1>Admin Dashboard</h1>
    <nav>
        <a href="/admin">Home</a> |
        <a href="/admin/events">Events</a> |
        <a href="/admin/media">Media</a> |
        <a href="/admin/invite">Invites</a>|
        <a href="/signout">Logout</a>
    </nav>
    <hr>
</header>
