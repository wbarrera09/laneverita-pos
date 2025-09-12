<?php
// backend/get_payment_methods.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

$pdo = pdo();
$stmt = $pdo->query("SELECT DISTINCT payment_method FROM orders ORDER BY payment_method");
$methods = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($methods, JSON_UNESCAPED_UNICODE);
