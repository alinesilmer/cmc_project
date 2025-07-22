<aside class="sidebar">
  <div class="logo">
    <img src="/assets/images/logoCMC.png" alt="CMC Logo">
  </div>

  <nav class="nav-links">
    <a href="../dashboard/dashboard.php" class="nav-item">
      <span class="icon">🏠</span> Inicio
    </a>

    <a href="../obras_sociales/obras_sociales.php" class="nav-item">
      <span class="icon">📄</span> Facturación
    </a>

    <!-- === Liquidación (toggle) =================================== -->
    <button type="button" class="nav-item nav-parent" aria-expanded="false">
      <span class="icon">💰</span> Liquidación
      <span class="caret">▼</span>
    </button>

    <div class="sub-menu">
      <a href="../../liquidacion/listar_medicos/listar_medicos.php" class="sub-item">Lista de Médicos</a>
      <a href="../../liquidacion/periodo/periodo.php" class="sub-item">Periodo</a>
      <a href="../../liquidacion/descuentos/descuentos.php" class="sub-item">Descuentos</a>
    </div>

  </nav>
</aside>