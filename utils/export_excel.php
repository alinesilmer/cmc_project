<?php
session_start();

/* 1)  Funciones de utilidades */
require_once __DIR__ . '/utils.php';   // misma carpeta

/* 1-bis)  Fallback (por si utils.php faltara) */
if (!function_exists('normalizeKey')) {
    function normalizeKey(string $label): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $label);
        $key   = preg_replace('/[^A-Za-z0-9]+/', '_', $ascii);
        $key   = strtolower(trim($key, '_'));
        if ($key === '') {
            $key = $label === '%' ? 'porcentaje' : 'col_' . dechex(crc32($label));
        }
        return $key;
    }
}

/* 2)  Librería Excel */
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/* 3)  Datos de sesión */

$headers = $_SESSION['headers']    ?? [];
$data    = $_SESSION['table_data'] ?? [];

/* 4)  Creamos libro y hoja */
$ss = new Spreadsheet();
$sh = $ss->getActiveSheet();
$sh->fromArray($headers, null, 'A1');

/* 5)  Rellenamos filas */
$rowNum = 2;
foreach ($data as $fila) {
    $values = [];
    foreach ($headers as $label) {
        $values[] = $fila[normalizeKey($label)] ?? '';
    }
    $sh->fromArray($values, null, 'A' . $rowNum++);
}

/* 6)  Enviamos al navegador */
$filename = 'resumen_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer = new Xlsx($ss);
$writer->save('php://output');
exit;
