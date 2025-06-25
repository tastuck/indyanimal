<?php

require __DIR__ . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__ . '/config')->load();

$email = 'abc123@gmail.com';
$emailSalt = $_ENV['EMAIL_SALT'] ?? '';

// Hash the email
$emailHash = hash('sha256', $emailSalt . strtolower(trim($email)));

echo "Email Hash: $emailHash\n";

?>