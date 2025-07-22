<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Médicos</title>
    <link href="../../../globals.css" rel="stylesheet">
    <link href="../../navegacion/sidebar/sidebar.css" rel="stylesheet">
    <link href="./listar_medicos.css" rel="stylesheet">
</head>

<body>
    <div class="layout">
        <?php include '../../navegacion/sidebar/sidebar.php'; ?>

        <div class="main-content">
            <?php
            /* ---------- conexión a BD SOLO para lista de médicos ---------- */
            $medicos = []; // Initialize as empty array
            try {
                $pdo = new PDO(
                    'mysql:host=localhost;dbname=cmc_db',
                    'root',
                    '',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                /* ---- Fetch Médicos ---- */
                $medicos = $pdo->query("SELECT nro_socio,nombre,matricula_prov,matricula_nac
                                        FROM listado_medico ORDER BY nombre")
                    ->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Database connection error for doctors list: " . $e->getMessage());
                // Optionally, display a user-friendly message
                // echo "<p class='error-message'>Error al conectar con la base de datos de médicos.</p>";
            }

            // Dummy function to get discounts for a given doctor (replace with real DB query)
            function getDoctorDiscounts($nro_socio)
            {
                $allDiscounts = [
                    '11111' => [ // Pedro Espinoza
                        ['id' => 'D001', 'concepto' => 'Descuento por Antigüedad', 'precio' => '$150', 'porcentaje' => '1.5%'],
                        ['id' => 'D002', 'concepto' => 'Descuento por Volumen Mensual', 'precio' => '$300', 'porcentaje' => '3%'],
                    ],
                    '11112' => [ // Maria Lopez
                        ['id' => 'D003', 'concepto' => 'Descuento por Especialidad', 'precio' => '$250', 'porcentaje' => '2.5%'],
                    ],
                    '11113' => [ // Juan Perez
                        ['id' => 'D004', 'concepto' => 'Descuento por Convenio A', 'precio' => '$400', 'porcentaje' => '4%'],
                        ['id' => 'D005', 'concepto' => 'Descuento por Campaña Invierno', 'precio' => '$100', 'porcentaje' => '1%'],
                        ['id' => 'D006', 'concepto' => 'Descuento por Volumen Anual', 'precio' => '$600', 'porcentaje' => '6%'],
                    ],
                    '11114' => [ // Ana Gomez
                        ['id' => 'D007', 'concepto' => 'Descuento por Referencia', 'precio' => '$75', 'porcentaje' => '0.75%'],
                    ],
                    // Add more doctors and their specific discounts
                ];
                return $allDiscounts[$nro_socio] ?? []; // Return empty array if doctor not found
            }

            // Prepare data for JavaScript (including discounts)
            $medicos_with_discounts = [];
            foreach ($medicos as $m) {
                $m['descuentos'] = getDoctorDiscounts($m['nro_socio']);
                $medicos_with_discounts[] = $m;
            }
            ?>

            <div class="section-card" id="medicosSection">
                <div class="section-header">
                    <h2 class="section-title">Listado de médicos</h2>
                </div>
                <div class="section-content active" id="medicosSectionContent">
                    <div class="search-container-inline">
                        <input type="search" id="buscarMed" placeholder="Buscar médico…">
                        <span class="search-icon">🔍</span>
                    </div>
                    <div class="table-container">
                        <table id="tablaMed">
                            <thead>
                                <tr>
                                    <th>Nro Socio</th>
                                    <th>Nombre</th>
                                    <th>Matrícula Prov.</th>
                                    <th>Matrícula Nac.</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($medicos_with_discounts)): ?>
                                    <?php foreach ($medicos_with_discounts as $m): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($m['nro_socio']) ?></td>
                                            <td><?= htmlspecialchars($m['nombre']) ?></td>
                                            <td><?= htmlspecialchars($m['matricula_prov']) ?></td>
                                            <td><?= htmlspecialchars($m['matricula_nac']) ?></td>
                                            <td class="actions-cell">
                                                <button
                                                    class="action-button view-discounts-btn"
                                                    data-nro-socio="<?= htmlspecialchars($m['nro_socio']) ?>"
                                                    data-doctor-name="<?= htmlspecialchars($m['nombre']) ?>">
                                                    Descuentos
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 20px; color: var(--text-medium);">No se encontraron médicos o hubo un error de conexión.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Discounts Modal Structure -->
    <div id="discountsModal" class="modal-overlay">
        <div class="modal-content-discounts">
            <div class="modal-header-discounts">
                <h3 id="modalDoctorName">Descuentos para [Nombre del Médico]</h3>
                <button class="modal-close-btn">✖</button>
            </div>
            <div class="modal-body-discounts">
                <div class="table-container">
                    <table id="modalDiscountsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Concepto</th>
                                <th>Precio</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Discounts will be loaded here by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <p id="noDiscountsMessage" class="no-data-message" style="display: none;">No hay descuentos asignados a este médico.</p>
            </div>
        </div>
    </div>

    <script src="../../../utils/sidebarToggle.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initSidebarToggle();
        });
    </script>
    <script>
        // Pass PHP data to JavaScript
        const allDoctorsData = <?= json_encode($medicos_with_discounts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>;

        // --- Search Functionality for Medicos Table ---
        const buscarMedInput = document.getElementById('buscarMed');
        const tablaMedRows = document.querySelectorAll('#tablaMed tbody tr');

        if (buscarMedInput) {
            buscarMedInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                tablaMedRows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

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

        // --- Discounts Modal Functionality ---
        const discountsModal = document.getElementById('discountsModal');
        const modalCloseBtn = discountsModal.querySelector('.modal-close-btn');
        const modalDoctorName = document.getElementById('modalDoctorName');
        const modalDiscountsTableBody = document.querySelector('#modalDiscountsTable tbody');
        const noDiscountsMessage = document.getElementById('noDiscountsMessage');

        document.querySelectorAll('.view-discounts-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const nroSocio = e.target.dataset.nroSocio;
                const doctorName = e.target.dataset.doctorName;

                modalDoctorName.textContent = `Descuentos para ${doctorName}`;
                modalDiscountsTableBody.innerHTML = '';

                const doctorData = allDoctorsData.find(doc => doc.nro_socio === nroSocio);
                const discounts = doctorData ? doctorData.descuentos : [];

                if (discounts.length > 0) {
                    noDiscountsMessage.style.display = 'none';
                    discounts.forEach(discount => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${discount.id}</td>
                            <td>${discount.concepto}</td>
                            <td>${discount.precio}</td>
                            <td>${discount.porcentaje}</td>
                        `;
                        modalDiscountsTableBody.appendChild(row);
                    });
                    document.getElementById('modalDiscountsTable').style.display = ''; // Show table
                } else {
                    document.getElementById('modalDiscountsTable').style.display = 'none'; // Hide table
                    noDiscountsMessage.style.display = 'block';
                }

                discountsModal.classList.add('active');
            });
        });

        // Close modal functions
        const closeModal = () => {
            discountsModal.classList.remove('active');
        };

        modalCloseBtn.addEventListener('click', closeModal);

        discountsModal.addEventListener('click', (e) => {
            if (e.target === discountsModal) { // Close if clicked on overlay
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && discountsModal.classList.contains('active')) {
                closeModal();
            }
        });
    </script>
</body>

</html>