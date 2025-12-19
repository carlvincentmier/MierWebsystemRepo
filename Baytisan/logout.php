<?php
session_start();
session_unset();
session_destroy();
?>
<!doctype html>
<html>
<head>
  <script>
    localStorage.removeItem('baytisan_cart_count');
    window.location.href = "index.php";
  </script>
  <noscript>
    <meta http-equiv="refresh" content="0;url=index.php">
  </noscript>
</head>
<body>
  <p>Logging out...</p>
</body>
</html>