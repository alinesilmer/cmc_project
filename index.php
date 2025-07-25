<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Colegio Médico de Corrientes</title>

    <!-- Opcional: enlaza un CSS externo -->
    <!-- <link rel="stylesheet" href="/assets/css/welcome.css"> -->

    <style>
        :root {
            --primary: #3066be;
            --primary-dark: #255099;
            --bg-1: #eef2f7;
            --bg-2: #dfe8ff;
            --text: #2d2d38;
            --white: #fff;
            --glass: rgba(255, 255, 255, .55);
            --shadow: 0 20px 50px rgba(0, 0, 0, .12);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: var(--text);
            background: linear-gradient(135deg, var(--bg-1) 0%, var(--bg-2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Floating blobs */
        .blob {
            position: absolute;
            width: 38vmax;
            height: 38vmax;
            background: var(--primary);
            filter: blur(70px);
            opacity: .12;
            animation: float 18s ease-in-out infinite;
            z-index: 0;
        }

        .blob:nth-child(1) {
            top: -10%;
            left: -10%;
            animation-delay: 0s;
        }

        .blob:nth-child(2) {
            bottom: -15%;
            right: -5%;
            background: #7aa3ff;
            animation-delay: 3s;
        }

        .blob:nth-child(3) {
            top: 40%;
            left: 60%;
            background: #00c2ff;
            animation-delay: 6s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(5%, -6%) scale(1.1);
            }
        }

        /* Card */
        .welcome-card {
            position: relative;
            z-index: 1;
            width: min(420px, 90vw);
            padding: 3.2rem 2.6rem 2.6rem;
            border-radius: 22px;
            background: var(--glass);
            backdrop-filter: blur(16px) saturate(160%);
            -webkit-backdrop-filter: blur(16px) saturate(160%);
            box-shadow: var(--shadow);
            text-align: center;
            animation: fadeIn .6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(.98);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.2rem;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 2rem;
            font-weight: 600;
        }


        .welcome-card h1 {
            font-size: 1.65rem;
            font-weight: 700;
            margin-bottom: .6rem;
            color: var(--primary-dark);
        }

        .subtitle {
            font-size: .95rem;
            color: #5a5a66;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .enter-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            padding: .85rem 1.6rem;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background: var(--primary);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(48, 102, 190, .28);
            transition: transform .16s, box-shadow .16s, background .16s;
            text-decoration: none;
        }

        .enter-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(48, 102, 190, .34);
        }

        .enter-btn svg {
            width: 18px;
            height: 18px;
            fill: #fff;
            transition: transform .18s;
        }

        .enter-btn:hover svg {
            transform: translateX(3px);
        }

        .footer {
            margin-top: 2.2rem;
            font-size: .75rem;
            color: #8a8a98;
        }

        .footer a {
            color: inherit;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- blobs -->
    <span class="blob"></span>
    <span class="blob"></span>
    <span class="blob"></span>

    <div class="welcome-card">
        <div class="logo">
            <img src="./assets/images/logoCMC.png" alt="logo-cmc">
        </div>
        <h1>Colegio Médico de Corrientes</h1>
        <p class="subtitle">Portal interno para profesionales. Accede a tu tablero y gestiona tu información rápidamente.</p>

        <a href="/pages/Navegacion/dashboard/dashboard.php" class="enter-btn" aria-label="Ingresar al sistema">
            Ingresar
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M13 5l7 7-7 7M5 12h14" />
            </svg>
        </a>

        <div class="footer">© <?= date('Y') ?> CMC. Todos los derechos reservados.</div>
    </div>
</body>

</html>