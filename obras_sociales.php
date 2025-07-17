<?php
session_start();
$obras = ['Sancor', 'MEDIFÉ', 'OSDE', 'IOMA', 'Swiss Medical', 'OSECAC'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Obras Sociales – Resumen</title>
    <style>
        :root {
            --bg: #f9f9fb;
            --card-bg: #fff;
            --card-border: #e0e0e6;
            --primary: #3066be;
            --text: #2d2d38;
            --danger: #e54f6d;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 200px;
            background: var(--primary);
            color: #fff;
            padding: 1rem;
        }

        .sidebar h2 {
            margin-top: 0;
            font-size: 1.25rem;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar nav li {
            margin: 0.75rem 0;
        }

        .sidebar nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
        }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
        }

        .content header {
            background: var(--primary);
            color: #fff;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        .search-bar {
            margin: 1rem 0;
            max-width: 420px;
        }

        .search-bar input {
            width: 100%;
            padding: 0.6rem 1rem;
            border: 1px solid #cfd4e2;
            border-radius: 0.65rem;
            font-size: 0.95rem;
        }

        .cards-container {
            flex: 1;
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-top: 1rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 0.75rem;
            padding: 1.2rem;
            text-align: center;
            cursor: pointer;
            transition: box-shadow 0.25s, transform 0.25s;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .card span {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: var(--primary);
        }

        /* Modal */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s;
        }

        .modal.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content {
            background: #fff;
            padding: 1.5rem;
            border-radius: 0.7rem;
            width: 100%;
            max-width: 320px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .modal-content h3 {
            margin-top: 0;
            color: var(--primary);
        }

        .modal-content input {
            width: 100%;
            margin: 0.5rem 0;
            padding: 0.55rem;
            border: 1px solid #ccc;
            border-radius: 0.4rem;
        }

        .modal-content button {
            width: 100%;
            margin-top: 1rem;
            padding: 0.6rem;
            border: none;
            border-radius: 0.45rem;
            background: var(--primary);
            color: #fff;
            cursor: pointer;
        }

        .error-msg {
            color: var(--danger);
            font-size: 0.85rem;
            height: 1.1rem;
            margin-top: 0.3rem;
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

    <section class="content">
        <header>
            <h1>Seleccione la Obra Social</h1>
        </header>

        <div class="search-bar">
            <input type="search" id="search" placeholder="Buscar obra social…">
        </div>

        <div class="cards-container">
            <?php foreach ($obras as $obra): ?>
                <div class="card" data-obra="<?= htmlspecialchars($obra, ENT_QUOTES) ?>">
                    <span><?= htmlspecialchars($obra, ENT_QUOTES) ?></span>
                    Cargar resumen
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <h3>Acceso</h3>
            <input type="text" id="user" placeholder="Usuario">
            <input type="password" id="pass" placeholder="Contraseña">
            <div class="error-msg" id="err"></div>
            <button id="loginBtn">Entrar</button>
        </div>
    </div>

    <script>
        // Filtrar obras
        const search = document.getElementById('search');
        const cards = [...document.querySelectorAll('.card')];
        search.addEventListener('input', e => {
            const q = e.target.value.toLowerCase().trim();
            cards.forEach(c => {
                c.style.display = c.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        // Control de modal
        const modal = document.getElementById('loginModal');
        const userInp = document.getElementById('user');
        const passInp = document.getElementById('pass');
        const errBox = document.getElementById('err');
        let targetObra = '';

        cards.forEach(card => {
            card.addEventListener('click', () => {
                targetObra = card.dataset.obra;
                userInp.value = '';
                passInp.value = '';
                errBox.textContent = '';
                modal.classList.add('active');
                userInp.focus();
            });
        });

        document.getElementById('loginBtn').addEventListener('click', () => {
            const u = userInp.value.trim();
            const p = passInp.value.trim();
            if (u === 'admin' && p === '1234') {
                window.location.href = 'dynamic_table.php?obra_social=' + encodeURIComponent(targetObra);
            } else {
                errBox.textContent = 'Credenciales inválidas';
            }
        });

        const closeModal = () => modal.classList.remove('active');
        window.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });
        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });
    </script>
</body>

</html>