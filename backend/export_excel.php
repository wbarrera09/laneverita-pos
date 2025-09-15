<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$pdo = pdo();

$start   = $_GET['start']    ?? null;
$end     = $_GET['end']      ?? null;
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

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Reporte Órdenes");

// Encabezados
$headers = ["ID", "Fecha", "Cliente", "Total", "Método de pago", "Notas"];
$sheet->fromArray($headers, null, "A1");

// Estilo encabezados
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4F81BD'] // azul
    ]
];
$sheet->getStyle("A1:F1")->applyFromArray($headerStyle);

// Insertar datos
$rowNum = 2;
foreach ($rows as $r) {
    $sheet->setCellValue("A$rowNum", $r['id']);
    $sheet->setCellValue("B$rowNum", $r['date_time']);
    $sheet->setCellValue("C$rowNum", $r['customer_name']);
    $sheet->setCellValue("D$rowNum", (float)$r['total']);
    $sheet->setCellValue("E$rowNum", $r['payment_method']);
    $sheet->setCellValue("F$rowNum", $r['notes']);
    $rowNum++;
}

// Formato de moneda en columna D
$sheet->getStyle("D2:D$rowNum")
      ->getNumberFormat()
      ->setFormatCode('#,##0.00');

// Formato de fecha en columna B
$sheet->getStyle("B2:B$rowNum")
      ->getNumberFormat()
      ->setFormatCode('dd/mm/yyyy hh:mm');

// Zebra stripes (filas alternadas gris claro)
for ($i = 2; $i < $rowNum; $i++) {
    if ($i % 2 == 0) {
        $sheet->getStyle("A$i:F$i")->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setRGB('F2F2F2');
    }
}

// Ajustar ancho columnas
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Descargar Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="report_orders.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
