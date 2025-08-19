<?php
session_start();

/* ---------- DATA DEMO ---------- */
if (!isset($_SESSION['contadora_amounts'])) {
    $_SESSION['contadora_amounts'] = [
        "Período 1" => "150000",
        "Período 2" => "180000",
        "Período 3" => "165000",
        "Período 4" => "190000",
    ];
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['period'])) {
    $p   = $_POST['period'];
    $raw = preg_replace('/[^0-9.]/', '', $_POST['amount'] ?? '');
    if ($raw !== '' && isset($_SESSION['contadora_amounts'][$p])) {
        $_SESSION['contadora_amounts'][$p] = $raw;
        $msg = 'Monto actualizado';
    }
}
$amounts = $_SESSION['contadora_amounts'];

/* helper */
function money($v)
{
    return '$' . number_format((float)$v, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contadora</title>

    <link href="../../../globals.css" rel="stylesheet" />
    <link href="../sidebar/sidebar.css" rel="stylesheet" />
    <link href="./contadora.css" rel="stylesheet" />
</head>

<body>
    <div class="dashboard-container">
        <?php include '../sidebar/sidebar.php'; ?>

        <!-- ---------- MAIN ---------- -->
        <main class="main-content">
            <!-- selector + resumen -->
            <div class="period-switcher">
                <select id="periodSelect">
                    <?php foreach ($amounts as $period => $val): ?>
                        <option value="<?= htmlspecialchars($period, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($period, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div id="cardResumen" class="resumen-card">
                    <!-- se llena por JS -->
                </div>
            </div>
        </main>
    </div>

    <!-- ---------- MODAL ---------- -->
    <div id="editModal" class="modal" hidden aria-hidden="true">
        <div class="modal-backdrop"></div>
        <div class="modal-window" role="dialog" aria-modal="true">
            <button class="modal-close" aria-label="Cerrar">×</button>
            <h3 class="modal-title">Editar monto</h3>
            <p id="modalPeriodo" class="modal-subtitle"></p>

            <form id="editForm" method="POST" autocomplete="off">
                <input type="hidden" name="period" id="hiddenPeriod">
                <label for="amount" class="modal-label">Nuevo monto ($)</label>
                <input type="number" id="amount" name="amount" min="0" step="1" class="modal-input" required>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelBtn">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- toast -->
    <div id="toastContainer" class="toast-container"></div>

    <script src="../../../utils/sidebarToggle.js"></script>
    <script>
        /* ---------- DATA desde PHP ---------- */
        const DATA = <?= json_encode($amounts, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK) ?>;

        /* ---------- Utils ---------- */
        const $ = s => document.querySelector(s);

        function money(n) {
            return '$' + Number(n).toLocaleString('es-AR');
        }

        function toast(msg, ok = true) {
            const t = document.createElement('div');
            t.className = 'toast ' + (ok ? 'success' : 'error');
            t.innerHTML = `<span class="icon">${ok?'✔️':'⚠️'}</span><span>${msg}</span>`;
            const cont = $('#toastContainer');
            if (cont) {
                cont.appendChild(t);
                setTimeout(() => t.remove(), 4000);
            }
        }

        /* ---------- Init ---------- */
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initSidebarToggle === 'function') initSidebarToggle();

            const sel = $('#periodSelect');
            const card = $('#cardResumen');
            const modal = $('#editModal');
            const amountIn = $('#amount');
            const hiddenP = $('#hiddenPeriod');
            const subtitle = $('#modalPeriodo');

            if (!sel || !card) return;

            function renderCard(p) {
                const val = DATA[p] ?? 0;
                card.innerHTML = `
      <h2>${p}</h2>
      <p class="big-amount">${money(val)}</p>
      <button class="btn-primary" id="editBtn">Editar</button>
    `;
                $('#editBtn').onclick = () => openModal(p);
            }

            function openModal(period) {
                if (!modal) return;
                modal.hidden = false;
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                subtitle.textContent = period;
                hiddenP.value = period;
                amountIn.value = DATA[period] ?? 0;
                amountIn.select();
            }

            function closeModal() {
                if (!modal) return;
                modal.hidden = true;
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            sel.addEventListener('change', e => renderCard(e.target.value));
            renderCard(sel.value);

            // modal basic
            const backdrop = modal.querySelector('.modal-backdrop');
            const closeBtn = modal.querySelector('.modal-close');
            const cancel = $('#cancelBtn');

            backdrop && (backdrop.onclick = closeModal);
            closeBtn && (closeBtn.onclick = closeModal);
            cancel && (cancel.onclick = closeModal);

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape' && !modal.hidden) closeModal();
            });

            // post-submit toast (viene de PHP)
            <?php if ($msg): ?>
                toast('<?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>');
            <?php endif; ?>
        });
    </script>
</body>

</html>