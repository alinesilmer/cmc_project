<?php
session_start();

/* 1) Datos de liquidación guardados por dynamic_table.php */
$data = $_SESSION['liquidacion_data'] ?? [];
$obra = $_GET['obra_social'] ?? '';

/* 2) Agrupamos totales por médico y periodos únicos */
$summary  = [];
$periodos = [];

foreach ($data as $r) {
    $doc = $r['Doctor']    ?? '';
    $mat = $r['Matrícula'] ?? '';
    $f   = $r['Fecha']     ?? '';
    $d   = DateTime::createFromFormat('Y-m-d', $f);
    $ym  = $d ? $d->format('Y-m') : '';

    $sub = floatval(str_replace(',', '.', $r['Total'] ?? '0'));
    if (!isset($summary[$doc])) {
        $summary[$doc] = [
            'mat'       => $mat,
            'total'     => 0.0,
            'descuento' => 0.0,
            'deuda'     => 0.0,
            'periodos'  => [],
        ];
    }
    $summary[$doc]['total'] += $sub;
    if ($ym && !in_array($ym, $summary[$doc]['periodos'], true)) {
        $summary[$doc]['periodos'][] = $ym;
        $periodos[] = $ym;
    }
}
$periodos = array_unique($periodos);
sort($periodos);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Ver Resumen – <?= htmlspecialchars($obra) ?></title>
    <style>
        :root {
            --primary: #3066be;
            --primary-light: #e8efff;
            --bg: #f9f9fb;
            --surface: #fff;
            --text: #2d2d38;
            --accent: #f5b700;
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
            display: flex;
            min-height: 100vh;
        }

        nav {
            width: 200px;
            padding: 1rem;
            background: var(--primary);
            color: #fff;
        }

        nav a {
            display: block;
            color: #fff;
            text-decoration: none;
            margin: .5rem 0;
        }

        main {
            flex: 1;
            padding: 1rem;
        }

        header h1 {
            margin: 0 0 1rem;
            background: var(--primary);
            color: #fff;
            padding: 1rem;
            border-radius: .5rem;
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .controls input,
        .controls select {
            padding: .45rem .8rem;
            border: 1px solid #cfd4e2;
            border-radius: .5rem;
            font-size: .9rem;
            background: #fff;
        }

        .container {
            background: var(--surface);
            padding: 1rem;
            border-radius: .5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
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
            padding: .6rem .8rem;
            border: 1px solid #e0e0e6;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background: var(--primary-light);
        }

        tbody tr:hover {
            background: #f3f6ff;
        }

        .btn-discount,
        .btn-debt {
            border: none;
            padding: .4rem .8rem;
            border-radius: .4rem;
            cursor: pointer;
        }

        .btn-discount {
            background: var(--accent);
            color: #000;
            margin-right: .4rem;
        }

        .btn-debt {
            background: var(--danger);
            color: #fff;
        }
    </style>
</head>

<body>
    <nav>
        <h2>Sistema</h2>
        <a href="liquidacion.php">← Volver a Liquidación</a>
        <a href="/pages/Facturacion/dynamic_table.php?obra_social=<?= urlencode($obra) ?>">← Resumen Raw</a>
    </nav>

    <main>
        <header>
            <h1>Ver Resumen: <?= htmlspecialchars($obra) ?></h1>
        </header>

        <div class="controls">
            <input type="search" id="searchDoctor" placeholder="Buscar doctor…">
            <select id="filterPeriod">
                <option value="">Todos los periodos</option>
                <?php foreach ($periodos as $p):
                    $dt = DateTime::createFromFormat('Y-m', $p);
                    $lbl = $dt ? $dt->format('F Y') : $p;
                ?>
                    <option value="<?= $p ?>"><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="container">
            <table id="summaryTable">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Matrícula</th>
                        <th>Total (€)</th>
                        <th>Descuento (€)</th>
                        <th>Deuda (€)</th>
                        <th>Total a Liquidar (€)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($summary as $doc => $info):
                        $final = $info['total'] - $info['descuento'] - $info['deuda'];
                    ?>
                        <tr data-doc="<?= strtolower($doc) ?>" data-periods="<?= implode(',', $info['periodos']) ?>">
                            <td><?= htmlspecialchars($doc) ?></td>
                            <td><?= htmlspecialchars($info['mat']) ?></td>
                            <td class="cell-total"><?= number_format($info['total'], 2, ',', '.') ?></td>
                            <td class="cell-descuento"><?= number_format($info['descuento'], 2, ',', '.') ?></td>
                            <td class="cell-deuda"><?= number_format($info['deuda'], 2, ',', '.') ?></td>
                            <td class="cell-final"><?= number_format($final, 2, ',', '.') ?></td>
                            <td>
                                <a class="btn-discount"
                                    href="discount_apply.php?obra_social=<?= urlencode($obra) ?>&doctor=<?= urlencode($doc) ?>">
                                    Descuento
                                </a>
                                <button class="btn-debt">Deuda</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        /* Filtros dinámicos */
        const rows = [...document.querySelectorAll('#summaryTable tbody tr')];
        searchDoctor.oninput = e => {
            const term = e.target.value.toLowerCase().trim();
            rows.forEach(r => r.style.display = term === '' || r.dataset.doc.includes(term) ? '' : 'none');
        };
        filterPeriod.onchange = e => {
            const per = e.target.value;
            rows.forEach(r => r.style.display =
                per === '' || r.dataset.periods.split(',').includes(per) ? '' : 'none');
        };
    </script>
</body>

</html>