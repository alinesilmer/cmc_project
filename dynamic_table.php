<?php include 'sidebar.php'; ?>
<?php
session_start();

/* === 1)  Incluimos utils.php con ruta absoluta  =============== */
$utilsPath = __DIR__ . '/utils.php';
if (is_file($utilsPath)) {
    /** @noinspection PhpIncludeInspection */
    require_once $utilsPath;
}

/* === 2)  Fallback por si utils.php no se encontró ============== */
if (!function_exists('normalizeKey')) {
    function normalizeKey(string $label): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $label);
        $key   = preg_replace('/[^A-Za-z0-9]+/', '_', $ascii);
        $key   = strtolower(trim($key, '_'));

        if ($key === '') {
            $replacements = ['%' => 'porcentaje', '#' => 'numero'];
            $key = $replacements[$label] ?? 'col_' . dechex(crc32($label));
        }
        return $key;
    }
}

$_SESSION['dirty']     = $_SESSION['dirty']     ?? false;
$_SESSION['approved']  = $_SESSION['approved']  ?? false;

/* ---------- clic en Aprobación ---------- */
if (isset($_GET['approve'])) {
    $_SESSION['dirty']    = false;
    $_SESSION['approved'] = true;
    header('Location: dynamic_table.php?sent=1');
    exit;
}

$btnApproveDisabled = $_SESSION['dirty']     ? '' : 'disabled';
$btnResumenDisabled = $_SESSION['approved']  ? '' : 'disabled';

/* -------- CONFIG ---------------- */
$default_headers = [
    'Socio',
    'Nombre Socio',
    'Matrícula Prov',
    'Nro. Orden',
    'Fecha',
    'Código',
    'Nro. Afiliado',
    'Nombre Afiliado',
    'Cantidad',
    '%',
    'Honorarios',
    'Gastos',
    'Subtotal'
];

/* --- 1)  Sincroniza cabeceras por código vs. sesión ------- */
$headers_hash = md5(json_encode($default_headers));
if (!isset($_SESSION['headers_hash']) || $_SESSION['headers_hash'] !== $headers_hash) {
    $_SESSION['headers']      = $default_headers;
    $_SESSION['headers_hash'] = $headers_hash;
    $_SESSION['types']        = [];  // fuerza recálculo tipos
}

/* --- 2)  Tipos de dato por columna ----------------------- */
foreach ($_SESSION['headers'] as $label) {
    $key = normalizeKey($label);
    if (!isset($_SESSION['types'][$key])) {
        $_SESSION['types'][$key] = ($label === 'Fecha' ? 'date'
            : ($label === '%'     ? 'number'
                :  'text'));
    }
}

/* --- 3)  Estructuras base -------------------------------- */
$_SESSION['table_data'] = $_SESSION['table_data'] ?? [];
$_SESSION['approved']   = $_SESSION['approved']   ?? false;

/* --- 3a) MIGRA filas antiguas con clave "" → "porcentaje" */
foreach ($_SESSION['table_data'] as &$fila) {
    if (isset($fila['']) && !isset($fila['porcentaje'])) {
        $fila['porcentaje'] = $fila[''];
        unset($fila['']);
    }
}
unset($fila);  // rompe referencia

/* --- 4)  Acciones GET ----------------------------------- */
if (isset($_GET['approve'])) {
    $_SESSION['approved'] = true;
    header('Location: dynamic_table.php');
    exit;
}

if (isset($_GET['delete_row'])) {
    $idx = (int)$_GET['delete_row'];
    if (isset($_SESSION['table_data'][$idx])) {
        array_splice($_SESSION['table_data'], $idx, 1);
    }
    header('Location: dynamic_table.php');
    exit;
}

