<?php
require 'database.php';

$email = 'admin@baytisan.local';
$password = 'admin123'; // try your intended admin password here

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found";
    exit;
}

echo "Hash: " . $user['password_hash'] . "<br>";

if (password_verify($password, $user['password_hash'])) {
    echo "Password is CORRECT! Role: " . $user['role'];
} else {
    echo "Password is WRONG!";
}