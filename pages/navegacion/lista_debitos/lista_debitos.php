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
            ["mp" => "11114", "medico" => "Ana Gomez",     "prestaciones" => "20", "fecha" => "08/07/2025", "debito" => "$9.000",  "total_bruto" => "$40.000", "total_a_pagar" => "$40.000", "reason" => "Reintegro por medicación no cubierta."],
        ]
    ],
    [
        "company" => "Medifé",
        "id"      => "medife-card",
        "rows"    => [
            ["mp" => "22221", "medico" => "Carlos Ruiz",      "prestaciones" => "18", "fecha" => "12/07/2025", "debito" => "$7.500",  "total_bruto" => "$32.000", "total_a_pagar" => "$32.000", "reason" => "Error en la carga de datos del paciente."],
            ["mp" => "22222", "medico" => "Laura Fernandez",  "prestaciones" => "35", "fecha" => "11/07/2025", "debito" => "$13.000", "total_bruto" => "$60.000", "total_a_pagar" => "$60.000", "reason" => "Prestación fuera de convenio."],
        ]
    ],
    [
        "company" => "OSDE",
        "id"      => "osde-card",
        "rows"    => [
            ["mp" => "33331", "medico" => "Lucía Pérez", "prestaciones" => "22", "fecha" => "10/08/2025", "debito" => "$8.000",  "total_bruto" => "$38.000", "total_a_pagar" => "$38.000", "reason" => "Código mal cargado."],
        ]
    ],
    [
        "company" => "IOMA",
        "id"      => "ioma-card",
        "rows"    => [
            ["mp" => "44441", "medico" => "Nicolás Díaz", "prestaciones" => "28", "fecha" => "09/08/2025", "debito" => "$9.500", "total_bruto" => "$42.000", "total_a_pagar" => "$42.000", "reason" => "Presentación fuera de término."]
        ]
    ],
    [
        "company" => "Swiss Medical",
        "id"      => "swiss-card",
        "rows"    => [
            ["mp" => "55551", "medico" => "Rosa Mena", "prestaciones" => "16", "fecha" => "08/08/2025", "debito" => "$6.000", "total_bruto" => "$29.000", "total_a_pagar" => "$29.000", "reason" => "Falta autorización."]
        ]
    ],
    [
        "company" => "OSECAC",
        "id"      => "osecac-card",
        "rows"    => [
            ["mp" => "66661", "medico" => "Mario Gómez", "prestaciones" => "12", "fecha" => "07/08/2025", "debito" => "$4.000", "total_bruto" => "$20.000", "total_a_pagar" => "$20.000", "reason" => "Cobertura no vigente."]
        ]
    ]
];

