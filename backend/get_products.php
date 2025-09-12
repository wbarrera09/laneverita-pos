<?php
// backend/get_products.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

try {
    $pdo = pdo();

    // Traer categorías
    $cats = $pdo->query("SELECT id, label, icon FROM categories ORDER BY label")
                ->fetchAll(PDO::FETCH_ASSOC);

    // Traer productos
    $prods = $pdo->query("SELECT id, name, price, category_id AS category, image FROM products ORDER BY name")
                 ->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'categories' => $cats,
        'products'   => $prods
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
