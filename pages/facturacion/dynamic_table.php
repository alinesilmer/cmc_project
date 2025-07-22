<?php include 'sidebar.php'; ?>
<?php
session_start();

/* 1) Shared utilities */
$utils = __DIR__ . '/utils.php';
if (is_file($utils)) {
    /** @noinspection PhpIncludeInspection */
    require_once $utils;
}
if (!function_exists('normalizeKey')) {
    function normalizeKey(string $label): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $label);
        $key   = preg_replace('/[^A-Za-z0-9]+/', '_', $ascii);
        $key   = strtolower(trim($key, '_'));
        if ($key === '') {
            $map = ['%' => 'porcentaje', '#' => 'numero'];
            $key = $map[$label] ?? 'col_' . dechex(crc32($label));
        }
        return $key;
    }
}

/* Capture obra_social */
$obra = $_GET['obra_social'] ?? '';

/* 2) Seed headers on first load */
if (empty($_SESSION['headers'])) {
    $_SESSION['headers'] = ['Doctor', 'Matrícula', 'Fecha', 'Total'];
    foreach ($_SESSION['headers'] as $lbl) {
        $k = normalizeKey($lbl);
        $_SESSION['types'][$k] = $lbl === 'Fecha' ? 'date' : 'text';
    }
}

/* 3) Ensure session defaults */
$_SESSION['table_data'] = $_SESSION['table_data'] ?? [];
$_SESSION['dirty']      = $_SESSION['dirty']      ?? false;
$_SESSION['approved']   = $_SESSION['approved']   ?? false;

/* 4) “Enviar para Aprobación” */
if (isset($_GET['approve'])) {
    $_SESSION['dirty']    = false;
    $_SESSION['approved'] = true;
    header('Location: /pages/Facturacion/dynamic_table.php?obra_social=' . urlencode($obra) . '&sent=1');
    exit;
}

/* 5) “Eliminar fila” */
if (isset($_GET['delete_row'])) {
    $i = (int)$_GET['delete_row'];
    if (isset($_SESSION['table_data'][$i])) {
        array_splice($_SESSION['table_data'], $i, 1);
    }
    header('Location: dynamic_table.php?obra_social=' . urlencode($obra));
    exit;
}

/* 6) “Enviar Resumen” */
if (isset($_GET['send_resumen'])) {
    $filtered = [];
    $kDoc  = normalizeKey('Doctor');
    $kMat  = normalizeKey('Matrícula Prov');
    $kF    = normalizeKey('Fecha');
    $kTot  = normalizeKey('Subtotal');
    foreach ($_SESSION['table_data'] as $r) {
        $filtered[] = [
            'Doctor'    => isset($r[$kDoc]) ? $r[$kDoc] : '',
            'Matrícula' => isset($r[$kMat]) ? $r[$kMat] : '',
            'Fecha'     => isset($r[$kF])   ? $r[$kF]   : '',
            'Total'     => isset($r[$kTot]) ? $r[$kTot] : '',
        ];
    }
    $_SESSION['liquidacion_data'] = $filtered;
    header('Location: ver_resumen.php?obra_social=' . urlencode($obra));
    exit;
}

