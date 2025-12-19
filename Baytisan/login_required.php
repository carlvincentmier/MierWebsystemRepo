<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Login Required</title>
<link rel="stylesheet" href="style.css">
<style>
body {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: var(--bg);
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
.notice-icon {
  font-size: 60px;
  color: var(--primary);
  margin-bottom: 15px;
}
.notice-card h1 {
  font-size: 1.6rem;
  margin-bottom: 10px;
}
.notice-card p {
  color: var(--muted);
  margin-bottom: 25px;
  font-size: 1rem;
}
.btn-primary {
  background: var(--primary);
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
.pot-shape {
  width: 80px;
  height: 80px;
  margin: 0 auto 20px;
  background: radial-gradient(circle at 50% 20%, #d9a066 30%, #c18040 70%);
  border-radius: 50% 50% 45% 45% / 60% 60% 40% 40%;
  box-shadow: inset 0 0 10px rgba(0,0,0,0.2);
}
</style>
</head>
<body>
  <div class="notice-card">
    <div class="pot-shape"></div>
    <h1>Login Required</h1>
    <p>You must be signed in to view this page and place an order.</p>
    <a href="index.php" class="btn-primary">Go to Login</a>
  </div>
</body>
</html>