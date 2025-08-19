<?php
session_start();

// DATA
$data_sections_os = [
    [
        "company" => "Sancor",
        "id"      => "sancor-card",
        "rows"    => [
            ["mp" => "11111", "medico" => "Pedro Espinoza", "prestaciones" => "30", "fecha" => "11/07/2025", "debito" => "$12.000", "total_bruto" => "$56.000", "total_a_pagar" => "$56.000", "reason" => "Mala Praxis por error de diagnóstico."],
            ["mp" => "11112", "medico" => "Maria Lopez",   "prestaciones" => "25", "fecha" => "10/07/2025", "debito" => "$10.000", "total_bruto" => "$45.000", "total_a_pagar" => "$45.000", "reason" => "Facturación duplicada en el sistema."],
            ["mp" => "11113", "medico" => "Juan Perez",    "prestaciones" => "40", "fecha" => "09/07/2025", "debito" => "$15.000", "total_bruto" => "$70.000", "total_a_pagar" => "$70.000", "reason" => "Servicio no autorizado por la Obra Social."],
            ["mp" => "11114", "medico" => "Ana Gomez",     "prestaciones" => "20", "fecha" => "08/07/2025", "debito" => "$9.000", "total_bruto" => "$40.000", "total_a_pagar" => "$40.000", "reason" => "Reintegro por medicación no cubierta."],
        ]
    ],
    [
        "company" => "Medifé",
        "id"      => "medife-card",
        "rows"    => [
            ["mp" => "22221", "medico" => "Carlos Ruiz",      "prestaciones" => "18", "fecha" => "12/07/2025", "debito" => "$7.500", "total_bruto" => "$32.000", "total_a_pagar" => "$32.000", "reason" => "Error en la carga de datos del paciente."],
            ["mp" => "22222", "medico" => "Laura Fernandez",  "prestaciones" => "35", "fecha" => "11/07/2025", "debito" => "$13.000", "total_bruto" => "$60.000", "total_a_pagar" => "$60.000", "reason" => "Prestación fuera de convenio."],
        ]
    ]
];

$data_sections_col = [
    [
        "company" => "Débitos Administrativos",
        "id"      => "admin-card",
        "rows"    => [
            ["mp" => "C001", "medico" => "Pedro Espinoza", "fecha" => "05/07/2025", "total_bruto" => 56000, "debito_os" => 12000, "debito_colegio" => 2500, "reason" => "Cuota colegiatura atrasada."],
            ["mp" => "C002", "medico" => "Ana Gomez",     "fecha" => "03/07/2025", "total_bruto" => 40000, "debito_os" => 9000, "debito_colegio" => 1800, "reason" => "Multa por presentación fuera de término."],
        ]
    ],
    [
        "company" => "Deudas",
        "id"      => "otros-card",
        "rows"    => [
            ["mp" => "C010", "medico" => "Juan Perez", "fecha" => "02/07/2025", "deuda" => 15000, "cód_deuda" => 900, "Estado" => "A"],
        ]
    ]
];

// HELPERS
function money($v)
{
    return $v === null ? '—' : '$' . number_format($v, 0, ',', '.');
}

