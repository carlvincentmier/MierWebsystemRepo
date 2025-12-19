<?php
session_start();
require 'database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: admin_required.php");
  exit;
}
$user_id = $_SESSION['user_id'];
$msg = '';
$success = false;

// Fetch admin info
$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first = trim($_POST['first_name'] ?? '');
  $last = trim($_POST['last_name'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $changePw = !empty($_POST['new_password']);
  $newPw = $_POST['new_password'] ?? '';
  $confirmPw = $_POST['confirm_password'] ?? '';

  if (!$first || !$email) {
    $msg = "First name and email are required.";
  } elseif ($changePw && $newPw !== $confirmPw) {
    $msg = "Passwords do not match.";
  } else {
    // Check email duplicate (exclude self)
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check->execute([$email, $user_id]);
    if ($check->fetch()) {
      $msg = "Email is already taken by another user.";
    } else {
      $params = [$first, $last, $email, $user_id];
      $sql = "UPDATE users SET first_name=?, last_name=?, email=? WHERE id=?";
      if ($changePw && $newPw) {
        $hash = password_hash($newPw, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET first_name=?, last_name=?, email=?, password_hash=? WHERE id=?";
        $params = [$first, $last, $email, $hash, $user_id];
      }
      $upd = $pdo->prepare($sql);
      if ($upd->execute($params)) {
        $_SESSION['first_name'] = $first;
        $msg = "Profile updated successfully.";
        $success = true;
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
      } else {
        $msg = "Failed to update profile.";
      }
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Profile — Baytisan</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { background: #fdf7f4; }
    .profile-container {
      max-width: 430px;
      margin: 44px auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(100,100,100,0.08);
      padding: 36px 28px 28px 28px;
    }
    h2 { margin-bottom: 18px; text-align: center;}
    .form-group { margin-bottom:15px; }
    label { font-weight:600; margin-bottom:5px; display:block;}
    input[type="text"],input[type="email"],input[type="password"] { width:100%; padding:10px 13px; border-radius:8px; border:1px solid #ccc; font-size:1.05em;}
    .btn-main { width:100%; }
    .msg { text-align:center; margin:10px 0; color: #e05d5d;}
    .success { color:#4caf50;}
    .profile-icon { text-align:center; font-size:3em; color: var(--primary);}
    .pw-toggle { font-size:.96em; color:var(--primary); cursor:pointer; }
    .pw-fields { display:none; margin-top:10px; }
    .pw-fields.active { display:block; }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded',function(){
      var toggle = document.getElementById('togglePw');
      var fields = document.getElementById('pwFields');
      if(toggle && fields) {
        toggle.addEventListener('click',function(){
          fields.classList.toggle('active');
        });
      }
    });
  </script>
</head>
<body>
<header class="site-header">
  <div class="container nav">
    <a href="index.php" class="brand"><img src="images/logo.png" class="logo">Baytisan</a>
    <nav class="main-nav">
      <a href="index.php">Home</a>
      <a href="products.php">Shop</a>
      <a href="order_history.php">Orders</a>
      <a href="admin_profile.php" class="active">Admin Profile</a>
      <a href="admin_dashboard.php">Admin Dashboard</a>
    </nav>
    <div class="nav-actions">
      <span class="welcome"><i class="fa fa-user-shield"></i> <?= htmlspecialchars($_SESSION['first_name']) ?></span>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>
</header>
<main>
  <div class="profile-container">
    <div class="profile-icon"><i class="fa fa-user-shield"></i></div>
    <h2>Admin Profile</h2>
    <?php if ($msg): ?>
      <div class="msg<?= $success ? ' success' : '' ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
      <div class="form-group">
        <label for="first_name">First Name</label>
        <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
      </div>
      <div class="form-group">
        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['last_name']) ?>">
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>
      <div class="form-group">
        <span id="togglePw" class="pw-toggle"><i class="fa fa-key"></i> Change password?</span>
        <div class="pw-fields" id="pwFields">
          <label for="new_password">New Password</label>
          <input type="password" name="new_password" id="new_password">
          <label for="confirm_password">Confirm New Password</label>
          <input type="password" name="confirm_password" id="confirm_password">
        </div>
      </div>
      <button type="submit" class="btn btn-main"><i class="fa fa-save"></i> Save Changes</button>
    </form>
  </div>
</main>
</body>
</html>