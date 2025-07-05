<?php
session_start();

/* 1)  Cargamos utils.php (misma carpeta) */
$utils = __DIR__ . '/utils.php';
if (is_file($utils)) {
    /** @noinspection PhpIncludeInspection */
    require_once $utils;
}
/* 1-bis) Fallback si utils.php no estuviera */
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

/* 2)  Inicializamos estructuras de sesión si faltan */
$_SESSION['headers'] = $_SESSION['headers'] ?? [];
$_SESSION['types']   = $_SESSION['types']   ?? [];
$_SESSION['table_data'] = $_SESSION['table_data'] ?? [];

/* ---------- Eliminar columna -------------------------------- */
if (isset($_GET['delete_column'])) {
    $delLabel = $_GET['delete_column'];
    $idx = array_search($delLabel, $_SESSION['headers'], true);
    if ($idx !== false) {
        array_splice($_SESSION['headers'], $idx, 1);
        $key = normalizeKey($delLabel);
        unset($_SESSION['types'][$key]);
        foreach ($_SESSION['table_data'] as &$fila) {
            unset($fila[$key]);
        }
        unset($fila);
    }
    header('Location: modificar_tabla.php');
    exit;
}

/* ---------- Procesar formulario POST ------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* 2-a) actualizar tipos */
    if (!empty($_POST['types'])) {
        foreach ($_POST['types'] as $label => $t) {
            $key = normalizeKey($label);
            $_SESSION['types'][$key] = in_array($t, ['text', 'number', 'date'], true) ? $t : 'text';
        }
    }

    /* 2-b) agregar columna */
    if (!empty($_POST['new_column'])) {
        $newLabel = trim($_POST['new_column']);
        if ($newLabel !== '' && !in_array($newLabel, $_SESSION['headers'], true)) {
            $_SESSION['headers'][] = $newLabel;
            $_SESSION['types'][normalizeKey($newLabel)] = 'text';
        }
    }

    header('Location: modificar_tabla.php');
    exit;
}

/* ---------- Datos para la vista ----------------------------- */
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
            --danger: #e53935;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .wrapper {
            max-width: 700px;
            margin: 2rem auto;
            background: var(--surface);
            padding: 1.5rem;
            border-radius: .5rem;
            box-shadow: 0 2px 8px rgb(0 0 0 / .1);
        }

        h2 {
            margin: 0 0 1rem;
            color: var(--primary);
        }

        .column-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .column-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .5rem 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .column-list button {
            background: var(--danger);
            border: none;
            color: #fff;
            padding: .3rem .6rem;
            border-radius: .3rem;
            cursor: pointer;
            transition: filter .3s;
        }

        .column-list button:hover {
            filter: brightness(1.1);
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
            margin-top: 1rem;
        }

        .toggle-btn:hover,
        .add-btn:hover {
            filter: brightness(1.1);
        }

        #typesSection {
            display: none;
            margin-top: 1rem;
        }

        label,
        select,
        input {
            display: block;
            width: 100%;
            margin-top: .5rem;
        }

        select,
        input {
            padding: .5rem;
            border: 1px solid #ccc;
            border-radius: .3rem;
        }

        hr {
            margin: 2rem 0;
            border: none;
            border-top: 1px solid #e0e0e0;
        }

        .secondary-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: var(--primary);
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <a href="obras_sociales.php" class="secondary-link">← Volver al listado</a>
        <h2>Modificar Tabla</h2>

        <h3>Columnas Actuales</h3>
        <ul class="column-list">
            <?php foreach ($headers as $col): ?>
                <li>
                    <?= htmlspecialchars($col) ?>
                    <button onclick="if(confirm('Eliminar columna <?= htmlspecialchars($col) ?>?')) location.href='modificar_tabla.php?delete_column=<?= urlencode($col) ?>'">
                        Eliminar
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <button class="toggle-btn" id="toggleTypes">Configurar Tipos de Columnas</button>

        <div id="typesSection">
            <form method="POST">
                <?php foreach ($headers as $col):
                    $key = normalizeKey($col);
                    $tipo = $types[$key] ?? 'text';
                ?>
                    <label>
                        <?= htmlspecialchars($col) ?> tipo:
                        <select name="types[<?= htmlspecialchars($col) ?>]">
                            <option value="text" <?= $tipo === 'text'   ? 'selected' : '' ?>>Palabra</option>
                            <option value="number" <?= $tipo === 'number' ? 'selected' : '' ?>>Número</option>
                            <option value="date" <?= $tipo === 'date'   ? 'selected' : '' ?>>Fecha</option>
                        </select>
                    </label>
                <?php endforeach; ?>
                <button type="submit" class="toggle-btn">Guardar Tipos</button>
            </form>
        </div>

        <hr>
        <h3>Agregar Nueva Columna</h3>
        <form method="POST">
            <label>Nombre columna:
                <input type="text" name="new_column" placeholder="Nombre de la nueva columna" required>
            </label>
            <button type="submit" class="add-btn">Agregar Columna</button>
        </form>
    </div>

    <script>
        document.getElementById('toggleTypes').onclick = () => {
            const sec = document.getElementById('typesSection');
            sec.style.display = (sec.style.display === 'none' || sec.style.display === '') ? 'block' : 'none';
        };
    </script>
</body>

</html>