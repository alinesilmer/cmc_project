<?php session_start(); ?>
<?php
$phone = '5493794581224';
$msg   = urlencode('¡Hola! Necesito soporte con el sistema.');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bienvenido a su Tablero</title>

    <link href="../../../globals.css" rel="stylesheet" />
    <link href="../sidebar/sidebar.css" rel="stylesheet" />
    <link href="./dashboard.css" rel="stylesheet" />

</head>

<body>
    <div class="layout">
        <?php include '../sidebar/sidebar.php'; ?>

        <main class="main-content">
            <!-- decorativos -->
            <div class="bg-blob blob-1"></div>
            <div class="bg-blob blob-2"></div>

            <!-- bienvenida -->
            <section class="welcome-card reveal">
                <h2 class="title">¡Bienvenido<?php echo isset($_SESSION['nombre']) ? ", " . $_SESSION['nombre'] : ""; ?>!</h2>
                <p class="subtitle">Elegí una opción del menú lateral o usa los accesos rápidos.</p>
            </section>

            <!-- KPIs -->
            <section class="kpis reveal" aria-label="Indicadores clave">
                <article class="kpi-card">
                    <span class="kpi-label">Período Activo</span>
                    <span class="kpi-value" data-count="202507">202507</span>
                </article>
                <article class="kpi-card">
                    <span class="kpi-label">Estado del Período</span>
                    <span class="label">No Liquidado</span>
                </article>
                <article class="kpi-card">
                    <span class="kpi-label">Cierre del Período</span>
                    <span class="label">30/07/2025</span>
                </article>
            </section>

            <!-- accesos rápidos -->
            <section class="quick-actions reveal" aria-label="Accesos rápidos">
                <a href="../listar_medicos/listar_medicos.php" class="qa-card">
                    <div class="qa-icon">📁</div>
                    <span class="action-title">Lista de Médicos</span>
                </a>
                <a href="../obras_sociales/obras_sociales.php" class="qa-card">
                    <div class="qa-icon">📄</div>
                    <span class="action-title">Débitos de Obra Social</span>
                </a>
                <a href="../periodo/periodo.php" class="qa-card">
                    <div class="qa-icon">🗓️</div>
                    <span class="action-title">Lista de Períodos</span>
                </a>
                <a href="../descuentos/descuentos.php" class="qa-card">
                    <div class="qa-icon">💰</div>
                    <span class="action-title">Cargar Débitos</span>
                </a>
                <a href="https://wa.me/<?= $phone ?>?text=<?= $msg ?>"
                    class="qa-card" target="_blank" rel="noopener">
                    <div class="qa-icon">💬</div>
                    <span class="action-title">Soporte</span>
                </a>
            </section>
        </main>
    </div>

    <script src="../../../utils/sidebarToggle.js"></script>
    <script src="../../../utils/dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initSidebarToggle();
        });
    </script>
</body>

</html>