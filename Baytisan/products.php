<?php
session_start();
require_once 'database.php';

// Always get cart count from session for badge
$cart = $_SESSION['cart'] ?? [];
$cartCount = array_sum($cart);

$loggedIn = isset($_SESSION['user_id']);
$firstName = $_SESSION['first_name'] ?? '';
$role = $_SESSION['role'] ?? '';

// Get selected category filter (if any)
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$sql = "SELECT p.*, c.name AS category_name, l.name AS location_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        LEFT JOIN locations l ON p.origin_location_id = l.id";
if ($categoryFilter > 0) {
    $sql .= " WHERE p.category_id = ?";
}
$sql .= " ORDER BY p.id ASC";

$stmt = $pdo->prepare($sql);
$categoryFilter > 0 ? $stmt->execute([$categoryFilter]) : $stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Helper for image path (tries PNG with and without extension)
function product_image_path($filename) {
    if (!$filename) return "images/logo.png";
    if (strtolower(substr($filename, -4)) === '.png') {
        $path = "images/products/" . $filename;
        if (file_exists($path)) return $path;
    }
    $try = "images/products/" . $filename . ".png";
    if (file_exists($try)) return $try;
    return "images/logo.png";
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Shop — Baytisan</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <script defer src="script.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .category-filter .btn,
    .nav-actions .btn {
      background: #8EB486;
      color: #fff !important;
      border: none;
      border-radius: 10px;
      padding: 10px 18px;
      font-weight: 600;
      font-size: 1em;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      box-shadow: 0 6px 18px rgba(142,180,134,0.07);
      cursor: pointer;
      transition: background .18s, color .18s, box-shadow .18s, transform .16s;
    }
    .category-filter .btn:hover,
    .nav-actions .btn:hover,
    .category-filter .btn:focus,
    .nav-actions .btn:focus {
      background: #6a9c5b;
      color: #fff !important;
      box-shadow: 0 12px 26px rgba(104,180,130,0.13);
      transform: translateY(-2px) scale(1.04);
      text-decoration: none;
    }
    .category-filter .btn i,
    .nav-actions .btn i {
      font-size: 1.07em;
      margin-right: 5px;
      transition: transform .16s;
    }
    .category-filter .btn:hover i,
    .nav-actions .btn:hover i {
      transform: scale(1.15) rotate(-8deg);
    }
    .category-filter .btn.active,
    .category-filter .btn.btn-primary {
      background: #8EB486;
      color: #fff !important;
      font-weight: bold;
      border: none;
      box-shadow: 0 10px 22px rgba(142,180,134,0.11);
    }
    .category-filter .btn { margin: 0 4px 9px 4px; }
    .btn-add {
      background: #8EB486;
      color: #fff !important;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
      font-weight: 600;
      font-size: 1em;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      box-shadow: 0 6px 18px rgba(142,180,134,0.07);
      cursor: pointer;
      transition: background .18s, color .18s, box-shadow .18s, transform .16s;
    }
    .btn-add:hover, .btn-add:focus {
      background: #6a9c5b;
      color: #fff !important;
      box-shadow: 0 12px 26px rgba(104,180,130,0.13);
      transform: translateY(-2px) scale(1.04);
      text-decoration: none;
    }
    .btn-add i { font-size: 1.07em; margin-right: 5px; transition: transform .16s; }
    .btn-add:hover i { transform: scale(1.15) rotate(-8deg); }
  </style>
</head>
<body>
<header class="site-header">
  <div class="container nav">
    <a href="index.php" class="brand">
      <img src="images/logo.png" alt="Baytisan logo" class="logo">
      <span class="brand-text">Baytisan</span>
    </a>
    <nav class="main-nav">
      <a href="index.php">Home</a>
      <a href="products.php" class="active">Shop</a>
      <a href="order_history.php">Orders</a>
      <a href="admin_dashboard.php">Admin</a>
    </nav>
    <div class="nav-actions" id="navActions">
      <?php if ($loggedIn): ?>
        <span class="welcome"><i class="fa fa-user"></i> Welcome, <?= htmlspecialchars($firstName) ?></span>
        <?php if ($role === 'customer'): ?>
          <a href="profile.php" class="btn btn-primary" style="margin-left:8px;">
            <i class="fa fa-user"></i> Profile
          </a>
        <?php elseif ($role === 'admin'): ?>
          <a href="admin_profile.php" class="btn btn-primary" style="margin-left:8px;">
            <i class="fa fa-user-shield"></i> Admin Profile
          </a>
        <?php endif; ?>
        <a href="cart.php" class="btn"><i class="fa fa-shopping-cart"></i> Cart (<?= $cartCount ?>)</a>
        <a href="logout.php" class="btn btn-danger"><i class="fa fa-sign-out-alt"></i> Logout</a>
      <?php else: ?>
        <a href="index.php" class="btn"><i class="fa fa-sign-in-alt"></i> Login / Signup</a>
        <a href="cart.php" class="btn"><i class="fa fa-shopping-cart"></i> Cart (0)</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="container" style="padding:20px 0">
  <h2><i class="fa fa-store"></i> Our Products</h2>

  <!-- Category Filter Buttons -->
  <div class="category-filter">
    <a href="products.php" class="btn <?php echo ($categoryFilter === 0) ? 'active' : ''; ?>"><i class="fa fa-th-large"></i> All</a>
    <?php foreach ($categories as $cat): ?>
      <a href="products.php?category=<?= $cat['id'] ?>"
         class="btn <?php echo ($categoryFilter === (int)$cat['id']) ? 'active' : ''; ?>">
         <?= htmlspecialchars($cat['name']) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="product-grid">
    <?php if (empty($products)): ?>
      <p>No products found.</p>
    <?php else: ?>
      <?php foreach ($products as $p): ?>
        <div class="card">
          <div class="img-wrap">
            <img src="<?= htmlspecialchars(product_image_path($p['image_filename'])) ?>"
                 alt="<?= htmlspecialchars($p['name']) ?>">
          </div>
          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <p class="text-muted"><?= htmlspecialchars($p['category_name']) ?><?= $p['location_name'] ? ' • ' . htmlspecialchars($p['location_name']) : '' ?></p>
          <p class="price">₱<?= number_format($p['price'],2) ?></p>
          <form method="POST" action="cart.php" style="display:flex;gap:8px;align-items:center;margin-top:8px">
            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
            <input type="number" name="qty" value="1" min="1" style="width:72px;padding:8px;border-radius:8px;border:1px solid #ddd">
            <button type="submit" class="btn-add"><i class="fa fa-cart-plus"></i> Add</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>
</body>
</html>