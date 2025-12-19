<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Access Required</title>
<link rel="stylesheet" href="style.css">
<style>
body {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: var(--bg,#FDF7F4);
  margin: 0;
}
.notice-card {
  text-align: center;
  background: #fff;
  border-radius: 14px;
  padding: 40px 30px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.06);
  max-width: 420px;
}
.notice-card h1 {
  font-size: 1.4rem;
  margin-bottom: 10px;
  color: var(--dark,#685752);
}
.notice-card p {
  color: var(--muted,#7a7a7a);
  margin-bottom: 25px;
  font-size: 1rem;
}
.btn-primary {
  background: var(--primary,#8EB486);
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 1rem;
  text-decoration: none;
  display: inline-block;
}
.btn-primary:hover {
  background: #6d9765;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
.admin-lock {
  font-size: 60px;
  margin-bottom: 15px;
  color: #c18040;
}
</style>
</head>
<body>
  <div class="notice-card">
    <div class="admin-lock"><i class="fa fa-user-shield"></i></div>
    <h1>Admin Access Required</h1>
    <p>You must be signed in as an <strong>admin</strong> to view this page.</p>
    <a href="index.php" class="btn-primary"><i class="fa fa-sign-in-alt"></i> Go to Login</a>
  </div>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</body>
</html>