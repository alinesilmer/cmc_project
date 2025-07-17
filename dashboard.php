<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard – Colegio Médico</title>
    <style>
        :root {
            --bg: #f9f9fb;
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

        .sidebar h2 {
            margin-top: 0;
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
            font-weight: 500;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
        }

        .main-content h1 {
            margin-top: 0;
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
        <h1>Bienvenido al sistema</h1>
        <p>Seleccione una opción del menú para comenzar.</p>
    </main>
</body>

</html>