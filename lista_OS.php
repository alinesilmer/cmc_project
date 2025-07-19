<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colegio Médico de Corrientes</title>
    <style>
        :root {
            --primary-blue: #007bff;
            --light-blue: #e0f2ff;
            --dark-grey-text: #333;
            --medium-grey-text: #666;
            --light-grey-bg: #f4f7f9;
            --white: #fff;
            --border-color: #e0e0e0;
            --green-export: #28a745;
            --light-green-bg: #e6ffe6;
            --shadow-light: rgba(0, 0, 0, 0.05);
            --modal-overlay-bg: rgba(0, 0, 0, 0.5);
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--light-grey-bg);
            color: var(--dark-grey-text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: var(--white);
            padding: 20px;
            box-shadow: 2px 0 5px var(--shadow-light);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            flex-shrink: 0;
        }

        .sidebar .logo img {
            max-width: 120px;
            height: auto;
            margin-bottom: 20px;
        }

        .sidebar .nav-links {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .sidebar .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            text-decoration: none;
            color: var(--medium-grey-text);
            border-radius: 8px;
            transition: background-color 0.2s, color 0.2s;
            font-weight: 500;
        }

        .sidebar .nav-item .icon {
            font-size: 1.2em;
            line-height: 1;
        }

        .sidebar .nav-item:hover {
            background-color: var(--light-grey-bg);
            color: var(--primary-blue);
        }

        .sidebar .nav-item.active {
            background-color: var(--light-blue);
            color: var(--primary-blue);
            font-weight: 600;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--white);
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px var(--shadow-light);
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 12px;
            flex-grow: 1;
            max-width: 400px;
        }

        .search-bar .search-icon {
            color: var(--medium-grey-text);
            margin-right: 8px;
        }

        .search-bar input {
            border: none;
            outline: none;
            flex-grow: 1;
            font-size: 1em;
            padding: 2px 0;
            color: var(--dark-grey-text);
        }

        .search-bar input::placeholder {
            color: var(--medium-grey-text);
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            position: relative;
        }

        .period-button,
        .export-all-button,
        .btn-outline,
        .btn-icon,
        .btn-link,
        .btn-link-green {
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.2s, border-color 0.2s, color 0.2s;
            text-decoration: none;
            white-space: nowrap;
            border: 1px solid var(--border-color);
            background-color: var(--white);
            color: var(--dark-grey-text);
        }

        .period-button {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            color: var(--dark-grey-text);
        }

        .period-button:hover {
            background-color: var(--light-grey-bg);
        }

        .period-button .icon {
            font-size: 1.1em;
        }

        .period-button .arrow {
            margin-left: 5px;
            transition: transform 0.2s;
        }

        .period-button.active .arrow {
            transform: rotate(180deg);
        }

        .export-all-button {
            background-color: var(--green-export);
            color: var(--white);
            border: 1px solid var(--green-export);
        }

        .export-all-button:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .period-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            min-width: 150px;
            z-index: 10;
            padding: 5px 0;
            display: none;
        }

        .period-dropdown.show {
            display: block;
        }

        .period-dropdown a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: var(--dark-grey-text);
            transition: background-color 0.2s;
        }

        .period-dropdown a:hover {
            background-color: var(--light-grey-bg);
        }

        .content-area {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .data-card {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 2px 5px var(--shadow-light);
            padding: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-header h2 {
            margin: 0;
            font-size: 1.5em;
            color: var(--primary-blue);
        }

        .card-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-outline {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            color: var(--dark-grey-text);
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9em;
        }

        .btn-outline:hover {
            background-color: var(--light-grey-bg);
        }

        .btn-icon {
            background: none;
            border: none;
            color: var(--medium-grey-text);
            font-size: 1.2em;
            padding: 5px;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s, color 0.2s;
        }

        .btn-icon:hover {
            color: var(--primary-blue);
            background-color: var(--light-grey-bg);
        }

        .btn-icon.rotated {
            transform: rotate(180deg);
        }

        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            max-height: 1000px;
            opacity: 1;
        }

        .table-container.hidden {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            padding-top: 0;
            padding-bottom: 0;
            margin-top: 0;
            margin-bottom: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--light-grey-bg);
            font-weight: 600;
            color: var(--dark-grey-text);
            white-space: nowrap;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .row-actions {
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .btn-link {
            background: none;
            border: none;
            color: var(--primary-blue);
            text-decoration: none;
            padding: 5px 8px;
            border-radius: 4px;
            font-size: 0.85em;
        }

        .btn-link:hover {
            background-color: var(--light-blue);
        }

        .btn-link-green {
            background: none;
            border: none;
            color: var(--green-export);
            text-decoration: none;
            padding: 5px 8px;
            border-radius: 4px;
            font-size: 0.85em;
        }

        .btn-link-green:hover {
            background-color: var(--light-green-bg);
        }

        .statistics-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .statistics-list li {
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .statistics-list li:last-child {
            border-bottom: none;
        }

        .statistics-list li strong {
            color: var(--primary-blue);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--modal-overlay-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: var(--white);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 500px;
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.4em;
            color: var(--primary-blue);
        }

        .modal-close-button {
            background: none;
            border: none;
            font-size: 1.8em;
            cursor: pointer;
            color: var(--medium-grey-text);
            transition: color 0.2s;
        }

        .modal-close-button:hover {
            color: var(--dark-grey-text);
        }

        .modal-body p {
            margin-bottom: 15px;
            line-height: 1.8;
        }

        .modal-body strong {
            color: var(--dark-grey-text);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 15px;
                box-shadow: 0 2px 5px var(--shadow-light);
                flex-direction: row;
                justify-content: space-around;
                gap: 10px;
                position: sticky;
                top: 0;
                z-index: 1000;
            }

            .sidebar .logo {
                display: none;
            }

            .sidebar .nav-links {
                flex-direction: row;
                justify-content: space-around;
                width: auto;
                flex-grow: 1;
            }

            .sidebar .nav-item {
                padding: 8px 12px;
                font-size: 0.9em;
                flex-direction: column;
                text-align: center;
                gap: 5px;
            }

            .main-content {
                padding: 15px;
            }

            .header {
                flex-direction: column;
                align-items: stretch;
                padding: 15px;
            }

            .search-bar {
                max-width: 100%;
            }

            .header-actions {
                width: 100%;
                justify-content: space-between;
            }

            .period-button,
            .export-all-button {
                flex-grow: 1;
                text-align: center;
                justify-content: center;
            }

            .period-dropdown {
                width: 100%;
                left: 0;
                right: 0;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .modal-content {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .sidebar .nav-item {
                padding: 6px 8px;
                font-size: 0.8em;
            }

            .period-button,
            .export-all-button {
                font-size: 0.85em;
                padding: 8px 10px;
            }

            .btn-outline,
            .btn-icon,
            .btn-link,
            .btn-link-green {
                font-size: 0.8em;
                padding: 6px 10px;
            }

            th,
            td {
                padding: 8px 10px;
                font-size: 0.9em;
            }

            .modal-content {
                padding: 15px;
            }

            .modal-header h3 {
                font-size: 1.2em;
            }

            .modal-close-button {
                font-size: 1.5em;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Captura%20de%20pantalla%202025-07-17%20011742-eVC4VcrrYeUxLarnt9S0vjBsNMtmfY.png" alt="CMC Logo">
            </div>
            <nav class="nav-links">
                <a href="#" class="nav-item active">
                    <span class="icon">📄</span> Facturación
                </a>
                <a href="#" class="nav-item">
                    <span class="icon">💰</span> Liquidación
                </a>
                <a href="#" class="nav-item">
                    <span class="icon">📊</span> Estadísticas
                </a>
            </nav>
        </aside>
        <div class="main-content">
            <header class="header">
                <div class="search-bar">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" placeholder="Buscar...">
                </div>
                <div class="header-actions">
                    <button class="period-button" id="periodButton">
                        <span class="icon">📅</span> <span id="selectedPeriod">Período 2</span> <span class="arrow">▼</span>
                    </button>
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

                <div class="data-card">
                    <div class="card-header">
                        <h2>Estadísticas de Prestaciones</h2>
                        <div class="card-actions">
                            <button class="btn-icon toggle-card-table" data-target-table="statistics-table">▲</button>
                        </div>
                    </div>
                    <div class="table-container" id="statistics-table">
                        <ul class="statistics-list">
                            <?php foreach ($prestaciones_summary as $company_name => $total_prestaciones): ?>
                                <li>
                                    <span><?php echo htmlspecialchars($company_name); ?>:</span>
                                    <strong><?php echo htmlspecialchars($total_prestaciones); ?> Prestaciones</strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

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
</body>

</html>