<?php
session_start();

/* ====== Datos iniciales (demo) ====== */
$periodo_actual = 'Período 2 · Julio 2025';

$registros = [
    ["codigo" => "A001", "medico" => "Pedro Espinoza", "mp" => "11111", "fecha" => "2025-07-11", "prestaciones" => 30, "monto_bruto" => 56000, "debitos" => 12000, "motivo" => "Mala Praxis", "monto_neto" => 44000],
    ["codigo" => "A002", "medico" => "Ana Gomez", "mp" => "11114", "fecha" => "2025-07-08", "prestaciones" => 20, "monto_bruto" => 40000, "debitos" => 9000, "motivo" => "Reintegro no cubierto", "monto_neto" => 31000],
];

/* helper */
function money($v)
{
    return $v === null ? '—' : '$' . number_format($v, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Carga de Débitos</title>

    <link href="../../../globals.css" rel="stylesheet" />
    <link href="../sidebar/sidebar.css" rel="stylesheet" />
    <link href="form_debitos.css" rel="stylesheet" />
</head>

<body>
    <div class="dashboard-container">
        <?php include '../sidebar/sidebar.php'; ?>

        <div class="main-content">

            <!-- Card período -->
            <div class="period-card">
                <span class="emoji">📅</span>
                <div class="period-info">
                    <h3><?= htmlspecialchars($periodo_actual) ?></h3>
                </div>
            </div>

            <!-- Tabla -->
            <section class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Débitos del Período</h2>
                    <input type="text" id="searchInput" class="filter-input" placeholder="Buscar...">
                </div>

                <div class="section-content active">
                    <div class="table-container">
                        <table id="tablaRegistros">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Médico</th>
                                    <th>M.P.</th>
                                    <th>Fecha</th>
                                    <th>Prestaciones</th>
                                    <th>Monto Bruto</th>
                                    <th>Débitos</th>
                                    <th>Motivo Débito</th>
                                    <th>Monto Neto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registros as $r): ?>
                                    <tr class="data-row">
                                        <td><?= htmlspecialchars($r['codigo']) ?></td>
                                        <td><?= htmlspecialchars($r['medico']) ?></td>
                                        <td><?= htmlspecialchars($r['mp']) ?></td>
                                        <td><?= htmlspecialchars($r['fecha']) ?></td>
                                        <td><?= (int)$r['prestaciones'] ?></td>
                                        <td><?= money($r['monto_bruto']) ?></td>
                                        <td><?= money($r['debitos']) ?></td>
                                        <td><?= htmlspecialchars($r['motivo']) ?></td>
                                        <td><?= money($r['monto_neto']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <button id="addRowBtn" class="btn-primary">+ Agregar registro</button>

            <!-- Formulario (sección oculta) -->
            <section id="formSection" class="section-card form-card hidden" aria-hidden="true">
                <div class="section-header">
                    <h2 class="section-title">Nuevo Registro</h2>
                    <button class="btn-icon" id="closeFormBtn" aria-label="Cerrar">✕</button>
                </div>

                <div class="section-content active">
                    <form id="registroForm" class="form-grid">
                        <div class="form-field">
                            <label for="codigo">Código</label>
                            <input type="text" id="codigo" name="codigo" required>
                        </div>

                        <div class="form-field">
                            <label for="medico">Médico</label>
                            <input type="text" id="medico" name="medico" required>
                        </div>

                        <div class="form-field">
                            <label for="mp">M.P.</label>
                            <input type="text" id="mp" name="mp" required>
                        </div>

                        <div class="form-field">
                            <label for="fecha">Fecha</label>
                            <input type="date" id="fecha" name="fecha" required>
                        </div>

                        <div class="form-field">
                            <label for="prestaciones">Prestaciones (cant.)</label>
                            <input type="number" id="prestaciones" name="prestaciones" min="0" required>
                        </div>

                        <div class="form-field">
                            <label for="monto_bruto">Monto Bruto</label>
                            <input type="number" id="monto_bruto" name="monto_bruto" min="0" step="0.01" required>
                        </div>

                        <div class="form-field">
                            <label for="debitos">Débitos</label>
                            <input type="number" id="debitos" name="debitos" min="0" step="0.01" required>
                        </div>

                        <div class="form-field">
                            <label for="motivo">Motivo del Débito</label>
                            <textarea id="motivo" name="motivo" rows="2" required></textarea>
                        </div>

                        <div class="form-field readonly">
                            <label for="monto_neto">Monto Neto</label>
                            <input type="text" id="monto_neto" name="monto_neto" readonly>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-secondary" id="cancelFormBtn">Cancelar</button>
                            <button type="submit" class="btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Toast -->
            <div id="toastContainer" class="toast-container"></div>

        </div><!-- /main-content -->
    </div><!-- /dashboard-container -->

    <script src="../../../utils/sidebarToggle.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initSidebarToggle();

            const addBtn = document.getElementById('addRowBtn');
            const formSec = document.getElementById('formSection');
            const closeFormBtn = document.getElementById('closeFormBtn');
            const cancelFormBtn = document.getElementById('cancelFormBtn');
            const form = document.getElementById('registroForm');

            const brutoInput = document.getElementById('monto_bruto');
            const debitosInput = document.getElementById('debitos');
            const netoInput = document.getElementById('monto_neto');

            const tableBody = document.querySelector('#tablaRegistros tbody');
            const searchInput = document.getElementById('searchInput');

            /* toast */
            const toastContainer = document.getElementById('toastContainer');

            function showToast(msg, type = 'success') {
                const t = document.createElement('div');
                t.className = `toast ${type}`;
                t.innerHTML = `<span class="icon">${type==='success'?'✔️':'⚠️'}</span><span>${msg}</span>`;
                toastContainer.appendChild(t);
                setTimeout(() => t.remove(), 4000);
            }

            function toggleForm(show) {
                formSec.classList.toggle('hidden', !show);
                formSec.setAttribute('aria-hidden', !show);
                document.body.style.overflow = show ? 'hidden' : '';
                if (show) {
                    form.reset();
                    calcNeto();
                    document.getElementById('codigo').focus();
                }
            }

            function moneyJS(num) {
                return '$' + Number(num).toLocaleString('es-AR', {
                    minimumFractionDigits: 0
                });
            }

            function calcNeto() {
                const b = parseFloat(brutoInput.value) || 0;
                const d = parseFloat(debitosInput.value) || 0;
                const n = b - d;
                netoInput.value = moneyJS(n);
            }

            brutoInput.addEventListener('input', calcNeto);
            debitosInput.addEventListener('input', calcNeto);

            addBtn.addEventListener('click', () => toggleForm(true));
            closeFormBtn.addEventListener('click', () => toggleForm(false));
            cancelFormBtn.addEventListener('click', () => toggleForm(false));

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                // obtengo datos
                const data = Object.fromEntries(new FormData(form));
                const bruto = parseFloat(data.monto_bruto) || 0;
                const deb = parseFloat(data.debitos) || 0;
                const neto = bruto - deb;

                // agrega fila (front)
                const tr = document.createElement('tr');
                tr.className = 'data-row';
                tr.innerHTML = `
      <td>${data.codigo}</td>
      <td>${data.medico}</td>
      <td>${data.mp}</td>
      <td>${data.fecha}</td>
      <td>${data.prestaciones}</td>
      <td>${moneyJS(bruto)}</td>
      <td>${moneyJS(deb)}</td>
      <td>${data.motivo}</td>
      <td>${moneyJS(neto)}</td>
    `;
                tableBody.appendChild(tr);

                toggleForm(false);
                showToast('Registro agregado correctamente');

                // TODO: enviar al servidor con fetch/AJAX
                // fetch('guardar_registro.php',{method:'POST',body:new FormData(form)});
            });

            // Filtro
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                document.querySelectorAll('#tablaRegistros tbody tr').forEach(row => {
                    const txt = row.innerText.toLowerCase();
                    row.style.display = txt.includes(term) ? '' : 'none';
                });
            });
        });
    </script>

</body>

</html>