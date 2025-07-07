<?php include 'sidebar.php'; ?>
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
            --primary-weak: #e8efff;
            --text: #2d2d38;
            --danger: #e54f6d;
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

        .main {
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
            cursor: pointer;
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

        /* Modal */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s;
        }

        .modal.active {
            opacity: 1;
            pointer-events: auto
        }

        .modal-content {
            background: #fff;
            padding: 1.5rem;
            border-radius: .7rem;
            width: 100%;
            max-width: 320px;
            text-align: center;
            box-shadow: 0 4px 16px rgb(0 0 0 / .12);
            position: relative;
        }

        .modal-content h3 {
            margin-top: 0;
            color: var(--primary);
        }

        .modal-content input {
            width: 100%;
            margin: .5rem 0;
            padding: .55rem;
            border: 1px solid #ccc;
            border-radius: .4rem;
        }

        .modal-content button {
            width: 100%;
            margin-top: 1rem;
            padding: .6rem;
            border: none;
            border-radius: .45rem;
            background: var(--primary);
            color: #fff;
            cursor: pointer;
        }

        .error-msg {
            color: var(--danger);
            font-size: .85rem;
            height: 1.1rem;
            margin-top: .3rem;
        }
    </style>
</head>

<body>
    <main>
        <header>
            <h1>Seleccione la Obra Social</h1>
        </header>
    
        <div class="search-bar"><input type="search" id="search" placeholder="Buscar obra social…"></div>
    
        <div class="main">
            <?php foreach ($obras as $obra): ?>
                <div class="card" data-obra="<?= htmlspecialchars($obra) ?>">
                    <span><?= htmlspecialchars($obra) ?></span>
                    Cargar resumen
                </div>
            <?php endforeach; ?>
        </div>
    
        <!-- Modal -->
        <div id="loginModal" class="modal">
            <div class="modal-content" id="modalBox">
                <h3>Acceso</h3>
                <input type="text" id="user" placeholder="Usuario">
                <input type="password" id="pass" placeholder="Contraseña">
                <div class="error-msg" id="err"></div>
                <button id="loginBtn">Entrar</button>
            </div>
        </div>
    </main>

    
    <script>
        /* Buscar */
        const search = document.getElementById('search');
        const cards = [...document.querySelectorAll('.card')];
        search.oninput = e => {
            const q = e.target.value.toLowerCase().trim();
            cards.forEach(c => c.style.display = c.textContent.toLowerCase().includes(q) ? '' : 'none');
        };

        /* Modal */
        const modal = document.getElementById('loginModal');
        const modalBox = document.getElementById('modalBox');
        const userInp = document.getElementById('user');
        const passInp = document.getElementById('pass');
        const errBox = document.getElementById('err');
        let targetObra = '';

        cards.forEach(card => {
            card.onclick = () => {
                targetObra = card.dataset.obra;
                userInp.value = passInp.value = '';
                errBox.textContent = '';
                modal.classList.add('active');
                userInp.focus();
            };
        });

        document.getElementById('loginBtn').onclick = () => {
            const u = userInp.value.trim(),
                p = passInp.value.trim();
            if (u === 'admin' && p === '1234') {
                window.location.href = 'dynamic_table.php?obra_social=' + encodeURIComponent(targetObra);
            } else {
                errBox.textContent = 'Credenciales inválidas';
            }
        };

        /* Cerrar modal */
        const closeModal = () => modal.classList.remove('active');
        window.onkeydown = e => {
            if (e.key === 'Escape') closeModal();
        }
        modal.onclick = e => {
            if (e.target === modal) closeModal();
        }
    </script>
</body>

</html>