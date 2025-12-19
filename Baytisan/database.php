<?php
// database.php - PDO connection
$db_host = '127.0.0.1';
$db_name = 'baytisan_db';
$db_user = 'root';
$db_pass = ''; // change if needed

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
  die("DB connection failed: " . $e->getMessage());
}

