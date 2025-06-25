<?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: header.php
 * Description:
 */
?>

<?php
// deny access unless user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: /signin');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Indyanimal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header>
    <h1>Welcome to Indyanimal</h1>
    <nav>
        <a href="/dashboard">Home</a> |
        <a href="/media/upload">Upload Media</a> |
<!--        <a href="/media/search">Search Media</a> |-->
        <a href="/events">Browse Events</a>      |
        <a href="/signout">Logout</a>
    </nav>
    <hr>
</header>
