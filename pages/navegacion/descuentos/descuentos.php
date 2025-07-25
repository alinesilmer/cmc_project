<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Descuentos</title>

    <link href="../../../globals.css" rel="stylesheet" />
    <link href="../sidebar/sidebar.css" rel="stylesheet" />
    <link href="./descuentos.css" rel="stylesheet" />
</head>

<body>
    <div class="layout">
        <?php include '../sidebar/sidebar.php'; ?>

        <div class="main-content">
            <?php
            // ---- datos demo ----
            $descuentos = [
                ['id' => 'DESC001', 'concepto' => 'Descuento por Volumen',       'precio' => '$500',  'porcentaje' => '5'],
                ['id' => 'DESC002', 'concepto' => 'Descuento por Pronto Pago',   'precio' => '$200', 'porcentaje' => '2'],
                ['id' => 'DESC003', 'concepto' => 'Descuento por Uso de Quinta', 'precio' => '$1000', 'porcentaje' => '10'],
                ['id' => 'DESC004', 'concepto' => 'Descuento por Campaña',       'precio' => '$150', 'porcentaje' => '1.5'],
            ];
            ?>

            <section class="section-card" id="descuentosSection">
                <header class="section-header">
                    <div>
                        <h2 class="section-title">Listado de Descuentos</h2>
                        <p class="section-subtitle">Administra y genera descuentos rápidamente</p>
                    </div>
                    <div class="section-actions">
                        <input type="text" id="filtroInput" class="filter-input" placeholder="Buscar…">
                    </div>
                </header>

                <div class="section-content active" id="descuentosSectionContent">
                    <div class="table-container">
                        <table id="tablaDescuentos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Concepto</th>
                                    <th>Precio</th>
                                    <th>%</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($descuentos as $d): ?>
                                    <tr data-id="<?= htmlspecialchars($d['id']) ?>"
                                        data-concepto="<?= htmlspecialchars($d['concepto']) ?>"
                                        data-precio="<?= preg_replace('/[^0-9.]/', '', $d['precio']) ?>"
                                        data-porcentaje="<?= htmlspecialchars($d['porcentaje']) ?>">

                                        <td><?= htmlspecialchars($d['id']) ?></td>
                                        <td><?= htmlspecialchars($d['concepto']) ?></td>

                                        <!-- precio editable -->
                                        <td class="price-cell">
                                            <span class="price-view"><?= htmlspecialchars($d['precio']) ?></span>
                                        </td>

                                        <!-- porcentaje editable -->
                                        <td class="percent-cell">
                                            <span class="percent-view"><?= htmlspecialchars($d['porcentaje']) ?>%</span>
                                        </td>

                                        <td class="actions-cell">
                                            <button class="action-button generate-btn">Generar</button>
                                            <button class="icon-btn edit-btn" title="Editar">✏️</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Toasts -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Modal edición -->
    <div id="editModal" class="modal" hidden aria-hidden="true">
        <div class="modal-backdrop"></div>
        <div class="modal-window" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <button class="modal-close" aria-label="Cerrar">&times;</button>
            <h3 id="modalTitle">Editar Descuento</h3>
            <p id="modalConcepto" class="modal-subtitle"></p>

            <form id="modalForm">
                <label for="modalPrecio" class="modal-label">Nuevo precio ($)</label>
                <input type="number" id="modalPrecio" name="precio" min="0" step="0.01" required class="modal-input" />

                <label for="modalPercent" class="modal-label">Nuevo porcentaje (%)</label>
                <input type="number" id="modalPercent" name="porcentaje" min="0" max="100" step="0.01" required class="modal-input" />

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="modalCancel">Cancelar</button>
                    <button type="submit" class="btn-primary" id="modalSave">Guardar</button>
                </div>
            </form>
        </div>
    </div>


    <!-- JS -->
    <script src="../../../utils/sidebarToggle.js"></script>
    <script src="./descuentos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initSidebarToggle();
        });
    </script>
</body>

</html>