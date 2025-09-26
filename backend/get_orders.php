<?php
// backend/get_orders.php

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';


try {
    $pdo = pdo();

    $sql = "SELECT id, date_time, customer_name, total, payment_method, notes
            FROM orders
            WHERE 1=1";
    $params = [];

    // Filtros
    if (!empty($_GET['start'])) {
        $sql .= " AND date_time >= :start";
        $params[':start'] = $_GET['start'] . " 00:00:00";
    }
    if (!empty($_GET['end'])) {
        $sql .= " AND date_time <= :end";
        $params[':end'] = $_GET['end'] . " 23:59:59";
    }
    if (!empty($_GET['order_id'])) {
        $sql .= " AND id = :order_id";
        $params[':order_id'] = (int) $_GET['order_id'];
    }
    if (!empty($_GET['customer'])) {
        $sql .= " AND customer_name LIKE :customer";
        $params[':customer'] = "%" . $_GET['customer'] . "%";
    }
    if (!empty($_GET['payment_method'])) {
        $sql .= " AND payment_method = :payment_method";
        $params[':payment_method'] = $_GET['payment_method'];
    }

    $sql .= " ORDER BY date_time DESC LIMIT 100"; // límite de seguridad

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
