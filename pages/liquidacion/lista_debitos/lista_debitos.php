<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colegio Médico de Corrientes</title>
    <link href="../../../globals.css" rel="stylesheet">
    <link href="../../navegacion/sidebar/sidebar.css" rel="stylesheet">
    <link href="./lista_debitos.css" rel="stylesheet">
</head>

<body>
    <div class="dashboard-container">
        <?php include '../../navegacion/sidebar/sidebar.php'; ?>
        <div class="main-content">
            <header class="header">
                <div class="search-bar">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" placeholder="Buscar...">
                </div>
                <div class="header-actions">
                    <!-- <button class="period-button" id="periodButton">
                        <span class="icon">📅</span> <span id="selectedPeriod">Período 2</span> <span class="arrow">▼</span>
                    </button> -->
                    <div class="period-dropdown" id="periodDropdown">
                        <a href="#" data-period="Período 1">Período 1</a>
                        <a href="#" data-period="Período 2">Período 2</a>
                        <a href="#" data-period="Período 3">Período 3</a>
                        <a href="#" data-period="Período 4">Período 4</a>
                    </div>
                    <a href="export_excel.php" class="export-all-button">Exportar Todo</a>
                </div>
            </header>
            <div class="content-area">
                <?php
                $data_sections = [
                    [
                        "company" => "Sancor",
                        "id" => "sancor-card",
                        "rows" => [
                            ["mp" => "11111", "medico" => "Pedro Espinoza", "prestaciones" => "30", "fecha" => "11/07/2025", "debito" => "$12.000", "total_bruto" => "$56.000", "total_a_pagar" => "$56.000", "reason" => "Mala Praxis por error de diagnóstico."],
                            ["mp" => "11112", "medico" => "Maria Lopez", "prestaciones" => "25", "fecha" => "10/07/2025", "debito" => "$10.000", "total_bruto" => "$45.000", "total_a_pagar" => "$45.000", "reason" => "Facturación duplicada en el sistema."],
                            ["mp" => "11113", "medico" => "Juan Perez", "prestaciones" => "40", "fecha" => "09/07/2025", "debito" => "$15.000", "total_bruto" => "$70.000", "total_a_pagar" => "$70.000", "reason" => "Servicio no autorizado por la Obra Social."],
                            ["mp" => "11114", "medico" => "Ana Gomez", "prestaciones" => "20", "fecha" => "08/07/2025", "debito" => "$9.000", "total_bruto" => "$40.000", "total_a_pagar" => "$40.000", "reason" => "Reintegro por medicación no cubierta."],
                        ]
                    ],
                    [
                        "company" => "Medifé",
                        "id" => "medife-card",
                        "rows" => [
                            ["mp" => "22221", "medico" => "Carlos Ruiz", "prestaciones" => "18", "fecha" => "12/07/2025", "debito" => "$7.500", "total_bruto" => "$32.000", "total_a_pagar" => "$32.000", "reason" => "Error en la carga de datos del paciente."],
                            ["mp" => "22222", "medico" => "Laura Fernandez", "prestaciones" => "35", "fecha" => "11/07/2025", "debito" => "$13.000", "total_bruto" => "$60.000", "total_a_pagar" => "$60.000", "reason" => "Prestación fuera de convenio."],
                        ]
                    ]
                ];

                $prestaciones_summary = [];
                foreach ($data_sections as $section) {
                    $total_prestaciones = 0;
                    foreach ($section["rows"] as $row) {
                        $total_prestaciones += (int)$row["prestaciones"];
                    }
                    $prestaciones_summary[$section["company"]] = $total_prestaciones;
                }

                foreach ($data_sections as $section) {
                    echo '<div class="data-card" id="' . htmlspecialchars($section["id"]) . '">';
                    echo '<div class="card-header">';
                    echo '<h2>' . htmlspecialchars($section["company"]) . '</h2>';
                    echo '<div class="card-actions">';
                    echo '<button class="btn-outline">Ver Resumen</button>';
                    echo '<a href="export_excel.php?company=' . urlencode($section["company"]) . '" class="btn-outline">Exportar</a>';
                    echo '<button class="btn-icon toggle-card-table" data-target-table="' . htmlspecialchars($section["id"]) . '-table">▲</button>';
                    echo '</div>';
                    echo '</div>';

                    if (!empty($section["rows"])) {
                        echo '<div class="table-container" id="' . htmlspecialchars($section["id"]) . '-table">';
                        echo '<table>';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>MP</th>';
                        echo '<th>Médico</th>';
                        echo '<th>Prestaciones</th>';
                        echo '<th>Fecha</th>';
                        echo '<th>Débito</th>';
                        echo '<th>Total Bruto</th>';
                        echo '<th>Total A Pagar</th>';
                        echo '<th></th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        foreach ($section["rows"] as $row) {
                            echo '<tr class="data-row">';
                            echo '<td>' . htmlspecialchars($row["mp"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["medico"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["prestaciones"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["fecha"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["debito"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["total_bruto"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["total_a_pagar"]) . '</td>';
                            echo '<td class="row-actions">';
                            echo '<button class="btn-link open-modal" data-doctor="' . htmlspecialchars($row["medico"]) . '" data-reason="' . htmlspecialchars($row["reason"]) . '">Ver Detalle</button>';
                            echo '<a href="export_excel.php?row_mp=' . urlencode($row["mp"]) . '" class="btn-link-green">Exportar</a>';
                            echo '<button class="btn-icon toggle-row-details">▼</button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    } else {
                        echo '<p style="text-align: center; color: var(--medium-grey-text); padding: 20px;">No data available for ' . htmlspecialchars($section["company"]) . '.</p>';
                    }
                    echo '</div>';
                }
                ?>

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
                <p>Este débito se aplica por la razón especificada por la Obra Social.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-card-table').forEach(button => {
                button.addEventListener('click', function() {
                    const targetTableId = this.dataset.targetTable;
                    const tableContainer = document.getElementById(targetTableId);
                    if (tableContainer) {
                        tableContainer.classList.toggle('hidden');
                        this.classList.toggle('rotated');
                        this.textContent = tableContainer.classList.contains('hidden') ? '▼' : '▲';
                    }
                });
            });

            document.querySelectorAll('.toggle-row-details').forEach(button => {
                button.addEventListener('click', function() {
                    alert('Showing more details for this row!');
                    this.classList.toggle('rotated');
                });
            });

            const periodButton = document.getElementById('periodButton');
            const periodDropdown = document.getElementById('periodDropdown');
            const selectedPeriodSpan = document.getElementById('selectedPeriod');

            periodButton.addEventListener('click', function() {
                periodDropdown.classList.toggle('show');
                periodButton.classList.toggle('active');
            });

            periodDropdown.querySelectorAll('a').forEach(item => {
                item.addEventListener('click', function(event) {
                    event.preventDefault();
                    selectedPeriodSpan.textContent = this.dataset.period;
                    periodDropdown.classList.remove('show');
                    periodButton.classList.remove('active');
                });
            });

            window.addEventListener('click', function(event) {
                if (!periodButton.contains(event.target) && !periodDropdown.contains(event.target)) {
                    periodDropdown.classList.remove('show');
                    periodButton.classList.remove('active');
                }
            });

            const searchInput = document.getElementById('searchInput');
            const allDataRows = document.querySelectorAll('.data-row');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                allDataRows.forEach(row => {
                    let rowText = '';
                    for (let i = 0; i < row.cells.length - 1; i++) {
                        rowText += row.cells[i].textContent.toLowerCase() + ' ';
                    }

                    if (rowText.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            const detailModal = document.getElementById('detailModal');
            const closeModalButton = document.getElementById('closeModalButton');
            const modalDoctorName = document.getElementById('modalDoctorName');
            const modalReason = document.getElementById('modalReason');

            document.querySelectorAll('.open-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const doctor = this.dataset.doctor;
                    const reason = this.dataset.reason;

                    modalDoctorName.textContent = doctor;
                    modalReason.textContent = reason;
                    detailModal.classList.add('show');
                });
            });

            closeModalButton.addEventListener('click', function() {
                detailModal.classList.remove('show');
            });

            detailModal.addEventListener('click', function(event) {
                if (event.target === detailModal) {
                    detailModal.classList.remove('show');
                }
            });
        });
    </script>
    <script src="../../../utils/sidebarToggle.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initSidebarToggle();
        });
    </script>
</body>

</html>