/* 7) Prepare rendering */
$headers             = $_SESSION['headers'];
$types               = $_SESSION['types'];
$data                = $_SESSION['table_data'];
$btnApproveDisabled  = $_SESSION['dirty']    ? '' : 'disabled';
$btnResumenDisabled  = $_SESSION['approved'] ? '' : 'disabled';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Resumen Obra Social</title>
    <style>
        :root {
            --primary: #3066be;
            --primary-light: #e8efff;
            --accent: #f5b700;
            --bg: #f9f9fb;
            --surface: #fff;
            --text: #2d2d38;
            --danger: #e54f6d;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto;
            background: var(--bg);
            color: var(--text);
        }

        header {
            background: var(--primary);
            color: #fff;
            padding: 1.2rem;
            text-align: center;
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            background: var(--surface);
            padding: 1rem;
        }

        .controls .group {
            display: flex;
            gap: .6rem;
        }

        .controls a,
        .controls button {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: .55rem 1rem;
            border-radius: .55rem;
            cursor: pointer;
            text-decoration: none;
            transition: background .25s;
            font-size: .9rem;
        }

        .controls a.secondary {
            background: var(--primary-light);
            color: var(--primary);
        }

        .controls button:disabled {
            background: #d4d9e8;
            color: #999;
            cursor: not-allowed;
        }

        .container {
            max-width: 1100px;
            margin: 2rem auto;
            background: var(--surface);
            padding: 1.5rem;
            border-radius: .75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .9rem;
        }

        thead {
            background: var(--primary);
            color: #fff;
            position: sticky;
            top: 0;
        }

        th,
        td {
            padding: .7rem .8rem;
            border: 1px solid #e0e0e6;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background: var(--primary-light);
        }

        tbody tr:hover {
            background: #f3f6ff;
        }

        .btn-edit {
            background: var(--accent);
            color: #000;
        }

        .btn-delete {
            background: var(--danger);
            color: #fff;
        }

        .btn-edit:hover,
        .btn-delete:hover {
            opacity: .9;
        }

        /* Modal base */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s;
        }

        .modal.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content {
            background: var(--surface);
            padding: 1.4rem;
            border-radius: .6rem;
            text-align: center;
            max-width: 380px;
            width: calc(100% - 2rem);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .modal-content h3 {
            margin-top: 0;
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

        .modal-content .secondary {
            background: var(--primary-light);
            color: var(--primary);
            margin-left: .5rem;
        }
    </style>
</head>

<body>
    <main>
        <header>
            <h1>Resumen: Obra Social "<?= htmlspecialchars($obra ?: 'Sancor') ?>"</h1>
        </header>

        <div class="controls">
            <!-- … your control buttons … -->
        </div>

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
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="<?= count($headers) + 1 ?>" style="text-align:center">No hay datos.</td>
                        </tr>
                        <?php else: foreach ($data as $i => $fila): ?>
                            <tr>
                                <?php foreach ($headers as $col):
                                    $key = normalizeKey($col);
                                    // **Safe access here**:
                                    $val = isset($fila[$key]) ? $fila[$key] : '';
                                    if ($types[$key] === 'date' && $val) {
                                        $d = DateTime::createFromFormat('Y-m-d', $val);
                                        $val = $d ? $d->format('d/m/Y') : $val;
                                    }
                                ?>
                                    <td><?= htmlspecialchars($val) ?></td>
                                <?php endforeach; ?>
                                <td>
                                    <button class="btn-edit"
                                        onclick="location.href='editar_datos.php?index=<?= $i ?>'">
                                        Editar
                                    </button>
                                    <button class="btn-delete"
                                        onclick="if(confirm('¿Eliminar fila?'))
                           location.href='/pages/Facturacion/dynamic_table.php?delete_row=<?= $i ?>'">
                                        Eliminar
                                    </button>
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

        // Approve
        document.getElementById('approveBtn').onclick = () => {
            if (!params.has('obra_social')) return;
            location.href = `/pages/Facturacion/dynamic_table.php?obra_social=${encodeURIComponent(params.get('obra_social'))}&approve=1`;
        };

        // Show success modal after approval
        if (params.has('sent')) {
            document.getElementById('successModal').classList.add('active');
        }
        document.getElementById('closeModal').onclick = () => {
            document.getElementById('successModal').classList.remove('active');
            params.delete('sent');
            history.replaceState(null, '', location.pathname + (params.toString() ? '?' + params.toString() : ''));
        };

        // Import modal
        document.getElementById('openImport').onclick = () => document.getElementById('importModal').classList.add('active');
        document.getElementById('cancelImport').onclick = () => document.getElementById('importModal').classList.remove('active');
        document.getElementById('importForm').addEventListener('submit', () => {
            document.getElementById('importModal').classList.remove('active');
        });

        // Send Resumen
        document.getElementById('sendResumenBtn').onclick = () => {
            if (!params.has('obra_social')) return;
            location.href = `/pages/Facturacion/dynamic_table.php?obra_social=${encodeURIComponent(params.get('obra_social'))}&send_resumen=1`;
        };
    </script>
</body>

</html>