// OBRAS SOCIALES
function render_section_cards(array $sections): void
{
    foreach ($sections as $section) {
        $osId = htmlspecialchars($section['id']);

        // ROW
        echo '<div class="card-row" data-os-row="' . $osId . '">';

        // CARD
        echo '<div class="data-card is-inactive" id="' . $osId . '" data-os-id="' . $osId . '">';
        echo '  <div class="card-header">';
        echo '    <h2>' . htmlspecialchars($section['company']) . '</h2>';
        echo '    <div class="card-actions">';
        echo '      <button class="btn-outline resumen-btn" disabled>Ver Resumen</button>';
        echo '      <a href="export_excel.php?company=' . urlencode($section['company']) . '" class="btn-outline disabled-link" aria-disabled="true" tabindex="-1">Exportar</a>';
        echo '      <button class="btn-icon toggle-card-table" data-target-table="' . $osId . '-table" disabled title="Activá la obra social para ver la tabla">▲</button>';
        echo '    </div>';
        echo '  </div>';
        echo '  <div class="os-period-picker">';
        echo '    <div class="picker-header">';
        echo '      <h3 class="picker-title"><span class="badge">Periodo</span> Seleccioná un período</h3>';
        echo '    </div>';
        echo '    <div class="picker-controls">';
        echo '      <input type="text" class="period-picker-input" data-os-id="' . $osId . '" placeholder="Elegí una fecha (mes/día/año)" disabled />';
        echo '    </div>';
        echo '    <div class="selected-periods" data-os-id="' . $osId . '"></div>';
        echo '    <div class="period-label hidden" data-os-id="' . $osId . '"></div>';
        echo '  </div>';
        if (!empty($section['rows'])) {
            echo '  <div class="table-container hidden" id="' . $osId . '-table">';
            echo '    <table>';
            echo '      <thead>';
            echo '        <tr>';
            echo '          <th>MP</th>';
            echo '          <th>Médico</th>';
            echo '          <th>Prestaciones</th>';
            echo '          <th>Fecha</th>';
            echo '          <th>Débito</th>';
            echo '          <th>Total Bruto</th>';
            echo '          <th>Total A Pagar</th>';
            echo '          <th></th>';
            echo '        </tr>';
            echo '      </thead>';
            echo '      <tbody>';
            foreach ($section['rows'] as $row) {
                echo '        <tr class="data-row">';
                echo '          <td>' . htmlspecialchars($row['mp']) . '</td>';
                echo '          <td>' . htmlspecialchars($row['medico']) . '</td>';
                echo '          <td>' . htmlspecialchars($row['prestaciones']) . '</td>';
                echo '          <td>' . htmlspecialchars($row['fecha']) . '</td>';
                echo '          <td>' . htmlspecialchars($row['debito']) . '</td>';
                echo '          <td>' . htmlspecialchars($row['total_bruto']) . '</td>';
                echo '          <td>' . htmlspecialchars($row['total_a_pagar']) . '</td>';
                echo '          <td class="row-actions">';
                echo '            <button class="btn-link open-modal" data-doctor="' . htmlspecialchars($row['medico']) . '" data-reason="' . htmlspecialchars($row['reason'] ?? '') . '">Ver Detalle</button>';
                echo '            <a href="export_excel.php?row_mp=' . urlencode($row['mp']) . '" class="btn-link-green">Exportar</a>';
                echo '          </td>';
                echo '        </tr>';
            }
            echo '      </tbody>';
            echo '    </table>';
            echo '  </div>';
        } else {
            echo '<p style="text-align:center;color:var(--medium-grey-text);padding:20px;">No hay datos.</p>';
        }
        echo '</div>';

        // SIDE ACTIONS
        echo '<div class="os-side-actions" aria-label="Acciones de Obra Social">';
        echo '  <button class="btn-add-os" data-os-id="' . $osId . '">Agregar Obra Social</button>';
        echo '  <label class="os-checklist hidden"><input type="checkbox" data-os-id="' . $osId . '" /> Liquidar</label>';
        echo '  <button class="btn-delete-os" data-os-id="' . $osId . '" disabled>Eliminar</button>';
        echo '</div>';

        echo '</div>';
    }
}

