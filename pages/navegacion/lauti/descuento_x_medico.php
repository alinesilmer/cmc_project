<?php include 'sidebar.php'; ?>
<?php
session_start();

/* ───────── parámetros ───────── */
$nro = $_GET['nro_socio'] ?? '';
if($nro===''){ header('Location: medicos.php'); exit; }

/* ───────── leer servicios del JSON ───────── */
$servicios = json_decode(file_get_contents(__DIR__ . '/servicios_json.json'), true);
$servById  = [];
foreach($servicios as $s) $servById[$s['id']] = $s;

/* ───────── inicializar descuentos en sesión ───────── */
$_SESSION['descuentos'] = $_SESSION['descuentos'] ?? [];
$_SESSION['descuentos'][$nro] = $_SESSION['descuentos'][$nro] ?? [];

/* ───────── agregar descuento ───────── */
if($_SERVER['REQUEST_METHOD']==='POST'){
    $periodo = $_POST['periodo'] ?? '';
    $idServ  = (int)($_POST['servicio_id'] ?? 0);
    if($periodo && isset($servById[$idServ])){
        $_SESSION['descuentos'][$nro][] = [
            'periodo' => $periodo,
            'servicio_id' => $idServ
        ];
    }
}

/* ───────── obtener datos médico (solo nombre) ───────── */
try{
    $pdo = new PDO('mysql:host=localhost;dbname=colegio','usuario','clave',
                   [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $medico = $pdo->prepare("SELECT nombre FROM medicos WHERE nro_socio=?");
    $medico->execute([$nro]);
    $nombreMedico = $medico->fetchColumn() ?: 'Desconocido';
}catch(Exception $e){
    $nombreMedico = "Socio $nro";
}

/* ───────── descuentos actuales ───────── */
$hist = $_SESSION['descuentos'][$nro];
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Descuentos – <?=$nombreMedico?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
        <link rel="stylesheet" href="assets/descuento_x_medico.css">
    </head>
    <body>
        <main>
            <a href="descuentos_medicos.php" class="back">← Volver</a>  
            <h2>Descuentos de <?=$nombreMedico?> (Socio <?=$nro?>)</h2>

            <table>
            <thead><tr><th>Periodo</th><th>Concepto</th><th>%</th><th>Precio</th></tr></thead>
            <tbody>
            <?php if(!$hist): ?>
            <tr><td colspan="4" style="text-align:center">Sin registros</td></tr>
            <?php else:
                foreach($hist as $d):
                    $srv = $servById[$d['servicio_id']]??['nombre'=>'?','porcentaje'=>'?']; ?>
            <tr>
                <td><?=htmlspecialchars($d['periodo'])?></td>
                <td><?=htmlspecialchars($srv['nombre'])?></td>
                <td><?=htmlspecialchars($srv['porcentaje'])?></td>
                <td><?=htmlspecialchars($srv['precio'])?></td>

            </tr>
            <?php endforeach; endif;?>
            </tbody>
            </table>

            <form method="POST">
                <h3>Agregar Descuento</h3>
                <label>Periodo
                    <input type="month" name="periodo" required>
                </label>

                <label>Tipo de descuento
                    <select name="servicio_id" id="servicioSel" required></select>
                </label>

                <button>Guardar</button>
            </form>

        </main>
        

        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            /* construimos opciones con “[ID] Nombre” */
            const servicios = <?=json_encode($servicios,JSON_UNESCAPED_UNICODE)?>;
            const opts = servicios.map(s=>({
            id: s.id,
            text: `[${s.id}] ${s.nombre}`
            }));

            $('#servicioSel').select2({
            data: opts,
            width:'100%',
            placeholder:'Buscar concepto…',
            matcher: function(params, data){
                if($.trim(params.term)==='') return data;
                const term = params.term.toLowerCase();
                return data.text.toLowerCase().includes(term) ? data : null;
            }
            });
        </script>
    </body>
</html>