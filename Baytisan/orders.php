<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login_required.php");
  exit;
}

require 'database.php';

// Fetch orders of logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT o.*, 
        (SELECT GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ', ')
         FROM order_items oi JOIN products p ON oi.product_id = p.id 
         WHERE oi.order_id = o.id) AS items
        FROM orders o 
        WHERE o.user_id = ?
        ORDER BY o.placed_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Your Orders</title>
<link rel="stylesheet" href="style.css">
<style>
.orders-container {
  max-width: 900px;
  margin: 40px auto;
  background: #fff;
  padding: 25px;
  border-radius: 14px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.05);
}
.order-card {
  border-bottom: 1px solid #eee;
  padding: 15px 0;
}
.order-card:last-child {
  border-bottom: none;
}
.order-items {
  font-size: 0.9rem;
  color: var(--muted);
}
.status {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 8px;
  font-size: 0.8rem;
  color: #fff;
  background: var(--primary);
}
.empty-orders {
  text-align: center;
  padding: 40px 20px;
  color: var(--muted);
}
.empty-orders a {
  display: inline-block;
  margin-top: 15px;
  background: var(--primary);
  color: white;
  padding: 8px 16px;
  border-radius: 10px;
  text-decoration: none;
}
.empty-orders a:hover {
  background: #6d9765;
}
</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="orders-container">
  <h2>Your Orders</h2>

  <?php if (!$orders): ?>
    <div class="empty-orders">
      <p>You haven’t placed any orders yet.</p>
      <a href="products.php">Go Shop Now</a>
    </div>
  <?php else: ?>
    <?php foreach ($orders as $order): ?>
      <div class="order-card">
        <p><strong>Order #<?= $order['id'] ?></strong> — 
           <span class="status"><?= ucfirst($order['status']) ?></span></p>
        <p class="order-items"><?= htmlspecialchars($order['items']) ?></p>
        <p><strong>Total:</strong> ₱<?= number_format($order['total_amount'], 2) ?></p>
        <small>Placed on <?= date("F j, Y g:i A", strtotime($order['placed_at'])) ?></small>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>
