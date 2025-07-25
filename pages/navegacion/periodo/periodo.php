<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Períodos de Liquidación</title>
    <link href="../../../globals.css" rel="stylesheet">
    <link href="../sidebar/sidebar.css" rel="stylesheet">
    <link href="./periodo.css" rel="stylesheet">
</head>

<body>
    <div class="layout">
        <?php include '../sidebar/sidebar.php'; ?>
        <div class="main-content">
            <div class="top-bar">
                <div class="search-container">
                    <input type="search" id="search-periodos" placeholder="Buscar período...">
                    <span class="search-icon">🔍</span>
                </div>
                <div class="actions-container">
                    <div class="dropdown-status">
                        <button class="dropdown-toggle" id="statusDropdownToggle">
                            <span class="icon">📋</span> Estado <span class="arrow">▼</span>
                        </button>
                        <div class="dropdown-menu" id="statusDropdownMenu">
                            <a href="#" class="dropdown-item active" data-value="Todos">Todos</a>
                            <a href="#" class="dropdown-item" data-value="Finalizado">Finalizado</a>
                            <a href="#" class="dropdown-item" data-value="En Curso">En Curso</a>
                        </div>
                    </div>
                    <button class="export-all-btn">Exportar Todo</button>
                </div>
            </div>

            <?php
            $periods = [

                [
                    'periodo' => 'Periodo 2024-05',
                    'total_bruto' => '$140.000',
                    'total_descuentos' => '$20.000',
                    'total_neto' => '$120.000',
                    'estado' => 'En Curso',
                    'link' => '/pages/liquidacion/detalle_periodo.php?id=2024-05'
                ],
                [
                    'periodo' => 'Periodo 2024-04',
                    'total_bruto' => '$95.000',
                    'total_descuentos' => '$10.000',
                    'total_neto' => '$85.000',
                    'estado' => 'Finalizado',
                    'link' => '/pages/liquidacion/detalle_periodo.php?id=2024-04'
                ],
                [
                    'periodo' => 'Periodo 2024-03',
                    'total_bruto' => '$135.000',
                    'total_descuentos' => '$18.000',
                    'total_neto' => '$117.000',
                    'estado' => 'Finalizado',
                    'link' => '/pages/liquidacion/detalle_periodo.php?id=2024-03'
                ],
                [
                    'periodo' => 'Periodo 2024-02',
                    'total_bruto' => '$110.000',
                    'total_descuentos' => '$12.000',
                    'total_neto' => '$98.000',
                    'estado' => 'Finalizado',
                    'link' => '/pages/liquidacion/detalle_periodo.php?id=2024-02'
                ],
                [
                    'periodo' => 'Periodo 2024-01',
                    'total_bruto' => '$120.000',
                    'total_descuentos' => '$15.000',
                    'total_neto' => '$105.000',
                    'estado' => 'Finalizado',
                    'link' => '/pages/liquidacion/detalle_periodo.php?id=2024-01'
                ],
            ];
            ?>

            <div class="section-card" id="periodosSection">
                <div class="section-header">
                    <h2 class="section-title">Períodos de Liquidación</h2>
                    <button class="toggle-section-btn" data-target="periodosSectionContent">▲</button>
                </div>
                <div class="section-content active" id="periodosSectionContent">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Período</th>
                                    <th>Total Bruto</th>
                                    <th>Total de Descuentos</th>
                                    <th>Total Neto</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($periods as $period): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($period['periodo']) ?></td>
                                        <td><?= htmlspecialchars($period['total_bruto']) ?></td>
                                        <td><?= htmlspecialchars($period['total_descuentos']) ?></td>
                                        <td><?= htmlspecialchars($period['total_neto']) ?></td>
                                        <td><span class="status-badge status-<?= strtolower(str_replace(' ', '-', $period['estado'])) ?>"><?= htmlspecialchars($period['estado']) ?></span></td>
                                        <td class="actions-cell">
                                            <a href="../lista_debitos/lista_debitos.php" class="action-link view-detail">Ver</a>
                                            <button class="action-button export-item">Exportar</button>
                                            <button class="action-button reopen-item">Re-abrir</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- Search Functionality ---
        const searchInput = document.getElementById('search-periodos');
        const tableRows = document.querySelectorAll('.section-card tbody tr');

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            tableRows.forEach(row => {
                const periodText = row.cells[0].textContent.toLowerCase();
                if (periodText.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // --- Status Dropdown Functionality ---
        const statusDropdownToggle = document.getElementById('statusDropdownToggle');
        const statusDropdownMenu = document.getElementById('statusDropdownMenu');
        const statusDropdownItems = statusDropdownMenu.querySelectorAll('.dropdown-item');

        statusDropdownToggle.addEventListener('click', () => {
            statusDropdownMenu.classList.toggle('active');
            statusDropdownToggle.querySelector('.arrow').textContent = statusDropdownMenu.classList.contains('active') ? '▲' : '▼';
        });

        statusDropdownItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const selectedStatus = e.target.dataset.value;
                statusDropdownToggle.innerHTML = `<span class="icon">📋</span> ${selectedStatus} <span class="arrow">▼</span>`;
                statusDropdownItems.forEach(dItem => dItem.classList.remove('active'));
                e.target.classList.add('active');
                statusDropdownMenu.classList.remove('active');


                tableRows.forEach(row => {
                    const statusCell = row.cells[4];
                    const rowStatus = statusCell.textContent.trim();

                    if (selectedStatus === 'Todos' || rowStatus === selectedStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        document.addEventListener('click', (e) => {
            if (!statusDropdownToggle.contains(e.target) && !statusDropdownMenu.contains(e.target)) {
                statusDropdownMenu.classList.remove('active');
                statusDropdownToggle.querySelector('.arrow').textContent = '▼';
            }
        });

        // --- Collapsible Section Functionality ---
        document.querySelectorAll('.toggle-section-btn').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.dataset.target;
                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.classList.toggle('active');
                    button.textContent = targetContent.classList.contains('active') ? '▲' : '▼';
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