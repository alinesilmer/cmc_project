<?php
session_start();

/* ——— CONFIG ——— */
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

/* --- 1) Detecta si la lista por defecto cambió -------- */
$headers_hash = md5(json_encode($default_headers));

if (!isset($_SESSION['headers_hash']) || $_SESSION['headers_hash'] !== $headers_hash) {
    //  ↳ Es la primera vez O bien modificaste $default_headers en el código
    $_SESSION['headers']      = $default_headers;
    $_SESSION['headers_hash'] = $headers_hash;
    $_SESSION['types']        = [];          // obliga a recalcular tipos
}

/* --- 2) Normaliza claves y tipos ----------------------- */
function normalizeKey(string $label): string
{
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $label);
    $key   = preg_replace('/[^A-Za-z0-9]+/', '_', $ascii);
    return strtolower(trim($key, '_'));
}

foreach ($_SESSION['headers'] as $col) {
    $key = normalizeKey($col);
    if (!isset($_SESSION['types'][$key])) {
        $_SESSION['types'][$key] = ($col === 'Fecha' ? 'date' : 'text');
    }
}
// Inicializar datos y bandera de aprobación
if (!isset($_SESSION['table_data'])) {
    $_SESSION['table_data'] = [];
}
if (!isset($_SESSION['approved'])) {
    $_SESSION['approved'] = false;
}

// Pedir aprobación
if (isset($_GET['approve'])) {
    $_SESSION['approved'] = true;
    header('Location: dynamic_table.php');
    exit;
}
// Eliminar fila
if (isset($_GET['delete_row'])) {
    $idx = (int)$_GET['delete_row'];
    if (isset($_SESSION['table_data'][$idx])) {
        array_splice($_SESSION['table_data'], $idx, 1);
    }
    header('Location: dynamic_table.php');
    exit;
}

//Referencias de render
$headers = &$_SESSION['headers'];
$data    = &$_SESSION['table_data'];
$types   = &$_SESSION['types'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Obra Social</title>

    <style>
        :root {
            --primary: #3066be;
            --primary-light: #e8efff;
            --accent: #f5b700;
            --bg: #f9f9fb;
            --surface: #ffffff;
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
            gap: .6rem;
            justify-content: center;
            padding: 1rem;
            background: var(--surface);
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


        .btn-action {
            border: none;
            padding: .35rem .7rem;
            border-radius: .4rem;
            font-size: .8rem;
            cursor: pointer;
            transition: background .25s;
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
    <header>
        <h1>Resumen: Obra Social "<?= htmlspecialchars($_GET['obra_social'] ?? 'Sancor') ?>"</h1>
    </header>
    <div class="controls">
        <a href="agregar_datos.php?obra_social=<?= urlencode($_GET['obra_social'] ?? '') ?>">Agregar Datos</a>
        <a href="modificar_tabla.php?obra_social=<?= urlencode($_GET['obra_social'] ?? '') ?>">Configurar Tabla</a>
        <button onclick="alert('Exportar a Excel no implementado')">Exportar a Excel</button>
        <button id="approveBtn">Enviar para Aprobación</button>
        <button id="sendResumenBtn" <?= empty($_SESSION['approved']) ? 'disabled' : '' ?>>Enviar Resumen</button>
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
                        <td colspan="<?= count($headers) + 1 ?>" style="text-align:center;">No hay datos.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $i => $row): ?>
                        <tr>
                            <?php foreach ($headers as $col):
                                $key = normalizeKey($col);
                                $val = $row[$key] ?? '';
                                if ($types[$key] === 'date' && $val) {
                                    $d = DateTime::createFromFormat('Y-m-d', $val);
                                    $val = $d ? $d->format('d/m/Y') : $val;
                                }
                            ?>
                                <td><?= htmlspecialchars($val) ?></td>
                            <?php endforeach; ?>
                            <td>
                                <button class="btn btn-edit" onclick="location.href='editar_datos.php?index=<?= $i ?>'">Editar</button>
                                <button class="btn btn-delete" onclick="if(confirm('¿Eliminar fila?')) location.href='dynamic_table.php?delete_row=<?= $i ?>'">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="successModal" class="modal">
        <div class="modal-content">
            <p>Enviado, se avisará cuando esté aprobada.</p>
            <button id="closeModal">Cerrar</button>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('approved')) {
            document.getElementById('successModal').classList.add('active');
            document.getElementById('sendResumenBtn').disabled = false;
        }
        document.getElementById('approveBtn').addEventListener('click', () => {
            const obra = urlParams.get('obra_social') || '';
            window.location.href = `dynamic_table.php?obra_social=${encodeURIComponent(obra)}&approve=1`;
        });
        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('successModal').classList.remove('active');
            const obra = urlParams.get('obra_social') || '';
            history.replaceState(null, '', `dynamic_table.php?obra_social=${encodeURIComponent(obra)}`);
        });
    </script>
</body>

</html>