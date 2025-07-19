<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Dashboard</title>
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
            --success-green: #28a745;
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

        .sidebar .nav-group-label {
            padding: 12px 20px 5px;
            color: var(--dark-grey-text);
            font-weight: 600;
            font-size: 0.9em;
            width: 100%;
            box-sizing: border-box;
        }

        .sidebar .nav-sub-item {
            padding: 8px 20px 8px 40px;
            text-decoration: none;
            color: var(--medium-grey-text);
            border-radius: 8px;
            transition: background-color 0.2s, color 0.2s;
            font-size: 0.9em;
            display: block;
            width: 100%;
            box-sizing: border-box;
        }

        .sidebar .nav-sub-item:hover {
            background-color: var(--light-grey-bg);
            color: var(--primary-blue);
        }

        .sidebar .nav-sub-item.active {
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
        .btn-link-green,
        .btn-primary {
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

        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
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

            .sidebar .nav-item,
            .sidebar .nav-group-label,
            .sidebar .nav-sub-item {
                padding: 8px 12px;
                font-size: 0.9em;
                flex-direction: column;
                text-align: center;
                gap: 5px;
            }

            .sidebar .nav-sub-item {
                padding-left: 12px;
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
        }

        @media (max-width: 480px) {

            .sidebar .nav-item,
            .sidebar .nav-group-label,
            .sidebar .nav-sub-item {
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
            .btn-link-green,
            .btn-primary {
                font-size: 0.8em;
                padding: 6px 10px;
            }

            th,
            td {
                padding: 8px 10px;
                font-size: 0.9em;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Captura%20de%20pantalla%202025-07-17%20011742-eVC4VcrrYeUxLarnt9S0vjBsNMtmfY.png" alt="CMC Logo">
            </div>
            <nav class="nav-links">
                <a href="index.php" class="nav-item">
                    <span class="icon">📄</span> Facturación
                </a>
                <div class="nav-group-label">Liquidación</div>
                <a href="debitos_OS.php" class="nav-sub-item">
                    <span class="icon">📋</span> Débitos por O.S.
                </a>
                <a href="contadora.php" class="nav-sub-item">
                    <span class="icon">💲</span> Contadora
                </a>
                <a href="estadisticas.php" class="nav-item active">
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
                <div class="data-card">
                    <div class="card-header">
                        <h2>Prestaciones por Obra Social</h2>
                        <div class="card-actions">
                            <button class="btn-icon toggle-card-table" data-target-table="chart-container">▲</button>
                        </div>
                    </div>
                    <div class="chart-container" id="chart-container">
                        <canvas id="prestacionesChart"></canvas>
                    </div>
                </div>

                <?php
                $data_sections = [
                    [
                        "company" => "Sancor",
                        "rows" => [
                            ["prestaciones" => "30"],
                            ["prestaciones" => "25"],
                            ["prestaciones" => "40"],
                            ["prestaciones" => "20"],
                        ]
                    ],
                    [
                        "company" => "Medifé",
                        "rows" => [
                            ["prestaciones" => "18"],
                            ["prestaciones" => "35"],
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

                $chart_labels = json_encode(array_keys($prestaciones_summary));
                $chart_data = json_encode(array_values($prestaciones_summary));
                ?>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('prestacionesChart').getContext('2d');
                        const chartLabels = <?php echo $chart_labels; ?>;
                        const chartData = <?php echo $chart_data; ?>;

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: chartLabels,
                                datasets: [{
                                    label: 'Total Prestaciones',
                                    data: chartData,
                                    backgroundColor: [
                                        'rgba(0, 123, 255, 0.7)',
                                        'rgba(40, 167, 69, 0.7)',
                                        'rgba(255, 193, 7, 0.7)',
                                        'rgba(220, 53, 69, 0.7)'
                                    ],
                                    borderColor: [
                                        'rgba(0, 123, 255, 1)',
                                        'rgba(40, 167, 69, 1)',
                                        'rgba(255, 193, 7, 1)',
                                        'rgba(220, 53, 69, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Número de Prestaciones'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Obra Social'
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    title: {
                                        display: true,
                                        text: 'Resumen de Prestaciones por Obra Social'
                                    }
                                }
                            }
                        });

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
                    });
                </script>
            </div>
        </div>
    </div>
</body>

</html>