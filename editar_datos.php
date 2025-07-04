<?php
session_start();

function normalizeKey(string $label): string
{
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $label);
    $key   = preg_replace('/[^A-Za-z0-9]+/', '_', $ascii);
    return strtolower(trim($key, '_'));
}

$headers = &$_SESSION['headers'];
$types   = &$_SESSION['types'];
$index   = isset($_GET['index']) ? (int)$_GET['index'] : null;

if ($index === null || !isset($_SESSION['table_data'][$index])) {
    header('Location: dynamic_table.php');
    exit;
}

if (isset($_GET['clear'])) {
    foreach ($headers as $col) {
        $key = normalizeKey($col);
        $_SESSION['table_data'][$index][$key] = '';
    }
    header('Location: editar_datos.php?index=' . $index);
    exit;
}

// Enviar formulario
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Datos</title>
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

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .form-wrapper {
            max-width: 700px;
            margin: 2rem auto;
            background: var(--white);
            padding: 2rem;
            border-radius: .5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
            color: var(--primary);
        }

        label {
            display: block;
            margin: 1rem 0;
            font-weight: bold;
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
            color: var(--white);
        }

        .btn-save:hover {
            filter: brightness(1.1);
        }

        .btn-clear {
            background: #ffa000;
            color: var(--white);
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
                $key = normalizeKey($col);
                $typeAttr = isset($types[$key]) && $types[$key] === 'number'
                    ? 'number' : (isset($types[$key]) && $types[$key] === 'date' ? 'date' : 'text');
                $value = htmlspecialchars($row[$key] ?? '');
            ?>
                <label for="<?= $key ?>"><?= htmlspecialchars($col) ?>:</label>
                <input id="<?= $key ?>" type="<?= $typeAttr ?>" name="<?= $key ?>" value="<?= $value ?>" placeholder="<?= $typeAttr === 'date' ? 'YYYY-MM-DD' : '' ?>" />
            <?php endforeach; ?>
            <div class="actions">
                <button type="submit" class="btn btn-save">Guardar Cambios</button>
                <button type="button" class="btn btn-clear" onclick="if(confirm('Limpiar esta fila?')) location.href='editar_datos.php?index=<?= $index ?>&clear=1'">Limpiar Fila</button>
            </div>
        </form>
    </div>
</body>

</html>