<?php
session_start();
require 'database.php';

$msg = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['first_name'] ?? '');
    $lname = trim($_POST['last_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$fname || !$email || !$password || !$confirm) {
        $msg = "Please fill in all fields.";
    } elseif ($password !== $confirm) {
        $msg = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $msg = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, 'admin')");
            $ins->execute([$fname, $lname, $email, $hash]);
            $msg = "Admin account created! You can now <a href='admin_login.php'>log in</a>.";
            $success = true;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Registration — Baytisan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #fdf7f4; }
        .register-container { max-width:420px; margin:60px auto; background:#fff; border-radius:16px; box-shadow:0 6px 28px rgba(0,0,0,0.08); padding:44px 32px; }
        h2 { margin-bottom:22px; text-align:center;}
        .form-group { margin-bottom:17px; }
        label { display:block; margin-bottom:8px; font-weight:600;}
        input[type="text"],input[type="email"],input[type="password"] { width:100%; padding:9px 13px; border-radius:8px; border:1px solid #ccc; font-size:1em;}
        .btn-primary { width:100%; }
        .msg { color: #e05d5d; margin: 13px 0 0 0; text-align: center;}
        .success { color: #4caf50; }
        .register-link { display:block; text-align:center; margin-top:20px; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register Admin</h2>
        <form method="post" autocomplete="off">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" id="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name">
            </div>
            <div class="form-group">
                <label for="email">Admin Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <?php if ($msg): ?>
                <div class="msg<?= $success ? ' success' : '' ?>"><?= $msg ?></div>
            <?php endif; ?>
        </form>
        <a href="admin_login.php" class="register-link">Back to Admin Login</a>
        <a href="index.php" class="register-link">Back to Home</a>
    </div>
</body>
</html>