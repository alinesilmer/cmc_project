<?php
// index.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Colegio Médico de Corrientes</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto;
            color: var(--text);
        }

        .landing {
            text-align: center;
        }

        .enter-btn {
            margin-top: 1rem;
            padding: .75rem 1.5rem;
            font-size: 1rem;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: .5rem;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="landing">
        <h1>Colegio Médico de Corrientes</h1>
        <button class="enter-btn" onclick="location.href='pages/Navegacion/dashboard.php'">
            Ingresar al sistema
        </button>
    </div>
</body>

</html>