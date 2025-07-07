<?php include 'sidebar.php'; ?>
<?php
session_start();

/* ---------- utilidades ---------- */
require_once __DIR__ . '/utils.php';
if (!function_exists('normalizeKey')) {
    function normalizeKey(string $label): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $label);
        $key   = preg_replace('/[^A-Za-z0-9]+/', '_', $ascii);
        $key   = strtolower(trim($key, '_'));
        return $key === '' ? ($label === '%' ? 'porcentaje' : 'col_' . dechex(crc32($label))) : $key;
    }
}

/* ---------- sesión por defecto ---------- */
$_SESSION['headers']    = $_SESSION['headers']    ?? [];
$_SESSION['types']      = $_SESSION['types']      ?? [];
$_SESSION['table_data'] = $_SESSION['table_data'] ?? [];
$_SESSION['dirty']      = $_SESSION['dirty']      ?? false;
$_SESSION['approved']   = $_SESSION['approved']   ?? false;

/* ---------- eliminar columna ---------- */
if (isset($_GET['delete_column'])) {
    $label = $_GET['delete_column'];
    if (($i = array_search($label, $_SESSION['headers'], true)) !== false) {
        array_splice($_SESSION['headers'], $i, 1);
        unset($_SESSION['types'][normalizeKey($label)]);
        foreach ($_SESSION['table_data'] as &$fila) unset($fila[normalizeKey($label)]);
        $_SESSION['dirty'] = true;                     // ← marca cambios
        $_SESSION['approved'] = false;
    }
    header('Location: modificar_tabla.php');
    exit;
}

/* ---------- procesar POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* 1-a  actualizar tipos */
    if (!empty($_POST['types'])) {
        foreach ($_POST['types'] as $label => $t) {
            $_SESSION['types'][normalizeKey($label)] =
                in_array($t, ['text', 'number', 'date'], true) ? $t : 'text';
        }
        $_SESSION['dirty'] = true;
        $_SESSION['approved'] = false;
    }

    /* 1-b  nueva columna */
    if (!empty($_POST['new_column'])) {
        $label = trim($_POST['new_column']);
        if ($label !== '' && !in_array($label, $_SESSION['headers'], true)) {
            $_SESSION['headers'][] = $label;
            $_SESSION['types'][normalizeKey($label)] = 'text';
            $_SESSION['dirty'] = true;
            $_SESSION['approved'] = false;
        }
    }

    header('Location: modificar_tabla.php');
    exit;
}

/* ---------- datos para vista ---------- */
$headers = $_SESSION['headers'];
$types   = $_SESSION['types'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Modificar Tabla</title>
    <style>
        :root {
            --primary: #3066be;
            --bg: #f0f0f0;
            --surface: #fff;
            --text: #2d2d38;
            --danger: #e53935
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--bg);
            color: var(--text)
        }

        .wrapper {
            max-width: 700px;
            margin: 2rem auto;
            background: var(--surface);
            padding: 1.5rem;
            border-radius: .5rem;
            box-shadow: 0 2px 8px rgb(0 0 0/.1)
        }

        h2 {
            margin: 0 0 1rem;
            color: var(--primary)
        }

        .column-list {
            list-style: none;
            padding: 0;
            margin: 0
        }

        .column-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .5rem 0;
            border-bottom: 1px solid #e0e0e0
        }

        .column-list button {
            background: var(--danger);
            border: none;
            color: #fff;
            padding: .3rem .6rem;
            border-radius: .3rem;
            cursor: pointer;
            transition: filter .3s
        }

        .column-list button:hover {
            filter: brightness(1.1)
        }

        .toggle-btn,
        .add-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: .6rem 1.2rem;
            border-radius: .35rem;
            cursor: pointer;
            transition: filter .3s;
            margin-top: 1rem
        }

        .toggle-btn:hover,
        .add-btn:hover {
            filter: brightness(1.1)
        }

        #typesSection {
            display: none;
            margin-top: 1rem
        }

        label,
        select,
        input {
            display: block;
            width: 100%;
            margin-top: .5rem
        }

        select,
        input {
            padding: .5rem;
            border: 1px solid #ccc;
            border-radius: .3rem
        }

        hr {
            margin: 2rem 0;
            border: none;
            border-top: 1px solid #e0e0e0
        }

        .secondary-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: var(--primary)
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <a href="dynamic_table.php" class="secondary-link">← Volver a la tabla</a>
        <h2>Modificar Tabla</h2>

        <h3>Columnas Actuales</h3>
        <ul class="column-list">
            <?php foreach ($headers as $col): ?>
                <li>
                    <?= htmlspecialchars($col) ?>
                    <button onclick="if(confirm('Eliminar columna <?= htmlspecialchars($col) ?>?'))location.href='modificar_tabla.php?delete_column=<?= urlencode($col) ?>'">
                        Eliminar
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <button class="toggle-btn" id="toggleTypes">Configurar Tipos de Columnas</button>
        <div id="typesSection">
            <form method="POST">
                <?php foreach ($headers as $col):
                    $tipo = $types[normalizeKey($col)] ?? 'text'; ?>
                    <label>
                        <?= htmlspecialchars($col) ?> tipo:
                        <select name="types[<?= htmlspecialchars($col) ?>]">
                            <option value="text" <?= $tipo === 'text' ? 'selected' : '' ?>>Palabra</option>
                            <option value="number" <?= $tipo === 'number' ? 'selected' : '' ?>>Número</option>
                            <option value="date" <?= $tipo === 'date' ? 'selected' : '' ?>>Fecha</option>
                        </select>
                    </label>
                <?php endforeach; ?>
                <button class="toggle-btn" type="submit">Guardar Tipos</button>
            </form>
        </div>

        <hr>
        <h3>Agregar Nueva Columna</h3>
        <form method="POST">
            <label>Nombre columna:<input name="new_column" required></label>
            <button class="add-btn" type="submit">Agregar Columna</button>
        </form>
    </div>

    <script>
        document.getElementById('toggleTypes').onclick = () => {
            const s = document.getElementById('typesSection');
            s.style.display = s.style.display === 'block' ? 'none' : 'block';
        };
    </script>
</body>

</html>