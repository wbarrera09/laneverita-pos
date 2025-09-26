<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$pdo = pdo();

$start   = $_GET['start']    ?? null;
$end     = $_GET['end']      ?? null;
$customer = $_GET['customer'] ?? null;
$product  = $_GET['product']  ?? null;
$payment  = $_GET['payment_method'] ?? null;

$sql = "SELECT 
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

if ($start && $end) {
  $sql .= " AND o.date_time BETWEEN :start AND :end";
  $params[':start'] = $start . " 00:00:00";
  $params[':end']   = $end . " 23:59:59";
}

if ($customer) {
  $sql .= " AND o.customer_name LIKE :customer";
  $params[':customer'] = "%" . $customer . "%";
}

if ($product) {
  $sql .= " AND oi.name LIKE :product";
  $params[':product'] = "%" . $product . "%";
}

if ($payment) {
  $sql .= " AND o.payment_method = :payment_method";
  $params[':payment_method'] = $payment;
}

$sql .= " ORDER BY o.date_time DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Productos Vendidos");

// Encabezados
$headers = ["Orden", "Fecha", "Cliente", "Producto", "Cantidad", "P. Unitario", "Subtotal", "Método de pago"];
$sheet->fromArray($headers, null, "A1");

// Estilo encabezados
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4F81BD']
    ]
];
$sheet->getStyle("A1:H1")->applyFromArray($headerStyle);

// Insertar datos
$rowNum = 2;
foreach ($rows as $r) {
    $sheet->setCellValue("A$rowNum", $r['order_id']);
    $sheet->setCellValue("B$rowNum", $r['date_time']);
    $sheet->setCellValue("C$rowNum", $r['customer_name']);
    $sheet->setCellValue("D$rowNum", $r['product_name']);
    $sheet->setCellValue("E$rowNum", (int)$r['qty']);
    $sheet->setCellValue("F$rowNum", (float)$r['unit_price']);
    $sheet->setCellValue("G$rowNum", (float)$r['line_total']);
    $sheet->setCellValue("H$rowNum", $r['payment_method']);
    $rowNum++;
}

// Formato de moneda
$sheet->getStyle("F2:G$rowNum")
      ->getNumberFormat()
      ->setFormatCode('#,##0.00');

// Formato de fecha
$sheet->getStyle("B2:B$rowNum")
      ->getNumberFormat()
      ->setFormatCode('dd/mm/yyyy hh:mm');

// Zebra stripes
for ($i = 2; $i < $rowNum; $i++) {
    if ($i % 2 == 0) {
        $sheet->getStyle("A$i:H$i")->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()->setRGB('F2F2F2');
    }
}

// Auto size columnas
foreach (range('A', 'H') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Descargar Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="report_sold_items.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
