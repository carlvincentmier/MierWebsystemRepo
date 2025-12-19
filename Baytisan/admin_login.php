<?php
session_start();
require 'database.php';

// If already logged in as admin, redirect to dashboard
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';

    if (!$email || !$pass) {
        $msg = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id, first_name, password_hash, role FROM users WHERE LOWER(email) = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user) {
            $msg = "Admin not found!";
        } elseif (!password_verify($pass, $user['password_hash'])) {
            $msg = "Invalid credentials!";
        } else {
            // success
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['role'] = $user['role'];
            header("Location: admin_dashboard.php");
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Login — Baytisan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #fdf7f4; }
        .login-container { max-width:380px; margin:60px auto; background:#fff; border-radius:16px; box-shadow:0 6px 28px rgba(0,0,0,0.08); padding:40px 32px; }
        h2 { margin-bottom:22px; text-align:center;}
        .form-group { margin-bottom:18px; }
        label { display:block; margin-bottom:7px; font-weight:600;}
        input[type="email"],input[type="password"] { width:100%; padding:9px 13px; border-radius:8px; border:1px solid #ccc; font-size:1em;}
        .btn-primary { width:100%; }
        .msg { color: #e05d5d; margin: 13px 0 0 0; text-align: center;}
        .register-link { display:block; text-align:center; margin-top:20px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="post" autocomplete="off">
            <div class="form-group">
                <label for="admin-email">Admin Email</label>
                <input type="email" name="email" id="admin-email" required>
            </div>
            <div class="form-group">
                <label for="admin-password">Password</label>
                <input type="password" name="password" id="admin-password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <?php if ($msg): ?>
                <div class="msg"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
        </form>
        <a href="admin_register.php" class="register-link">Register new admin</a>
        <a href="index.php" class="register-link">Back to Home</a>
    </div>
</body>
</html>