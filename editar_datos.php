<?php include 'sidebar.php'; ?>
<?php
session_start();

/* 1) Incluimos utils.php con ruta absoluta */
$utils = __DIR__ . '/utils.php';
if (is_file($utils)) {
    /** @noinspection PhpIncludeInspection */
    require_once $utils;
}
/* 1-bis) Fallback si utils.php no existe */
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

/* 2) Datos de sesión */
$headers = $_SESSION['headers'] ?? [];
$types   = $_SESSION['types']   ?? [];
$index   = isset($_GET['index']) ? (int)$_GET['index'] : null;

if ($index === null || !isset($_SESSION['table_data'][$index])) {
    header('Location: dynamic_table.php');
    exit;
}

/* 3) Limpiar fila */
if (isset($_GET['clear'])) {
    foreach ($headers as $col) {
        $_SESSION['table_data'][$index][normalizeKey($col)] = '';
    }
    header('Location: editar_datos.php?index=' . $index);
    exit;
}

/* 4) Guardar cambios */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($headers as $col) {
        $key = normalizeKey($col);
        $_SESSION['table_data'][$index][$key] = trim($_POST[$key] ?? '');
    }
    header('Location: dynamic_table.php');
    exit;
}

$row = $_SESSION['table_data'][$index];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Editar Datos</title>
    <style>
        :root {
            --primary: #3066be;
            --primary-light: #e8efff;
            --accent: #f5b700;
            --bg: #f9f9fb;
            --surface: #ffffff;
            --text: #2d2d38;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .form-wrapper {
            max-width: 700px;
            margin: 2rem auto;
            background: var(--surface);
            padding: 2rem;
            border-radius: .5rem;
            box-shadow: 0 2px 8px rgb(0 0 0 / .1);
        }

        h2 {
            margin: 0 0 1rem;
            color: var(--primary);
        }

        label {
            display: block;
            margin: 1rem 0;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: .6rem;
            border: 1px solid #ccc;
            border-radius: .35rem;
            font-size: .95rem;
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn {
            padding: .7rem 1.2rem;
            border: none;
            border-radius: .35rem;
            font-size: .95rem;
            cursor: pointer;
            transition: filter .3s;
        }

        .btn-save {
            background: var(--primary);
            color: #fff;
        }

        .btn-save:hover {
            filter: brightness(1.1);
        }

        .btn-clear {
            background: #ffa000;
            color: #fff;
        }

        .btn-clear:hover {
            filter: brightness(1.1);
        }
    </style>
</head>

<body>
    <div class="form-wrapper">
        <h2>Editar Datos</h2>

        <form method="POST" action="editar_datos.php?index=<?= $index ?>">
            <?php foreach ($headers as $col):
                $key   = normalizeKey($col);
                $type  = $types[$key] ?? 'text';
                $htmlT = $type === 'number' ? 'number' : ($type === 'date' ? 'date' : 'text');
                $value = htmlspecialchars($row[$key] ?? '');
            ?>
                <label for="<?= $key ?>"><?= htmlspecialchars($col) ?>:
                    <input id="<?= $key ?>" name="<?= $key ?>" type="<?= $htmlT ?>" value="<?= $value ?>"
                        placeholder="<?= $htmlT === 'date' ? 'YYYY-MM-DD' : '' ?>">
                </label>
            <?php endforeach; ?>

            <div class="actions">
                <button type="submit" class="btn btn-save">Guardar Cambios</button>
                <button type="button" class="btn btn-clear"
                    onclick="if(confirm('Limpiar esta fila?')) location.href='editar_datos.php?index=<?= $index ?>&clear=1'">
                    Limpiar Fila
                </button>
            </div>
        </form>
    </div>
</body>

</html>