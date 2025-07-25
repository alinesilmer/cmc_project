<?php
session_start();
$obras  = ['Sancor', 'MEDIFÉ', 'OSDE', 'IOMA', 'Swiss Medical', 'OSECAC'];
$logos  = [
    'Sancor' => 'sancorLogo.png'
];

$logoDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/';
?>

<?php
function obra_slug(string $nombre): string
{
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $nombre);
    $slug = preg_replace('/[^A-Za-z0-9]+/', '-', $slug);
    return strtolower(trim($slug, '-'));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Obras Sociales – Resumen</title>
    <link href="../../../globals.css" rel="stylesheet">
    <link href="../sidebar/sidebar.css" rel="stylesheet">
    <link href="./obras_sociales.css" rel="stylesheet">
</head>

<body>
    <div class="layout">
        <?php include '../sidebar/sidebar.php'; ?>
        <div class="main-content">
            <header class="content-header">
                <h1>Seleccione la Obra Social</h1>
            </header>
            <div class="search-bar">
                <input type="search" id="search" placeholder="Buscar obra social…">
            </div>
            <div class="cards-grid">
                <?php foreach ($obras as $obra): ?>
                    <div class="card" data-obra="<?= htmlspecialchars($obra) ?>">
                        <span class="obra-name"><?= htmlspecialchars($obra) ?></span>

                        <?php
                        $file = $logos[$obra] ?? obra_slug($obra) . '.png';
                        $abs  = $logoDir . $file;
                        $url  = '/assets/images/' . $file;
                        ?>

                        <?php if (is_file($abs)): ?>
                            <img src="<?= $url ?>" alt="Logo <?= htmlspecialchars($obra) ?>">
                        <?php else: ?>
                            <img src="" alt="Logo <?= htmlspecialchars($obra) ?>"
                                onerror="this.remove()">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
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
                window.location.href = '../form_debitos/form_debitos.php';
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
<script src="../../../utils/sidebarToggle.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initSidebarToggle();
    });
</script>

</html>