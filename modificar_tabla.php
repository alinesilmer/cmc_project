<?php
session_start();

function normalizeKey(string $label): string
{
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $label);
    $key   = preg_replace('/[^A-Za-z0-9]+/', '_', $ascii);
    return strtolower(trim($key, '_'));
}

// Eliminar columna
if (isset($_GET['delete_column'])) {
    $delLabel = $_GET['delete_column'];
    $idx = array_search($delLabel, $_SESSION['headers']);
    if ($idx !== false) {
        array_splice($_SESSION['headers'], $idx, 1);
        $key = normalizeKey($delLabel);
        unset($_SESSION['types'][$key]);
        foreach ($_SESSION['table_data'] as &$row) {
            unset($row[$key]);
        }
        unset($row);
    }
    header('Location: modificar_tabla.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // actualización de tipos
    if (!empty($_POST['types'])) {
        foreach ($_POST['types'] as $label => $t) {
            $key = normalizeKey($label);
            $_SESSION['types'][$key] = in_array($t, ['text', 'number', 'date']) ? $t : 'text';
        }
    }
    // agregar columna
    if (!empty($_POST['new_column'])) {
        $newLabel = trim($_POST['new_column']);
        if ($newLabel) {
            $_SESSION['headers'][] = $newLabel;
            $newKey = normalizeKey($newLabel);
            $_SESSION['types'][$newKey] = 'text';
        }
    }
    header('Location: modificar_tabla.php');
    exit;
}

$headers = $_SESSION['headers'];
$types   = $_SESSION['types'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Tabla</title>
    <style>
        :root {
            --primary: #3066be;
            --bg: #f0f0f0;
            --white: #fff;
            --text: #333;
            --danger: #e53935;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .wrapper {
            max-width: 700px;
            margin: 2rem auto;
            background: var(--white);
            padding: 1.5rem;
            border-radius: .5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: var(--primary);
            margin-top: 0;
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
            color: var(--white);
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
            color: var(--white);
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
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Modificar Tabla</h2>
        <h3>Columnas Actuales</h3>
        <ul class="column-list">
            <?php foreach ($headers as $col): ?>
                <li>
                    <?= htmlspecialchars($col) ?>
                    <button onclick="if(confirm('Eliminar columna <?= htmlspecialchars($col) ?>?')) location.href='modificar_tabla.php?delete_column=<?= urlencode($col) ?>'">Eliminar</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <button class="toggle-btn" id="toggleTypes">Configurar Tipos de Columnas</button>
        <div id="typesSection">
            <form method="POST">
                <?php foreach ($headers as $col): ?>
                    <label>
                        <?= htmlspecialchars($col) ?> tipo:
                        <select name="types[<?= htmlspecialchars($col) ?>]">
                            <option value="text" <?= $types[normalizeKey($col)] === 'text' ? ' selected' : '' ?>>Palabra</option>
                            <option value="number" <?= $types[normalizeKey($col)] === 'number' ? ' selected' : '' ?>>Número</option>
                            <option value="date" <?= $types[normalizeKey($col)] === 'date' ? ' selected' : '' ?>>Fecha</option>
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
                <input type="text" name="new_column" placeholder="Nombre de la nueva columna">
            </label>
            <button type="submit" class="add-btn">Agregar Columna</button>
        </form>
    </div>
    <script>
        document.getElementById('toggleTypes').addEventListener('click', () => {
            const sec = document.getElementById('typesSection');
            sec.style.display = sec.style.display === 'none' ? 'block' : 'none';
        });
    </script>
</body>

</html>