<?php
session_start();

$obra   = $_GET['obra_social'] ?? '';
$doctor = $_GET['doctor']      ?? '';

$tipos  = ['Administrativo', 'Inasistencia', 'Porcentual', 'Fijo', 'Otro'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['discount_type'];
    $val  = floatval(str_replace(',', '.', $_POST['discount_value']));

    // Guardamos descuento en la sesión:
    foreach ($_SESSION['liquidacion_data'] as &$r) {
        if ($r['Doctor'] === $doctor) {
            $r['DiscountType']  = $tipo;
            $r['DiscountValue'] = $val;
            // reflejamos inmediatamente en la tabla:
            $r['Total'] = max(0, ($r['Total'] ?? 0) - $val);
        }
    }
    unset($r);

    header('Location: ver_resumen.php?obra_social=' . urlencode($obra));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Descuento – <?= htmlspecialchars($doctor) ?></title>
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto;
            background: #f9f9fb;
            color: #2d2d38;
        }

        .form-wrapper {
            max-width: 400px;
            margin: 4rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: .5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
        }

        h2 {
            margin-top: 0;
            color: #3066be;
        }

        label {
            display: block;
            margin: 1rem 0;
        }

        select,
        input {
            width: 100%;
            padding: .6rem;
            border: 1px solid #ccc;
            border-radius: .4rem;
        }

        .actions {
            margin-top: 1.5rem;
            display: flex;
            gap: 1rem;
        }

        .btn {
            flex: 1;
            padding: .7rem;
            border: none;
            border-radius: .4rem;
            cursor: pointer;
        }

        .btn-save {
            background: #3066be;
            color: #fff;
        }

        .btn-cancel {
            background: #e0e0e6;
            color: #2d2d38;
            text-align: center;
            line-height: 2.4rem;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="form-wrapper">
        <h2>Descuento para <?= htmlspecialchars($doctor) ?></h2>
        <form method="POST">
            <label>Tipo de descuento:
                <select name="discount_type">
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= $t ?>"><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Monto / porcentaje:
                <input type="text" name="discount_value" placeholder="0.00" required>
            </label>

            <div class="actions">
                <button class="btn btn-save" type="submit">Guardar</button>
                <a class="btn btn-cancel"
                    href="ver_resumen.php?obra_social=<?= urlencode($obra) ?>">Cancelar</a>
            </div>
        </form>
    </div>
</body>

</html>