<?php

session_start();

$obras = [
    'Sancor',
    'PAMI',
    'OSDE',
    'IOMA',
    'Swiss Medical',
    'OSECAC'
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Obras Sociales – Resumen</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <style>
        :root {
            --bg: #f9f9fb;
            --card-bg: #ffffff;
            --card-border: #e0e0e6;
            --primary: #3066be;
            --primary-weak: #e8efff;
            --text: #2d2d38;
        }

        * {
            box-sizing: border-box
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

        .search-bar {
            max-width: 420px;
            margin: 1.5rem auto 0;
        }

        .search-bar input {
            width: 100%;
            padding: .6rem 1rem;
            border: 1px solid #cfd4e2;
            border-radius: .65rem;
            font-size: .95rem;
        }


        main {
            padding: 2rem 1rem;
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            max-width: 1100px;
            margin: auto;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: .75rem;
            padding: 1.2rem;
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: box-shadow .25s, transform .25s;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgb(0 0 0 / .08);
            transform: translateY(-2px);
        }

        .card span {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: .3rem;
            color: var(--primary);
        }
    </style>
</head>

<body>
    <header>
        <h1>Seleccione la Obra Social</h1>
    </header>

    <!--barra de búsqueda por Obra Social-->
    <div class="search-bar">
        <input type="search" id="search" placeholder="Buscar obra social…">
    </div>

    <!--Cartas de Obra Social-->
    <main>
        <?php foreach ($obras as $obra): ?>
            <a class="card" href="dynamic_table.php?obra_social=<?= urlencode($obra) ?>">
                <span><?= htmlspecialchars($obra) ?></span>
                Cargar resumen
            </a>
        <?php endforeach; ?>
    </main>

    <!--lógica de filtros por Obra Social-->
    <script>
        const search = document.getElementById('search');
        const cards = Array.from(document.querySelectorAll('.card'));
        search.addEventListener('input', e => {
            const q = e.target.value.toLowerCase().trim();
            cards.forEach(c => {
                c.style.display = c.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>

</body>

</html>