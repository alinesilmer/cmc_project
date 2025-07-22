<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a su Tablero</title>
    <link href="../../../globals.css" rel="stylesheet">
    <link href="../sidebar/sidebar.css" rel="stylesheet">
    <link href="./dashboard.css" rel="stylesheet">
</head>

<body>
    <!-- ======== layout principal ======== -->
    <div class="layout">
        <?php include '../sidebar/sidebar.php'; ?>
        <div class="main-content">
            <div class="text-wrapper">
                <h2 class="title">Bienvenido a su Tablero</h2>
                <p class="instructions">Escoge una opción del menú lateral para iniciar</p>
            </div>
        </div>
    </div>
</body>
<script src="../../../utils/sidebarToggle.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initSidebarToggle();
    });
</script>

</html>