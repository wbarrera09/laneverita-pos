<?php
// backend/get_sold_items.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

try {
    $pdo = pdo();

    $sql = "SELECT 
                oi.id,
                o.id AS order_id,
                o.date_time,
                o.customer_name,
                o.payment_method,
                oi.name AS product_name,
                oi.qty,
                oi.unit_price,
                oi.line_total
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE 1=1";
    $params = [];

    // Filtros
    if (!empty($_GET['start'])) {
        $sql .= " AND o.date_time >= :start";
        $params[':start'] = $_GET['start'] . " 00:00:00";
    }
    if (!empty($_GET['end'])) {
        $sql .= " AND o.date_time <= :end";
        $params[':end'] = $_GET['end'] . " 23:59:59";
    }
    if (!empty($_GET['customer'])) {
        $sql .= " AND o.customer_name LIKE :customer";
        $params[':customer'] = "%" . $_GET['customer'] . "%";
    }
    if (!empty($_GET['product'])) {
        $sql .= " AND oi.name LIKE :product";
        $params[':product'] = "%" . $_GET['product'] . "%";
    }
    if (!empty($_GET['payment_method'])) {
        $sql .= " AND o.payment_method = :payment_method";
        $params[':payment_method'] = $_GET['payment_method'];
    }

    $sql .= " ORDER BY o.date_time DESC LIMIT 500"; // límite de seguridad

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
