<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Liquidación – Colegio Médico</title>
    <style>
        :root {
            --bg: #f9f9fb;
            --card-bg: #fff;
            --card-border: #e0e0e6;
            --primary: #3066be;
            --text: #2d2d38;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            display: flex;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto;
            background: var(--bg);
            color: var(--text);
        }

        .sidebar {
            width: 200px;
            background: var(--primary);
            color: #fff;
            padding: 1rem;
            height: 100vh;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar nav li {
            margin: 1rem 0;
        }

        .sidebar nav a {
            color: #fff;
            text-decoration: none;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
        }

        .main-content h1 {
            margin-top: 0;
        }

        .cards-container {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: .75rem;
            padding: 1.2rem;
            text-align: center;
            cursor: pointer;
            transition: box-shadow .25s, transform .25s;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgb(0 0 0 / .08);
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <h2>Sistema</h2>
        <nav>
            <ul>
                <li><a href="obras_sociales.php">Obras Sociales</a></li>
                <li><a href="liquidacion.php">Liquidación</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <h1>Liquidación</h1>
        <div class="cards-container">
            <div class="card">Ver resumen</div>
            <div class="card">Pre-liquidar</div>
            <div class="card">Liquidar</div>
        </div>
    </main>
</body>

</html>