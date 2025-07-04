<?php
session_start();

function normalizeKey(string $label): string
{
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $label);
    $key = preg_replace('/[^A-Za-z0-9]+/', '_', $ascii);
    return strtolower(trim($key, '_'));
}

$headers = $_SESSION['headers'];
$types = $_SESSION['types'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entry = [];
    foreach ($headers as $col) {
        $key = normalizeKey($col);
        $input = trim($_POST[$key] ?? '');
        if ($types[$key] === 'number' && $input !== '' && !is_numeric($input)) {
            $error = "El campo '$col' debe ser numérico.";
        }
        if ($types[$key] === 'date' && $input !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $input);
            if (!$d || $d->format('Y-m-d') !== $input) $error = "El campo '$col' debe tener formato YYYY-MM-DD.";
        }
        if (isset($error)) {
            header('Location: agregar_datos.php?error=' . urlencode($error));
            exit;
        }
        $entry[$key] = $input;
    }
    $_SESSION['table_data'][] = $entry;
    header('Location: dynamic_table.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Datos</title>
    <style>
        :root {
            --primary: #3066be;
            --bg: #f0f0f0;
            --white: #fff;
            --text: #f0f0f0;
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
            padding: 1.5rem;
            border-radius: .5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 1rem 0;
        }

        input,
        button {
            width: 100%;
            padding: .6rem;
            border: 1px solid #ccc;
            border-radius: .35rem;
            font-size: .9rem;
        }

        button {
            margin-top: 1rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            cursor: pointer;
        }

        button:hover {
            filter: brightness(1.1);
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="form-wrapper">
        <h2>Agregar Datos</h2>
        <?php if (isset($_GET['error'])): ?><p class="error"><?= htmlspecialchars($_GET['error']) ?></p><?php endif; ?>
        <form method="POST">
            <?php foreach ($headers as $col):
                $key = normalizeKey($col);
                $type = $types[$key] ?? 'text';
                $ph = $type === 'date' ? 'YYYY-MM-DD' : ($type === 'number' ? '1234' : 'Texto libre');
            ?>
                <label><?= htmlspecialchars($col) ?>:<input type="<?= $type ?>" name="<?= $key ?>" placeholder="<?= $ph ?>"></label>
            <?php endforeach; ?>
            <button type="submit">Guardar y Volver</button>
        </form>
    </div>
</body>

</html>