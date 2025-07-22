<?php
session_start();

/* ---------- conexión a BD SOLO para lista de médicos ---------- */
$pdo = new PDO('mysql:host=localhost;dbname=cmc_db','root','',
               [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

/* ---- TAB 2  · Médicos ---- */
$medicos = $pdo->query("SELECT nro_socio,nombre,matricula_prov,matricula_nac
                        FROM listado_medico ORDER BY nombre")
               ->fetchAll(PDO::FETCH_ASSOC);

/* ---- TAB 3 · Servicios (JSON editable) ---- */
$jsonPath = __DIR__.'/servicios_json.json';
$servicios = json_decode(file_get_contents($jsonPath), true);

/* guardar ediciones */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['saveServicios'])) {
    $servicios = $_POST['srv'];                  // array  id => [nombre,precio,porcentaje]
    /* normaliza y guarda en disco (opcional) */
    file_put_contents($jsonPath, json_encode(array_values($servicios),
                                             JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    $msg = "Servicios actualizados.";
}
?>
<!DOCTYPE html>
<html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Liquidación</title>
<link rel="stylesheet" href="assets/sidebar.css"><!-- tu sidebar externa -->
<style>
/* —— Tabs internos —— */
.nav-tabs{display:flex;gap:.3rem;border-bottom:2px solid #e0e0e0;margin-bottom:1.2rem}
.nav-tabs a{padding:.55rem 1.2rem;background:#e8efff;color:#3066be;text-decoration:none;
            border-radius:.35rem .35rem 0 0}
.nav-tabs a.active{background:#3066be;color:#fff;font-weight:600}
.tab-pane{display:none} .tab-pane.active{display:block}

/* —— tablas generales —— */
table{border-collapse:collapse;width:100%;max-width:1000px;background:#fff}
th,td{padding:.55rem 1rem;border:1px solid #dfe3ee}
th{background:#3066be;color:#fff;text-align:left}

/* —— botón —— */
.btn{padding:.45rem .9rem;background:#3066be;color:#fff;border:none;border-radius:.3rem;cursor:pointer}
.btn:hover{background:#5680d2}
input[type=text],input[type=number]{width:100%;border:1px solid #ccc;border-radius:.25rem;padding:.3rem .4rem}
.tbl-resumen{border-collapse:collapse;width:100%;max-width:900px;background:#fff;margin-top:.8rem}
.tbl-resumen th,.tbl-resumen td{padding:.55rem .9rem;border:1px solid #dfe3ee}
.tbl-resumen th{background:#3066be;color:#fff;text-align:left}
.tbl-resumen .num{text-align:right;font-variant-numeric:tabular-nums}
.tbl-resumen .term{color:#3aa85f;font-weight:600}   /* finalizado */
.tbl-resumen .cur{color:#c77c07;font-weight:600}    /* en curso  */
</style>
</head>

<body>
<?php include 'sidebar.php'; ?>
<div class="main"><!-- contenido desplazado por sidebar -->

<h2>Liquidación</h2>

<!-- ------------- nav interna ---------------- -->
<nav class="nav-tabs">
  <a href="#tab-resumenes"   class="tab-link active">Resúmenes</a>
  <a href="#tab-medicos"     class="tab-link">Listado de médicos</a>
  <a href="#tab-config"      class="tab-link">Configuración</a>
</nav>

<!-- ------------- TAB 1  --------------------- -->
<div class="tab-pane active" id="tab-resumenes">
  <h3>Períodos de liquidación</h3>

  <table class="tbl-resumen">
    <thead>
      <tr>
        <th>Periodo</th>
        <th>Estado</th>
        <th>Monto bruto $</th>
        <th>Descuentos $</th>
        <th>Monto neto $</th>
      </tr>
    </thead>
    <tbody>
      <!-- ejemplos fijos -->
       <tr>
        <td>Junio 2025</td>
        <td class="cur">En curso</td>
        <td class="num">—</td>
        <td class="num">—</td>
        <td class="num">—</td>
      </tr>
      <tr>
        <td>Mayo 2025</td>
        <td class="term">Finalizado</td>
        <td class="num">1 380 000</td>
        <td class="num">-95 300</td>
        <td class="num">1 284 700</td>
      </tr>
      <tr>
        <td>Abril 2025</td>
        <td class="term">Finalizado</td>
        <td class="num">1 250 000</td>
        <td class="num">-87 500</td>
        <td class="num">1 162 500</td>
      </tr>
      
    </tbody>
  </table>
</div>

<!-- ------------- TAB 2  --------------------- -->
<div class="tab-pane" id="tab-medicos">
  <h3>Listado de médicos</h3>
  <input id="buscarMed" placeholder="Buscar médico…" style="margin:.8rem 0;width:320px;padding:.4rem">
  <table id="tablaMed">
    <thead><tr>
     <th>Nro Socio</th><th>Nombre</th><th>Matrícula Prov.</th><th>Matrícula Nac.</th><th>Acciones</th>
    </tr></thead><tbody>
    <?php foreach($medicos as $m): ?>
      <tr>
        <td><?=$m['nro_socio']?></td>
        <td><?=htmlspecialchars($m['nombre'])?></td>
        <td><?=htmlspecialchars($m['matricula_prov'])?></td>
        <td><?=htmlspecialchars($m['matricula_nac'])?></td>
        <td><a class="btn" href="descuento_x_medico.php?nro_socio=<?=$m['nro_socio']?>">Descuentos</a></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
</div>

<!-- ------------- TAB 3  --------------------- -->
<div class="tab-pane" id="tab-config">
  <h3>Servicios (editable)</h3>
  <?php if(!empty($msg)) echo "<p style='color:green'>$msg</p>"; ?>

  <form method="POST">
   <table>
     <thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>%</th></tr></thead><tbody>
     <?php foreach($servicios as $srv): ?>
        <tr>
          <td><input type="text"   name="srv[<?=$srv['id']?>][id]"        value="<?=$srv['id']?>"        readonly></td>
          <td><input type="text"   name="srv[<?=$srv['id']?>][nombre]"    value="<?=htmlspecialchars($srv['nombre'])?>"></td>
          <td><input type="number" step="0.01" name="srv[<?=$srv['id']?>][precio]"     value="<?=$srv['precio']?>"></td>
          <td><input type="number" step="0.01" name="srv[<?=$srv['id']?>][porcentaje]" value="<?=$srv['porcentaje']?>"></td>
        </tr>
     <?php endforeach;?>
     </tbody>
   </table>
   <button class="btn mt-2" name="saveServicios">Guardar cambios</button>
  </form>
</div>

</div><!-- /.main -->

<script>
/* —— tabs —— */
document.querySelectorAll('.tab-link').forEach(link=>{
   link.onclick = e=>{
      e.preventDefault();
      document.querySelectorAll('.tab-link').forEach(l=>l.classList.remove('active'));
      link.classList.add('active');
      document.querySelectorAll('.tab-pane').forEach(p=>p.classList.remove('active'));
      document.querySelector(link.getAttribute('href')).classList.add('active');
   };
});

/* —— búsqueda médicos —— */
const buscar   = document.getElementById('buscarMed');
if(buscar){
  const filas = Array.from(document.querySelectorAll('#tablaMed tbody tr'));
  buscar.oninput = e=>{
     const q = e.target.value.toLowerCase();
     filas.forEach(f=>{
        f.style.display = f.textContent.toLowerCase().includes(q) ? '' : 'none';
     });
  };
}
</script>
</body></html>