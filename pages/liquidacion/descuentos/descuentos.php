<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Descuentos</title>

    <!-- estilos -->
    <link href="../../../globals.css" rel="stylesheet">
    <link href="../../navegacion/sidebar/sidebar.css" rel="stylesheet">
    <link href="./descuentos.css" rel="stylesheet">
</head>

<body>
    <div class="layout">
        <?php include '../../navegacion/sidebar/sidebar.php'; ?>

        <div class="main-content">
            <?php
            /* datos de ejemplo */
            $descuentos = [
                ['id' => 'DESC001', 'concepto' => 'Descuento por Volumen',      'precio' => '$500',  'porcentaje' => '5'],
                ['id' => 'DESC002', 'concepto' => 'Descuento por Pronto Pago',  'precio' => '$200', 'porcentaje' => '2'],
                ['id' => 'DESC003', 'concepto' => 'Descuento por Uso de Quinta', 'precio' => '$1000', 'porcentaje' => '10'],
                ['id' => 'DESC004', 'concepto' => 'Descuento por Campaña',      'precio' => '$150', 'porcentaje' => '1.5'],
            ];
            ?>

            <div class="section-card" id="descuentosSection">
                <div class="section-header">
                    <h2 class="section-title">Listado de Descuentos</h2>
                </div>

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
                                <?php if ($descuentos): ?>
                                    <?php foreach ($descuentos as $d): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($d['id']) ?></td>
                                            <td><?= htmlspecialchars($d['concepto']) ?></td>
                                            <td><?= htmlspecialchars($d['precio']) ?></td>

                                            <!-- porcentaje editable -->
                                            <td>
                                                <span class="percent-view"><?= htmlspecialchars($d['porcentaje']) ?> %</span>
                                                <input type="number" step="0.01" min="0" max="100"
                                                    class="percent-input" value="<?= htmlspecialchars($d['porcentaje']) ?>" hidden>
                                            </td>

                                            <td class="actions-cell">
                                                <button class="action-button generate-btn">Generar</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center;padding:20px">No se encontraron descuentos.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="toastContainer" class="toast-container"></div>

    <!-- lógica JS -->
    <script>
        const toastContainer = document.getElementById('toastContainer');

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <span class="icon">${type === 'success' ? '✔️' : '⚠️'}</span>
                <span>${message}</span>
            `;
            toastContainer.appendChild(toast);
            // quitar después de animación
            setTimeout(() => toast.remove(), 4000);
        }

        /* ------------- botón “Generar” ------------- */
        document.querySelectorAll('.generate-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                btn.classList.add('loading');

                const row = btn.closest('tr');
                const id = row.cells[0].textContent.trim();
                const concepto = row.cells[1].textContent.trim();

                try {
                    // simulación de llamada server
                    await new Promise(r => setTimeout(r, 1800));

                    // si todo OK
                    showToast(`Descuento “${concepto}” (${id}) generado.`, 'success');

                } catch (err) {
                    showToast('Ocurrió un error al generar.', 'error');
                } finally {
                    btn.classList.remove('loading');
                }
            });
        });

        /* --- resto de scripts existentes (edición de % etc.) --- */
        document.querySelectorAll('#tablaDescuentos tbody tr').forEach(row => {
            const editBtn = row.querySelector('.edit-btn');
            if (!editBtn) return; // evita error si no existen botones de edición

            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            const viewSpan = row.querySelector('.percent-view');
            const input = row.querySelector('.percent-input');

            editBtn.onclick = () => {
                viewSpan.hidden = true;
                input.hidden = false;
                editBtn.hidden = true;
                saveBtn.hidden = false;
                cancelBtn.hidden = false;
                input.focus();
            };

            cancelBtn.onclick = () => {
                input.value = viewSpan.textContent.replace('%', '').trim();
                input.hidden = true;
                viewSpan.hidden = false;
                saveBtn.hidden = true;
                cancelBtn.hidden = true;
                editBtn.hidden = false;
            };

            saveBtn.onclick = async () => {
                const id = row.cells[0].textContent.trim();
                const val = parseFloat(input.value);
                if (isNaN(val) || val < 0) {
                    alert('Ingrese un porcentaje válido');
                    return;
                }

                /* --- ejemplo de guardado vía AJAX ---
                await fetch('actualizar_porcentaje.php', { ... });
                */

                viewSpan.textContent = val + ' %';
                input.hidden = true;
                viewSpan.hidden = false;
                saveBtn.hidden = true;
                cancelBtn.hidden = true;
                editBtn.hidden = false;
            };
        });
    </script>

    <!-- sidebar toggle -->
    <script src="../../../utils/sidebarToggle.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => initSidebarToggle());
    </script>
</body>

</html>