// DÉBITOS COLEGIO
function render_section_cards_col(array $sections): void
{
    foreach ($sections as $section) {
        $isDeudas = ($section['company'] === 'Deudas');

        echo '<div class="data-card" id="' . htmlspecialchars($section['id']) . '">';
        echo '  <div class="card-header">';
        echo '    <h2>' . htmlspecialchars($section['company']) . '</h2>';
        echo '    <div class="card-actions">';
        echo '      <button class="btn-outline resumen-btn">Ver Resumen</button>';
        echo '      <a href="export_excel.php?company=' . urlencode($section['company']) . '" class="btn-outline">Exportar</a>';
        echo '      <button class="btn-icon toggle-card-table" data-target-table="' . htmlspecialchars($section['id']) . '-table">▲</button>';
        echo '    </div>';
        echo '  </div>';
        if (!empty($section['rows'])) {
            echo '  <div class="table-container" id="' . htmlspecialchars($section['id']) . '-table">';
            echo '    <table>';
            echo '      <thead>';
            echo '        <tr>';
            if ($isDeudas) {
                echo '          <th>MP</th>';
                echo '          <th>Médico</th>';
                echo '          <th>Fecha</th>';
                echo '          <th>Cód. Deuda</th>';
                echo '          <th>Deuda</th>';
                echo '          <th>Estado</th>';
                echo '          <th></th>';
            } else {
                echo '          <th>MP</th>';
                echo '          <th>Médico</th>';
                echo '          <th>Fecha</th>';
                echo '          <th>Total Bruto</th>';
                echo '          <th>Débito OS</th>';
                echo '          <th>Débito Colegio</th>';
                echo '          <th>Total Neto</th>';
                echo '          <th></th>';
            }
            echo '        </tr>';
            echo '      </thead>';
            echo '      <tbody>';
            foreach ($section['rows'] as $row) {
                echo '        <tr class="data-row">';
                echo '          <td>' . htmlspecialchars($row['mp']) . '</td>';
                echo '          <td>' . htmlspecialchars($row['medico']) . '</td>';
                echo '          <td>' . htmlspecialchars($row['fecha']) . '</td>';
                if ($isDeudas) {
                    $codDeuda = $row['cód_deuda'] ?? $row['cod_deuda'] ?? '—';
                    $deuda    = $row['deuda'] ?? null;
                    $estado   = $row['Estado'] ?? $row['estado'] ?? '—';
                    echo '          <td>' . htmlspecialchars($codDeuda) . '</td>';
                    echo '          <td>' . money($deuda) . '</td>';
                    echo '          <td>' . htmlspecialchars($estado) . '</td>';
                } else {
                    $totalBruto    = $row['total_bruto']    ?? 0;
                    $debitoOS      = $row['debito_os']      ?? 0;
                    $debitoColegio = $row['debito_colegio'] ?? 0;
                    $neto          = $totalBruto - $debitoOS - $debitoColegio;
                    echo '          <td>' . money($totalBruto) . '</td>';
                    echo '          <td>' . money($debitoOS) . '</td>';
                    echo '          <td>' . money($debitoColegio) . '</td>';
                    echo '          <td>' . money($neto) . '</td>';
                }
                echo '          <td class="row-actions">';
                echo '            <button class="btn-link open-modal" data-doctor="' . htmlspecialchars($row['medico']) . '" data-reason="' . htmlspecialchars($row['reason'] ?? '') . '">Ver Detalle</button>';
                echo '            <a href="export_excel.php?row_mp=' . urlencode($row['mp']) . '" class="btn-link-green">Exportar</a>';
                echo '          </td>';
                echo '        </tr>';
            }
            echo '      </tbody>';
            echo '    </table>';
            echo '  </div>';
        } else {
            echo '<p style="text-align:center;color:var(--medium-grey-text);padding:20px;">No hay datos.</p>';
        }
        echo '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Colegio Médico de Corrientes</title>
    <link href="../../../globals.css" rel="stylesheet" />
    <link href="../sidebar/sidebar.css" rel="stylesheet" />
    <link href="./lista_debitos.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include '../sidebar/sidebar.php'; ?>

        <div class="main-content">
            <header class="header">
                <div class="period">
                    <h2 class="period-title">
                        <span class="period-kicker">Ciclo de Liquidación</span>
                        <span class="period-main">Período 2</span>
                    </h2>
                </div>
                <div class="search-bar">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" placeholder="Buscar...">
                </div>
                <div class="header-actions">
                    <a href="export_excel.php" class="export-all-button">Exportar Todo</a>
                </div>
            </header>

            <div class="list-switcher" role="tablist">
                <button class="switch-card active" data-target="lista-os" role="tab" aria-selected="true">
                    <span class="emoji">🏥</span><span>Obras Sociales</span>
                </button>
                <button class="switch-card" data-target="lista-col" role="tab" aria-selected="false">
                    <span class="emoji">🏛️</span><span>Débitos de Colegio</span>
                </button>
            </div>

            <div class="content-area">
                <div id="lista-os" class="list-panel active">
                    <?php render_section_cards($data_sections_os); ?>
                </div>
                <div id="lista-col" class="list-panel">
                    <?php render_section_cards_col($data_sections_col); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- POPUP -->
    <div id="popup-confirm" class="popup-overlay hidden">
        <div class="popup-box">
            <p>¿Estás seguro que quieres eliminar esta Obra Social?</p>
            <div class="popup-actions">
                <button id="popup-cancel">Cancelar</button>
                <button id="popup-accept">Eliminar</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="detailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Detalles del Débito</h3>
                <button class="modal-close-button" id="closeModalButton">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>Médico:</strong> <span id="modalDoctorName"></span></p>
                <p><strong>Razón del Débito:</strong> <span id="modalReason"></span></p>
                <p>Este débito se aplica por la razón especificada.</p>
            </div>
        </div>
    </div>

    <script src="../../../utils/sidebarToggle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // INIT
            initSidebarToggle();

            // TABS
            const switchBtns = document.querySelectorAll('.switch-card');
            const panels = document.querySelectorAll('.list-panel');
            switchBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    switchBtns.forEach(b => {
                        b.classList.toggle('active', b === btn);
                        b.setAttribute('aria-selected', b === btn);
                    });
                    panels.forEach(p => p.classList.remove('active'));
                    document.getElementById(btn.dataset.target).classList.add('active');
                    document.getElementById('searchInput').dispatchEvent(new Event('input'));
                });
            });

            // MODAL
            const detailModal = document.getElementById('detailModal');
            const closeModalButton = document.getElementById('closeModalButton');
            const modalDoctorName = document.getElementById('modalDoctorName');
            const modalReason = document.getElementById('modalReason');
            document.querySelectorAll('.open-modal').forEach(button => {
                button.addEventListener('click', function() {
                    modalDoctorName.textContent = this.dataset.doctor;
                    modalReason.textContent = this.dataset.reason;
                    detailModal.classList.add('show');
                });
            });
            closeModalButton.addEventListener('click', () => detailModal.classList.remove('show'));
            detailModal.addEventListener('click', e => {
                if (e.target === detailModal) detailModal.classList.remove('show');
            });

            // SEARCH
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const visiblePanel = document.querySelector('.list-panel.active');
                visiblePanel.querySelectorAll('.data-row').forEach(row => {
                    let rowText = '';
                    for (let i = 0; i < row.cells.length - 1; i++) {
                        rowText += row.cells[i].textContent.toLowerCase() + ' ';
                    }
                    row.style.display = rowText.includes(term) ? '' : 'none';
                });
            });

            // STATE
            const osState = {};
            let pendingDeleteOsId = null;

            // PER CARD
            document.querySelectorAll('.data-card[data-os-id]').forEach(card => {
                const osId = card.dataset.osId;
                osState[osId] = {
                    active: false,
                    selected: null,
                    fp: null
                };

                const input = card.querySelector('.period-picker-input');
                const chips = card.querySelector('.selected-periods');
                const periodLabel = card.querySelector('.period-label');

                const disableFn = (date) => {
                    const cutoff = new Date('2025-08-01T00:00:00');
                    if (date < cutoff) return true;
                    if (osState[osId].selected) {
                        const ymSel = osState[osId].selected.slice(0, 7);
                        const ym = date.toISOString().slice(0, 7);
                        if (ym === ymSel) return true;
                    }
                    return false;
                };

                const buildPicker = (enabled) => {
                    if (osState[osId].fp) osState[osId].fp.destroy();
                    osState[osId].fp = flatpickr(input, {
                        locale: 'es',
                        dateFormat: 'Y-m-d',
                        minDate: '2025-08-01',
                        disable: [disableFn],
                        clickOpens: enabled,
                        onChange: (selectedDates, dateStr, instance) => {
                            if (!selectedDates.length) return;
                            const picked = selectedDates[0];
                            const m = picked.getMonth() + 1;
                            const payMonth = (m % 12) + 1;
                            const year = picked.getFullYear();
                            const ym = picked.toISOString().slice(0, 7);
                            osState[osId].selected = ym;
                            instance.clear();
                            instance.destroy();
                            card.querySelector('.picker-controls').classList.add('hidden');
                            chips.classList.add('hidden');
                            const monthName = picked.toLocaleDateString('es-AR', {
                                month: 'long'
                            }).replace(/^\w/, c => c.toUpperCase());
                            periodLabel.textContent = 'Período ' + payMonth + ' del ' + year;
                            periodLabel.classList.remove('hidden');
                        }
                    });
                };

                buildPicker(false);
            });

            // ACTIVATE
            document.querySelectorAll('.btn-add-os').forEach(btn => {
                btn.addEventListener('click', () => {
                    const osId = btn.dataset.osId;
                    const row = document.querySelector(`.card-row[data-os-row="${osId}"]`);
                    const card = row.querySelector(`.data-card[data-os-id="${osId}"]`);
                    const deleteBtn = row.querySelector('.btn-delete-os');
                    const resumenBtn = card.querySelector('.resumen-btn');
                    const toggleBtn = card.querySelector('.toggle-card-table');
                    const exportLink = card.querySelector('.card-actions a');
                    const checklist = row.querySelector('.os-checklist');
                    const input = card.querySelector('.period-picker-input');

                    osState[osId].active = true;
                    card.classList.remove('is-inactive');
                    input.disabled = false;
                    resumenBtn.disabled = false;
                    toggleBtn.disabled = false;
                    exportLink.classList.remove('disabled-link');
                    exportLink.removeAttribute('aria-disabled');
                    exportLink.removeAttribute('tabindex');

                    btn.classList.add('hidden');
                    checklist.classList.remove('hidden');
                    deleteBtn.disabled = false;

                    if (osState[osId].fp) osState[osId].fp.destroy();
                    osState[osId].fp = flatpickr(input, {
                        locale: 'es',
                        dateFormat: 'Y-m-d',
                        minDate: '2025-08-01',
                        disable: [(d) => {
                            const cutoff = new Date('2025-08-01T00:00:00');
                            if (d < cutoff) return true;
                            if (osState[osId].selected) {
                                const ymSel = osState[osId].selected.slice(0, 7);
                                const ym = d.toISOString().slice(0, 7);
                                if (ym === ymSel) return true;
                            }
                            return false;
                        }],
                        onChange: (selectedDates, dateStr, instance) => {
                            if (!selectedDates.length) return;
                            const picked = selectedDates[0];
                            const m = picked.getMonth() + 1;
                            const payMonth = (m % 12) + 1;
                            const year = picked.getFullYear();
                            const ym = picked.toISOString().slice(0, 7);
                            osState[osId].selected = ym;
                            instance.clear();
                            instance.destroy();
                            card.querySelector('.picker-controls').classList.add('hidden');
                            card.querySelector('.selected-periods').classList.add('hidden');
                            const monthName = picked.toLocaleDateString('es-AR', {
                                month: 'long'
                            }).replace(/^\w/, c => c.toUpperCase());
                            card.querySelector('.period-label').textContent = 'Período ' + payMonth + ' del ' + year;
                            card.querySelector('.period-label').classList.remove('hidden');
                        }
                    });
                });
            });

            // POPUP
            const popup = document.getElementById("popup-confirm");
            const btnCancel = document.getElementById("popup-cancel");
            const btnAccept = document.getElementById("popup-accept");

            // DELETE
            document.querySelectorAll('.btn-delete-os').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (btn.disabled) return;
                    pendingDeleteOsId = btn.dataset.osId;
                    popup.classList.remove('hidden');
                });
            });

            btnCancel.addEventListener('click', () => {
                popup.classList.add('hidden');
                pendingDeleteOsId = null;
            });

            btnAccept.addEventListener('click', () => {
                popup.classList.add('hidden');
                if (!pendingDeleteOsId) return;
                const osId = pendingDeleteOsId;
                pendingDeleteOsId = null;

                const row = document.querySelector(`.card-row[data-os-row="${osId}"]`);
                const card = row.querySelector(`.data-card[data-os-id="${osId}"]`);
                const addBtn = row.querySelector('.btn-add-os');
                const resumenBtn = card.querySelector('.resumen-btn');
                const toggleBtn = card.querySelector('.toggle-card-table');
                const exportLink = card.querySelector('.card-actions a');
                const chips = card.querySelector('.selected-periods');
                const controls = card.querySelector('.picker-controls');
                const periodLabel = card.querySelector('.period-label');
                const checklist = row.querySelector('.os-checklist');

                osState[osId].active = false;
                osState[osId].selected = null;
                card.classList.add('is-inactive');

                const tableContainer = document.getElementById(`${osId}-table`);
                if (tableContainer && !tableContainer.classList.contains('hidden')) tableContainer.classList.add('hidden');

                card.querySelector('.period-picker-input').disabled = true;
                resumenBtn.disabled = true;
                toggleBtn.disabled = true;
                exportLink.classList.add('disabled-link');
                exportLink.setAttribute('aria-disabled', 'true');
                exportLink.setAttribute('tabindex', '-1');

                chips.innerHTML = '';
                controls.classList.remove('hidden');
                card.querySelector('.selected-periods').classList.remove('hidden');
                periodLabel.classList.add('hidden');

                if (osState[osId].fp) osState[osId].fp.destroy();
                osState[osId].fp = flatpickr(card.querySelector('.period-picker-input'), {
                    locale: 'es',
                    dateFormat: 'Y-m-d',
                    minDate: '2025-08-01',
                    disable: [() => true],
                    clickOpens: false
                });

                addBtn.classList.remove('hidden');
                checklist.classList.add('hidden');
                checklist.querySelector('input').checked = false;

                row.querySelector('.btn-delete-os').disabled = true;
            });

            // TOGGLE TABLE
            document.querySelectorAll('.toggle-card-table').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.disabled) return;
                    const tableContainer = document.getElementById(this.dataset.targetTable);
                    if (tableContainer) {
                        tableContainer.classList.toggle('hidden');
                        this.classList.toggle('rotated');
                        this.textContent = tableContainer.classList.contains('hidden') ? '▼' : '▲';
                    }
                });
            });
        });
    </script>
</body>

</html>