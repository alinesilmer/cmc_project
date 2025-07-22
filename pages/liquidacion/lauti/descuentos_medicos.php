<?php include 'sidebar.php'; ?>
<?php
/* 1)  Conexión PDO  ─ ajusta credenciales ─ */
$pdo = new PDO('mysql:host=localhost;dbname=cmc_db',
               'root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

/* 2)  Médicos */
$medicos = $pdo->query("
    SELECT nro_socio, nombre, matricula_prov, matricula_nac
    FROM listado_medico ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Médicos</title>
    <style>
    body{font-family:Arial,Helvetica,sans-serif;margin:0;background:#f5f7fb}
    h2{text-align:center;margin-bottom:1.5rem}
    #search{display:block;margin:0 auto 1rem;width:100%;max-width:350px;padding:.45rem .8rem;
            border:1px solid #ccc;border-radius:.35rem}
    table{border-collapse:collapse;width:100%;max-width:1000px;margin:auto;background:#fff}
    th,td{padding:.55rem 1rem;border:1px solid #dfe3ee}
    th{background:#3066be;color:#fff;text-align:left}
    a.btn{padding:.35rem .7rem;background:#3066be;color:#fff;text-decoration:none;border-radius:.3rem}
    a.btn:hover{background:#5680d2}
    .hidden{display:none}
    </style>
</head>
<body>
    <main>
        <h2>Listado de Médicos</h2>
        <input id="search" placeholder="Buscar por socio, nombre o matrícula…">
    
        <table id="tablaMedicos">
        <thead><tr>
            <th>Nro Socio</th><th>Nombre</th><th>Matrícula Prov.</th><th>Matrícula Nac.</th><th>Acciones</th>
        </tr></thead>
        <tbody>
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

    </main>

<script>
const buscador = document.getElementById('search');
const filas = Array.from(document.querySelectorAll('#tablaMedicos tbody tr'));

buscador.addEventListener('input', e=>{
  const q = e.target.value.toLowerCase().trim();
  filas.forEach(f=>{
      const texto = f.textContent.toLowerCase();
      f.classList.toggle('hidden', !texto.includes(q));
  });
});
</script>
</body>
</html>