<?php
/*  opcional - para marcar la opción activa  */
$current = basename($_SERVER['SCRIPT_NAME']);  // ej: medicos.php

if (empty($GLOBALS['_sidebar_css_loaded'])) {
  echo '<link rel="stylesheet" href="assets/root_base.css">' . PHP_EOL;
    echo '<link rel="stylesheet" href="assets/sidebar.css">' . PHP_EOL;
    $GLOBALS['_sidebar_css_loaded'] = true;
}
?>
<nav class="sidebar">
   <h3 class="brand">Panel CMC</h3>

   <ul>
     <li><a href="obras_sociales.php" class="<?= $current==='obras_sociales.php'?'active':''?>">Obras Sociales</a></li>
     <li><a href="liquidacion_section.php"    class="<?= $current==='liquidacion_section.php'?'active':''?>">Liquidacion</a></li>
     <!-- <li><a href="descuentos_medicos.php" class="<?= $current==='descuentos_medicos.php'?'active':''?>">Médicos</a></li> -->
   </ul>
</nav>