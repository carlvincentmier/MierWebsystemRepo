<?php
session_start();
require 'database.php';
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { die('Login required.'); }

$cart = $_SESSION['cart'] ?? [];
if (!$cart) { die('Cart empty.'); }

$addr = trim($_POST['shipping_address'] ?? '');
if (!$addr) { die('Address required.'); }

$pdo->beginTransaction();
try {
  $total = 0;
  $sel = $pdo->prepare("SELECT * FROM products WHERE id = ?");
  foreach ($cart as $pid => $qty) {
    $sel->execute([$pid]);
    $p = $sel->fetch();
    if (!$p) throw new Exception("Product not found");
    $total += $p['price'] * $qty;
  }
  $ins = $pdo->prepare("INSERT INTO orders (user_id,total_amount,shipping_address,status) VALUES (?,?,?,?)");
  $ins->execute([$user_id,$total,$addr,'pending']);
  $order_id = $pdo->lastInsertId();
  $ins2 = $pdo->prepare("INSERT INTO order_items (order_id,product_id,unit_price,quantity,subtotal) VALUES (?,?,?,?,?)");
  foreach ($cart as $pid => $qty) {
    $sel->execute([$pid]); $p = $sel->fetch();
    $sub = $p['price'] * $qty;
    $ins2->execute([$order_id,$pid,$p['price'],$qty,$sub]);
    $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$qty,$pid]);
  }
  $pdo->prepare("INSERT INTO order_tracking (order_id,status,note) VALUES (?,?,?)")->execute([$order_id,'pending','Order placed']);
  $pdo->commit();
  unset($_SESSION['cart']);
  header('Location: order_summary.php?order_id=' . $order_id);
  exit;
} catch (Exception $e) {
  $pdo->rollBack();
  die('Error: ' . $e->getMessage());
}
