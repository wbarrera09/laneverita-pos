<?php
// backend/save_order.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

try {
    $raw = file_get_contents('php://input');
    if ($raw === false) throw new RuntimeException('No se pudo leer el body');
    $payload = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

    // Validaciones mínimas
    if (!isset($payload['cartEntries']) || !is_array($payload['cartEntries']) || count($payload['cartEntries']) === 0) {
        throw new InvalidArgumentException('El carrito está vacío');
    }

    $pdo = pdo();
    $pdo->beginTransaction();

    // Insertar orden
    $stmt = $pdo->prepare("
        INSERT INTO orders
        (date_time, subtotal, tax, total, currency, payment_method, card_type,
         cash_amount, change_amount, customer_name, notes)
        VALUES (NOW(), :subtotal, :tax, :total, :currency, :payment_method, :card_type,
                :cash_amount, :change_amount, :customer_name, :notes)
    ");
    $stmt->execute([
        ':subtotal'      => $payload['subtotal'] ?? 0,
        ':tax'           => $payload['tax'] ?? 0,
        ':total'         => $payload['total'] ?? 0,
        ':currency'      => $payload['currency'] ?? 'USD',
        ':payment_method'=> $payload['paymentMethod'] ?? 'card',
        ':card_type'     => $payload['cardType'] ?? null,
        ':cash_amount'   => $payload['cashAmount'] !== null ? (float)$payload['cashAmount'] : null,
        ':change_amount' => $payload['change'] !== null ? (float)$payload['change'] : null,
        ':customer_name' => $payload['customerName'] ?? null,
        ':notes'         => $payload['notes'] ?? null,
    ]);

    $orderId = (int)$pdo->lastInsertId();

    // Insertar items
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items
        (order_id, product_id, name, unit_price, qty, line_total)
        VALUES (:order_id, :product_id, :name, :unit_price, :qty, :line_total)
    ");

    foreach ($payload['cartEntries'] as $line) {
        $stmtItem->execute([
            ':order_id'   => $orderId,
            ':product_id' => $line['id'],  // ← Esto antes era un slug
            ':name'       => $line['name'],
            ':unit_price' => $line['unitPrice'],
            ':qty'        => $line['qty'],
            ':line_total' => $line['lineTotal'],
        ]);
    }

    $pdo->commit();
    echo json_encode(['ok' => true, 'order_id' => $orderId], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
