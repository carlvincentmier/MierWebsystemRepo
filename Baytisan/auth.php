<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'signup') {
        $fname = trim($_POST['fname']);
        $lname = trim($_POST['lname']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$fname, $lname, $email, $password, 'customer']);
            $userId = $pdo->lastInsertId();
            // Set session variables in the same way as login.php
            $_SESSION['user_id'] = $userId;
            $_SESSION['first_name'] = $fname;
            $_SESSION['role'] = 'customer';
            header("Location: products.php");
            exit;
        } catch (PDOException $e) {
            echo "Error: Email already exists!";
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['role'] = $user['role'];
            header("Location: products.php");
            exit;
        } else {
            echo "Invalid email or password!";
        }
    }
}
?>