$data_sections_col = [
    [
        "company" => "Débitos Administrativos",
        "id"      => "admin-card",
        "rows"    => [
            ["mp" => "C001", "medico" => "Pedro Espinoza", "fecha" => "05/07/2025", "total_bruto" => 56000, "debito_os" => 12000, "debito_colegio" => 2500, "reason" => "Cuota colegiatura atrasada."],
            ["mp" => "C002", "medico" => "Ana Gomez",     "fecha" => "03/07/2025", "total_bruto" => 40000, "debito_os" => 9000,  "debito_colegio" => 1800, "reason" => "Multa por presentación fuera de término."],
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
        echo '<div class="card-row os-block" data-os-row="' . $osId . '" data-os-name="' . htmlspecialchars($section['company']) . '">';
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
            echo '          <th>MP</th><th>Médico</th><th>Prestaciones</th><th>Fecha</th><th>Débito</th><th>Total Bruto</th><th>Total A Pagar</th><th></th>';
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
                echo '          <td class="col-total-pagar">' . htmlspecialchars($row['total_a_pagar']) . '</td>';
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
        // SIDE
        echo '<div class="os-side-actions">';
        echo '  <button class="btn-add-os" data-os-id="' . $osId . '">Agregar Obra Social</button>';
        echo '  <label class="os-checklist hidden"><input type="checkbox" class="chk-liquidar" data-os-id="' . $osId . '" /> Liquidar</label>';
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
                echo '          <th>MP</th><th>Médico</th><th>Fecha</th><th>Cód. Deuda</th><th>Deuda</th><th>Estado</th><th></th>';
            } else {
                echo '          <th>MP</th><th>Médico</th><th>Fecha</th><th>Total Bruto</th><th>Débito OS</th><th>Débito Colegio</th><th>Total Neto</th><th></th>';
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
            <!-- EMPTY -->
            <div id="empty-state" class="empty-state">
                <p>No hay obras sociales cargadas todavía…</p>
                <button id="btn-open-os-picker" class="btn-primary">Cargar Obras Sociales</button>
            </div>

            <!-- HEADER -->
            <header class="header hidden" id="main-header">
                <div class="period">
                    <h2 class="period-title">
                        <span class="period-kicker">Ciclo de Liquidación</span>
                        <span class="period-main">Período 2</span>
                    </h2>
                </div>
                <div class="header-actions">
                    <a href="export_excel.php" class="export-all-button">Exportar Todo</a>
                </div>
            </header>

            <!-- SWITCH -->
            <div class="list-switcher hidden" id="main-switch" role="tablist">
                <button class="switch-card active" data-target="lista-os" role="tab" aria-selected="true">
                    <span class="emoji">🏥</span><span>Obras Sociales</span>
                </button>
                <button class="switch-card" data-target="lista-col" role="tab" aria-selected="false">
                    <span class="emoji">🏛️</span><span>Débitos de Colegio</span>
                </button>
            </div>

            <!-- CONTENT -->
            <div class="content-area hidden" id="content-area">
                <div class="search-bar">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" placeholder="Buscar...">
                </div>
                <div id="lista-os" class="list-panel active">
                    <?php render_section_cards($data_sections_os); ?>
                </div>
                <div id="lista-col" class="list-panel">
                    <?php render_section_cards_col($data_sections_col); ?>
                </div>
            </div>

            <!-- LIQUIDAR BAR -->
            <div class="liquidar-bar hidden" id="liquidar-bar">
                <div class="liquidar-summary">
                    <span id="sel-count">Seleccionadas: 0</span>
                    <span class="dot">•</span>
                    <span>Total estimado: <strong id="sel-total">$0</strong></span>
                </div>
                <button id="btn-liquidar" class="btn-primary" disabled>LIQUIDAR</button>
            </div>
        </div>
    </div>

    <!-- POPUP ELIMINAR -->
    <div id="popup-confirm" class="popup-overlay hidden">
        <div class="popup-box">
            <p>¿Estás seguro que quieres eliminar esta Obra Social?</p>
            <div class="popup-actions">
                <button id="popup-cancel">Cancelar</button>
                <button id="popup-accept">Eliminar</button>
            </div>
        </div>
    </div>

    <!-- POPUP LIQUIDAR -->
    <div id="pay-confirm" class="popup-overlay hidden">
        <div class="popup-box">
            <p>¿Confirmás la liquidación de las obras sociales seleccionadas?</p>
            <div class="popup-actions">
                <button id="pay-cancel">Cancelar</button>
                <button id="pay-accept">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- OS PICKER -->
    <div id="os-picker" class="picker-overlay hidden">
        <div class="picker-modal">
            <div class="picker-header-bar">
                <h3>Seleccioná Obras Sociales</h3>
                <button class="picker-close" id="os-picker-close">×</button>
            </div>
            <div class="picker-search">
                <input type="search" id="os-picker-search" placeholder="Buscar obra social…">
            </div>
            <div class="picker-tags" id="os-selected-tags"></div>
            <div class="picker-grid" id="os-picker-grid">
                <?php foreach ($data_sections_os as $s): ?>
                    <button class="picker-card" data-os-id="<?= htmlspecialchars($s['id']) ?>" data-os-name="<?= htmlspecialchars($s['company']) ?>">
                        <span class="picker-name"><?= htmlspecialchars($s['company']) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="picker-footer">
                <button id="os-load-selected" class="btn-primary" disabled>Cargar seleccionadas</button>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL -->
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
            initSidebarToggle();

            // BASIC
            const emptyState = document.getElementById('empty-state');
            const btnOpenPicker = document.getElementById('btn-open-os-picker');
            const header = document.getElementById('main-header');
            const switcher = document.getElementById('main-switch');
            const area = document.getElementById('content-area');
            const bar = document.getElementById('liquidar-bar');

            // SWITCH
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
                    // Mostrar SOLO una u otra sección
                    if (btn.dataset.target === 'lista-col') {
                        bar.classList.add('hidden'); // ocultar barra Liquidar en este tab
                    } else {
                        bar.classList.remove('hidden');
                    }
                    const si = document.getElementById('searchInput');
                    if (si) si.dispatchEvent(new Event('input'));
                });
            });

            // MODAL DETAIL
            const detailModal = document.getElementById('detailModal');
            const closeModalButton = document.getElementById('closeModalButton');
            const modalDoctorName = document.getElementById('modalDoctorName');
            const modalReason = document.getElementById('modalReason');
            document.addEventListener('click', e => {
                const t = e.target.closest('.open-modal');
                if (!t) return;
                modalDoctorName.textContent = t.dataset.doctor;
                modalReason.textContent = t.dataset.reason;
                detailModal.classList.add('show');
            });
            closeModalButton.addEventListener('click', () => detailModal.classList.remove('show'));
            detailModal.addEventListener('click', e => {
                if (e.target === detailModal) detailModal.classList.remove('show');
            });

            // SEARCH
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const term = this.value.toLowerCase();
                    const visiblePanel = document.querySelector('.list-panel.active');
                    visiblePanel.querySelectorAll('.data-row').forEach(row => {
                        let rowText = '';
                        for (let i = 0; i < row.cells.length - 1; i++) rowText += row.cells[i].textContent.toLowerCase() + ' ';
                        row.style.display = rowText.includes(term) ? '' : 'none';
                    });
                });
            }

            // STATE
            const osState = {};
            let pendingDeleteOsId = null;

            // PICKER
            const picker = document.getElementById('os-picker');
            const pickerClose = document.getElementById('os-picker-close');
            const pickerSearch = document.getElementById('os-picker-search');
            const pickerGrid = document.getElementById('os-picker-grid');
            const pickerTags = document.getElementById('os-selected-tags');
            const loadSelectedBtn = document.getElementById('os-load-selected');
            const selectedSet = new Set();

            btnOpenPicker.addEventListener('click', () => {
                selectedSet.clear();
                pickerTags.innerHTML = '';
                [...pickerGrid.querySelectorAll('.picker-card')].forEach(c => c.classList.remove('active'));
                document.querySelectorAll('.card-row').forEach(row => {
                    if (!row.classList.contains('os-block')) {
                        const id = row.dataset.osRow;
                        selectedSet.add(id);
                        const cardBtn = pickerGrid.querySelector(`.picker-card[data-os-id="${id}"]`);
                        if (cardBtn) cardBtn.classList.add('active');
                        addPickerTag(id, row.dataset.osName);
                    }
                });
                updateLoadBtn();
                picker.classList.remove('hidden');
            });
            pickerClose.addEventListener('click', () => {
                picker.classList.add('hidden');
            });
            picker.addEventListener('click', e => {
                if (e.target === picker) {
                    /* no close on backdrop */
                }
            });

            pickerSearch.addEventListener('input', e => {
                const q = e.target.value.toLowerCase().trim();
                [...pickerGrid.querySelectorAll('.picker-card')].forEach(c => {
                    const name = c.dataset.osName.toLowerCase();
                    c.style.display = name.includes(q) ? '' : 'none';
                });
            });

            pickerGrid.addEventListener('click', e => {
                const btn = e.target.closest('.picker-card');
                if (!btn) return;
                const id = btn.dataset.osId;
                const name = btn.dataset.osName;
                if (selectedSet.has(id)) {
                    selectedSet.delete(id);
                    btn.classList.remove('active');
                    removePickerTag(id);
                } else {
                    selectedSet.add(id);
                    btn.classList.add('active');
                    addPickerTag(id, name);
                }
                updateLoadBtn();
            });

            function addPickerTag(id, name) {
                if (pickerTags.querySelector(`[data-tag-id="${id}"]`)) return;
                const t = document.createElement('span');
                t.className = 'pick-tag';
                t.dataset.tagId = id;
                t.innerHTML = `<span class="tag-text">${name}</span><button class="tag-x" aria-label="Quitar" data-remove-id="${id}">×</button>`;
                pickerTags.appendChild(t);
            }

            function removePickerTag(id) {
                const el = pickerTags.querySelector(`[data-tag-id="${id}"]`);
                if (el) el.remove();
                const cardBtn = pickerGrid.querySelector(`.picker-card[data-os-id="${id}"]`);
                if (cardBtn) cardBtn.classList.remove('active');
                selectedSet.delete(id);
            }
            pickerTags.addEventListener('click', e => {
                const btn = e.target.closest('.tag-x');
                if (!btn) return;
                const id = btn.dataset.removeId;
                removePickerTag(id);
                updateLoadBtn();
            });

            function updateLoadBtn() {
                loadSelectedBtn.disabled = selectedSet.size === 0;
            }

            loadSelectedBtn.addEventListener('click', () => {
                let firstLoad = false;
                if (header.classList.contains('hidden')) firstLoad = true;

                selectedSet.forEach(id => {
                    const row = document.querySelector(`.card-row[data-os-row="${id}"]`);
                    if (row && row.classList.contains('os-block')) {
                        row.classList.remove('os-block');
                        attachPerCardBehaviors(row);
                    }
                });

                picker.classList.add('hidden');
                emptyState.classList.add('hidden');
                header.classList.remove('hidden');
                switcher.classList.remove('hidden');
                area.classList.remove('hidden');
                bar.classList.remove('hidden');

                if (firstLoad) updateLiquidarSummary();
            });

            // PREPARE HIDDEN CARDS
            document.querySelectorAll('.card-row').forEach(row => {
                row.classList.add('os-block');
            });

            // PER CARD
            function attachPerCardBehaviors(row) {
                if (row.dataset.bound === '1') return;
                row.dataset.bound = '1';
                const card = row.querySelector('.data-card[data-os-id]');
                const osId = card.dataset.osId;
                osState[osId] = {
                    active: false,
                    selected: null,
                    fp: null
                };

                const input = card.querySelector('.period-picker-input');
                const chips = card.querySelector('.selected-periods');
                const periodLabel = card.querySelector('.period-label');
                const addBtn = row.querySelector('.btn-add-os');
                const delBtn = row.querySelector('.btn-delete-os');
                const checklistWrap = row.querySelector('.os-checklist');
                const checklist = checklistWrap.querySelector('.chk-liquidar');
                const resumenBtn = card.querySelector('.resumen-btn');
                const toggleBtn = card.querySelector('.toggle-card-table');
                const exportLink = card.querySelector('.card-actions a');

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

                addBtn.addEventListener('click', () => {
                    osState[osId].active = true;
                    card.classList.remove('is-inactive');
                    input.disabled = false;
                    resumenBtn.disabled = false;
                    toggleBtn.disabled = false;
                    exportLink.classList.remove('disabled-link');
                    exportLink.removeAttribute('aria-disabled');
                    exportLink.removeAttribute('tabindex');
                    addBtn.classList.add('hidden');
                    checklistWrap.classList.remove('hidden');
                    delBtn.disabled = false;

                    if (osState[osId].fp) osState[osId].fp.destroy();
                    osState[osId].fp = flatpickr(input, {
                        locale: 'es',
                        dateFormat: 'Y-m-d',
                        minDate: '2025-08-01',
                        disable: [(d) => {
                            const c = new Date('2025-08-01T00:00:00');
                            if (d < c) return true;
                            if (osState[osId].selected) {
                                const a = osState[osId].selected.slice(0, 7),
                                    b = d.toISOString().slice(0, 7);
                                if (a === b) return true;
                            }
                            return false;
                        }],
                        onChange: (selectedDates) => {
                            if (!selectedDates.length) return;
                            const picked = selectedDates[0];
                            const m = picked.getMonth() + 1;
                            const payMonth = (m % 12) + 1;
                            const year = picked.getFullYear();
                            const ym = picked.toISOString().slice(0, 7);
                            osState[osId].selected = ym;
                            osState[osId].fp.clear();
                            osState[osId].fp.destroy();
                            card.querySelector('.picker-controls').classList.add('hidden');
                            chips.classList.add('hidden');
                            const monthName = picked.toLocaleDateString('es-AR', {
                                month: 'long'
                            }).replace(/^\w/, c => c.toUpperCase());
                            periodLabel.textContent = 'Período ' + payMonth + ' del ' + year;;
                            periodLabel.classList.remove('hidden');
                        }
                    });
                });

                // POPUP ELIMINAR
                const popup = document.getElementById("popup-confirm");
                const btnCancel = document.getElementById("popup-cancel");
                const btnAccept = document.getElementById("popup-accept");

                delBtn.addEventListener('click', () => {
                    if (delBtn.disabled) return;
                    pendingDeleteOsId = osId;
                    popup.classList.remove('hidden');
                });
                btnCancel.addEventListener('click', () => {
                    popup.classList.add('hidden');
                    pendingDeleteOsId = null;
                });
                btnAccept.addEventListener('click', () => {
                    popup.classList.add('hidden');
                    if (!pendingDeleteOsId) return;
                    const id = pendingDeleteOsId;
                    pendingDeleteOsId = null;

                    const r = document.querySelector(`.card-row[data-os-row="${id}"]`);
                    const c = r.querySelector(`.data-card[data-os-id="${id}"]`);
                    const add = r.querySelector('.btn-add-os');
                    const res = c.querySelector('.resumen-btn');
                    const tog = c.querySelector('.toggle-card-table');
                    const exp = c.querySelector('.card-actions a');
                    const chipC = c.querySelector('.selected-periods');
                    const controls = c.querySelector('.picker-controls');
                    const pLabel = c.querySelector('.period-label');
                    const chkWrap = r.querySelector('.os-checklist');
                    const chk = chkWrap.querySelector('.chk-liquidar');

                    osState[id].active = false;
                    osState[id].selected = null;
                    c.classList.add('is-inactive');

                    const tableContainer = document.getElementById(`${id}-table`);
                    if (tableContainer && !tableContainer.classList.contains('hidden')) tableContainer.classList.add('hidden');

                    c.querySelector('.period-picker-input').disabled = true;
                    res.disabled = true;
                    tog.disabled = true;
                    exp.classList.add('disabled-link');
                    exp.setAttribute('aria-disabled', 'true');
                    exp.setAttribute('tabindex', '-1');

                    chipC.innerHTML = '';
                    controls.classList.remove('hidden');
                    c.querySelector('.selected-periods').classList.remove('hidden');
                    pLabel.classList.add('hidden');

                    if (osState[id].fp) osState[id].fp.destroy();
                    osState[id].fp = flatpickr(c.querySelector('.period-picker-input'), {
                        locale: 'es',
                        dateFormat: 'Y-m-d',
                        minDate: '2025-08-01',
                        disable: [() => true],
                        clickOpens: false
                    });

                    add.classList.remove('hidden');
                    chkWrap.classList.add('hidden');
                    chk.checked = false;
                    r.querySelector('.btn-delete-os').disabled = true;
                    updateLiquidarSummary();
                });

                row.querySelector('.toggle-card-table').addEventListener('click', function() {
                    if (this.disabled) return;
                    const tableContainer = document.getElementById(this.dataset.targetTable);
                    if (tableContainer) {
                        tableContainer.classList.toggle('hidden');
                        this.classList.toggle('rotated');
                        this.textContent = tableContainer.classList.contains('hidden') ? '▼' : '▲';
                    }
                });

                checklist.addEventListener('change', updateLiquidarSummary);
            }

            // TOTAL
            function parseMoney(txt) {
                if (!txt) return 0;
                const n = txt.toString().replace(/\./g, '').replace(/[^\d]/g, '');
                return Number(n || 0);
            }

            function computeOsTotal(osId) {
                let sum = 0;
                const table = document.getElementById(`${osId}-table`);
                if (!table) return 0;
                table.querySelectorAll('td.col-total-pagar').forEach(td => {
                    sum += parseMoney(td.textContent.trim());
                });
                return sum;
            }

            function formatMoney(n) {
                return n.toLocaleString('es-AR', {
                    style: 'currency',
                    currency: 'ARS',
                    maximumFractionDigits: 0
                });
            }

            function updateLiquidarSummary() {
                const checks = document.querySelectorAll('.chk-liquidar:checked');
                let count = 0,
                    total = 0;
                checks.forEach(ch => {
                    const osId = ch.dataset.osId;
                    count += 1;
                    total += computeOsTotal(osId);
                });
                document.getElementById('sel-count').textContent = 'Seleccionadas: ' + count;
                document.getElementById('sel-total').textContent = formatMoney(total);
                const btn = document.getElementById('btn-liquidar');
                btn.disabled = count === 0;
            }

            // LIQUIDAR POPUP
            const btnLiquidar = document.getElementById('btn-liquidar');
            const payConfirm = document.getElementById('pay-confirm');
            const payCancel = document.getElementById('pay-cancel');
            const payAccept = document.getElementById('pay-accept');

            btnLiquidar.addEventListener('click', () => {
                if (!btnLiquidar.disabled) payConfirm.classList.remove('hidden');
            });
            payCancel.addEventListener('click', () => {
                payConfirm.classList.add('hidden');
            });
            payAccept.addEventListener('click', () => {
                payConfirm.classList.add('hidden');
                alert('Liquidación confirmada.');
            });
        });
    </script>
</body>

</html>