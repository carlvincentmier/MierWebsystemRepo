<?php
// order_tracking.php
session_start(); require 'database.php';
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: index.html'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $order_id=(int)($_POST['order_id']??0); $status=$_POST['status']??'processing'; $note=$_POST['note']??'';
  if ($order_id) {
    $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$status,$order_id]);
    $pdo->prepare("INSERT INTO order_tracking (order_id,status,note) VALUES (?,?,?)")->execute([$order_id,$status,$note]);
    header("Location: order_tracking.php?order_id=$order_id"); exit;
  }
}
$orders = $pdo->query("SELECT id,user_id,total_amount,status,placed_at FROM orders ORDER BY placed_at DESC")->fetchAll();
$order_id = (int)($_GET['order_id'] ?? 0);
$order = $order_id ? $pdo->prepare("SELECT * FROM orders WHERE id=?")->execute([$order_id]) : null;
?>
<!doctype html><html><head><meta charset="utf-8"><title>Order Tracking</title><link rel="stylesheet" href="style.css"></head><body>
<header class="site-header"><div class="container nav"><a href="index.html" class="brand"><img src="images/logo.png" class="logo">Baytisan</a></div></header>
<div class="container" style="padding:20px">
  <h2>Order Tracking</h2>
  <div style="display:flex;gap:20px">
    <div style="width:320px"><h3>Orders</h3><ul><?php foreach($orders as $o) echo "<li><a href='order_tracking.php?order_id={$o['id']}'>#{$o['id']}</a> - {$o['status']}</li>"; ?></ul></div>
    <div style="flex:1">
      <?php if ($order_id): 
        $o = $pdo->query("SELECT * FROM orders WHERE id = $order_id")->fetch();
        $tracking = $pdo->prepare("SELECT * FROM order_tracking WHERE order_id=? ORDER BY created_at DESC"); $tracking->execute([$order_id]); $tracking=$tracking->fetchAll();
      ?>
        <h3>Order #<?= $order_id ?> — <?= htmlspecialchars($o['status'] ?? '') ?></h3>
        <form method="POST"><input type="hidden" name="order_id" value="<?= $order_id ?>">
          <label>Status<select name="status"><option>pending</option><option>processing</option><option>shipped</option><option>delivered</option><option>cancelled</option></select></label>
          <label>Note<input name="note"></label>
          <button class="btn" type="submit">Update</button>
        </form>
        <h4>History</h4><ul><?php foreach($tracking as $t) echo "<li>{$t['created_at']} — {$t['status']} - ".htmlspecialchars($t['note'])."</li>"; ?></ul>
      <?php else: ?>
        <p>Select an order.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
</body></html>
