<?php
require __DIR__ . '/db.php';

$pdo = pdo();

$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;
$orderId = $_GET['order_id'] ?? null;

$sql = "SELECT id, date_time, customer_name, total, payment_method, notes
        FROM orders
        WHERE 1=1";

$params = [];

if ($start && $end) {
  $sql .= " AND date_time BETWEEN :start AND :end";
  $params[':start'] = $start . " 00:00:00";
  $params[':end']   = $end . " 23:59:59";
}

if ($orderId) {
  $sql .= " AND id = :id";
  $params[':id'] = $orderId;
}

$sql .= " ORDER BY date_time DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// headers de descarga
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=report_orders.csv");

// salida
$output = fopen("php://output", "w");
fputcsv($output, ["ID", "Fecha", "Cliente", "Total", "Método de pago", "Notas"]);

foreach ($rows as $r) {
  fputcsv($output, [
    $r['id'],
    $r['date_time'],
    $r['customer_name'],
    $r['total'],
    $r['payment_method'],
    $r['notes']
  ]);
}

fclose($output);
exit;