/* --- 5)  Atajos para render ----------------------------- */
$headers = &$_SESSION['headers'];
$data    = &$_SESSION['table_data'];
$types   = &$_SESSION['types'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Resumen Obra Social</title>
    <!-- ------------- ESTILOS (idénticos a los que ya tenías) -------------- -->
    <style>
        :root {
            --primary: #3066be;
            --primary-light: #e8efff;
            --accent: #f5b700;
            --bg: #f9f9fb;
            --surface: #fff;
            --text: #2d2d38;
            --text-muted: #555a6f;
            --danger: #e54f6d;
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto;
            background: var(--bg);
            color: var(--text);
            line-height: 1.45;
        }

        header {
            background: var(--primary);
            color: #fff;
            padding: 1.2rem 1rem;
            text-align: center;
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem 1.2rem;
            justify-content: center;
            padding: 1rem;
            background: var(--surface);
        }

        .controls .group {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap
        }

        .controls a,
        .controls button {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: .55rem 1rem;
            border-radius: .55rem;
            font-size: .88rem;
            text-decoration: none;
            cursor: pointer;
            transition: background .25s, box-shadow .25s;
        }

        .controls a:hover,
        .controls button:hover:not(:disabled) {
            background: var(--primary-light);
            color: var(--primary);
        }

        .controls button:disabled {
            background: #d4d9e8;
            color: var(--text-muted);
            cursor: not-allowed;
        }

        .secondary {
            background: var(--primary-light);
            color: var(--primary);
        }

        .container {
            max-width: 1300px;
            margin: 1.6rem auto;
            background: var(--surface);
            padding: 1.2rem;
            border-radius: .75rem;
            box-shadow: 0 2px 8px rgb(0 0 0 / .06);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .78rem;
        }

        thead {
            background: var(--primary);
            color: #fff;
            position: sticky;
            top: 0;
        }

        th,
        td {
            padding: .65rem .75rem;
            border: 1px solid #e0e0e6;
            text-align: left;
        }

        th:not(:last-child),
        td:not(:last-child) {
            border-right: 2px solid #dfe4f5;
        }

        tbody tr:nth-child(even) {
            background: var(--primary-light);
        }

        tbody tr:hover {
            background: #f3f6ff;
        }

        .btn-edit {
            background: var(--accent);
            color: #000
        }

        .btn-edit:hover {
            background: #ffe39c
        }

        .btn-delete {
            background: var(--danger);
            color: #fff
        }

        .btn-delete:hover {
            background: #ff7689
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s;
        }

        .modal.active {
            opacity: 1;
            pointer-events: auto
        }

        .modal-content {
            background: var(--surface);
            padding: 1.4rem;
            border-radius: .6rem;
            text-align: center;
            max-width: 320px;
            width: calc(100% - 2rem);
            box-shadow: 0 4px 16px rgb(0 0 0 / .12);
        }

        #importModal .modal-content {
            max-width: 380px
        }

        .modal-content button {
            margin-top: 1rem;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: .55rem 1.1rem;
            border-radius: .55rem;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <main>
        <header>
            <h1>Resumen: Obra Social "<?= htmlspecialchars($_GET['obra_social'] ?? 'Sancor') ?>"</h1>
        </header>
    
        <!-- ---------------- BARRA DE CONTROLES ----------------- -->
        <div class="controls">
            <!-- botón de regreso -->
            <div class="group">
                <a href="obras_sociales.php" class="secondary">← Volver al listado</a>
            </div>
    
            <div class="group">
                <a href="agregar_datos.php?obra_social=<?= urlencode($_GET['obra_social'] ?? '') ?>">Agregar Datos</a>
                <a href="modificar_tabla.php?obra_social=<?= urlencode($_GET['obra_social'] ?? '') ?>">Configurar Tabla</a>
            </div>
    
            <div class="group">
                <button id="openImport">Importar Excel</button>
                <a href="export_excel.php?obra_social=<?= urlencode($_GET['obra_social'] ?? '') ?>" class="secondary">Exportar Excel</a>
            </div>
    
            <div class="group">
                <button id="approveBtn" <?= $btnApproveDisabled ?>>Enviar para Aprobación</button>
                <button id="sendResumenBtn" <?= $btnResumenDisabled ?>>Enviar Resumen</button>
            </div>
        </div>
    
    
        <!-- ---------------- TABLA ------------------------------- -->
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <?php foreach ($headers as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$data): ?>
                        <tr>
                            <td colspan="<?= count($headers) + 1 ?>" style="text-align:center">No hay datos.</td>
                        </tr>
                        <?php else: foreach ($data as $i => $fila): ?>
                            <tr>
                                <?php foreach ($headers as $col):
                                    $key = normalizeKey($col);
                                    $val = $fila[$key] ?? '';
                                    if ($types[$key] === 'date' && $val) {
                                        $d = DateTime::createFromFormat('Y-m-d', $val);
                                        $val = $d ? $d->format('d/m/Y') : $val;
                                    } ?>
                                    <td><?= htmlspecialchars($val) ?></td>
                                <?php endforeach; ?>
                                <td>
                                    <button class="btn-edit" onclick="location.href='editar_datos.php?index=<?= $i ?>'">Editar</button>
                                    <button class="btn-delete" onclick="if(confirm('¿Eliminar fila?')) location.href='dynamic_table.php?delete_row=<?= $i ?>'">Eliminar</button>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    
        <!-- --------------- MODALES ----------------------------- -->
        <div id="successModal" class="modal">
            <div class="modal-content">
                <p>Enviado, se avisará cuando esté aprobada.</p>
                <button id="closeModal">Cerrar</button>
            </div>
        </div>
    
        <div id="importModal" class="modal">
            <div class="modal-content">
                <h3>Importar datos desde Excel</h3>
                <form id="importForm" action="import_excel.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="obra_social" value="<?= htmlspecialchars($_GET['obra_social'] ?? '') ?>">
                    <input type="file" name="xlsx" accept=".xlsx,.xls" required>
                    <div style="margin-top:1rem;display:flex;gap:.6rem;justify-content:center">
                        <button type="submit">Subir Archivo</button>
                        <button type="button" id="cancelImport" class="secondary">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

    </main>

    <!-- --------------- JS ---------------------------------- -->
    <script>
        const params = new URLSearchParams(window.location.search);

        document.getElementById('approveBtn').onclick = () => {
            if (document.getElementById('approveBtn').disabled) return;
            const obra = params.get('obra_social') || '';
            location.href = `dynamic_table.php?obra_social=${encodeURIComponent(obra)}&approve=1`;
        };

        /* mostrar modal si acabamos de aprobar */
        if (params.has('sent')) {
            document.getElementById('successModal').classList.add('active');
        }

        /* cerrar modal y limpiar ?sent de la url */
        document.getElementById('closeModal').onclick = () => {
            document.getElementById('successModal').classList.remove('active');
            params.delete('sent');
            history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));
        };

        /* Import modal */
        document.getElementById('openImport').onclick = () => document.getElementById('importModal').classList.add('active');
        document.getElementById('cancelImport').onclick = () => document.getElementById('importModal').classList.remove('active');
        document.getElementById('importForm').addEventListener('submit', () => document.getElementById('importModal').classList.remove('active'));
    </script>
</body>

</html>