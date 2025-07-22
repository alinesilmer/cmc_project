<?php
session_start();

/* 1)  Funciones compartidas */
require_once __DIR__ . '/utils.php';

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

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/* 3)  Verificamos subida */

if (empty($_FILES['xlsx']) || $_FILES['xlsx']['error'] !== UPLOAD_ERR_OK) {
    die('Error subiendo el archivo');
}
$tmp = $_FILES['xlsx']['tmp_name'];

/* 4)  Leemos la hoja (claves numéricas) */
$sheet = IOFactory::load($tmp)->getActiveSheet();
$rows  = $sheet->toArray(null, true, true, false);   // columnas 0,1,2…

/* 5)  Mapeamos cabeceras Excel → claves de la tabla */
$excelHeaders = array_filter($rows[0]);            // fila 0
$tableHeaders = $_SESSION['headers'] ?? [];

$map = []; // idx columna en Excel → key tabla
foreach ($excelHeaders as $idx => $label) {
    $norm = normalizeKey($label);
    foreach ($tableHeaders as $tblLabel) {
        if ($norm === normalizeKey($tblLabel)) {
            $map[$idx] = $norm;
            break;
        }
    }
}

/* 6)  Insertamos filas */
for ($r = 1; $r < count($rows); $r++) {
    $fila = [];
    foreach ($map as $idx => $key) {
        $cell = $rows[$r][$idx] ?? '';

        // Normaliza seriales de fecha
        if ($key === 'fecha' && is_numeric($cell)) {
            $cell = Date::excelToDateTimeObject($cell)->format('Y-m-d');
        }
        $fila[$key] = $cell;
    }
    if (array_filter($fila)) {        // evita fila vacía
        $_SESSION['table_data'][] = $fila;
    }
}

/* 7)  Redirigimos a la tabla */
$obra = $_POST['obra_social'] ?? $_GET['obra_social'] ?? '';
header('Location: /pages/Facturacion/dynamic_table.php?obra_social=' . urlencode($obra));
exit;
