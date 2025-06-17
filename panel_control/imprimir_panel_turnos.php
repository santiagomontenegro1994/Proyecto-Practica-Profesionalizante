<?php
session_start();

if (empty($_SESSION['Usuario_Nombre'])) {
    header('Location: ../inicio/cerrarsesion.php');
    exit;
}

require_once '../funciones/conexion.php';
require_once '../libreria/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Obtener parámetros del POST
$periodo = $_POST['periodo'] ?? 'Hoy';
$fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d', strtotime('-1 month'));
$fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');

// Conexión a la base de datos con verificación
$MiConexion = ConexionBD();
if (!$MiConexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Obtener datos para el reporte
$datos = obtenerDatosReporte($MiConexion, $periodo, $fecha_inicio, $fecha_fin);

// Iniciar buffer de salida
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Turnos</title>
    <style>
        @page { margin: 10px; }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 10px;
            color: #333;
        }
        .header-section {
            text-align: center;
            border-bottom: 2px solid #316B70;
            margin-bottom: 15px;
        }
        .logo {
            max-width: 100px;
            display: block;
            margin: 0 auto 10px;
        }
        .main-title {
            font-size: 20px;
            color: #316B70;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 12px;
            color: #6c757d;
            margin-top: 0;
        }
        h2 {
            text-align: center;
            font-size: 16px;
            margin: 10px 0;
        }
        .info-box {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
        }
        .info-item {
            flex: 1;
            padding-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .card-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #316B70;
        }
        .flex-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .flex-item {
            width: 48%;
            margin-bottom: 15px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="header-section">
    <?php
    $ruta_imagen = '../assets/img/logo-salon.png';
    if (file_exists($ruta_imagen)) {
        $tipo_imagen = pathinfo($ruta_imagen, PATHINFO_EXTENSION);
        $datos_imagen = file_get_contents($ruta_imagen);
        $base64_imagen = 'data:image/' . $tipo_imagen . ';base64,' . base64_encode($datos_imagen);
    } else {
        $base64_imagen = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><text x="50%" y="50%" text-anchor="middle" fill="#316B70" font-size="14">Pelucan</text></svg>');
    }
    ?>
    <img src="<?= $base64_imagen ?>" class="logo" alt="Logo">
    <h1 class="main-title">Reporte de Turnos</h1>
    <p class="subtitle">Generado el: <?= date('d/m/Y H:i:s') ?></p>
</div>

<div class="info-box">
    <div class="info-item"><strong>Período:</strong> <?= htmlspecialchars($periodo) ?></div>
    <?php if (!empty($fecha_inicio) && !empty($fecha_fin)): ?>
    <div class="info-item"><strong>Rango:</strong> <?= htmlspecialchars("$fecha_inicio al $fecha_fin") ?></div>
    <?php endif; ?>
</div>

<div class="flex-container">
    <div class="flex-item">
        <div class="card">
            <div class="card-title">Total de Turnos</div>
            <div style="font-size: 18px; text-align: center;"><?= $datos['resumen']['total_turnos'] ?? '0' ?></div>
        </div>
    </div>
    <div class="flex-item">
        <div class="card">
            <div class="card-title">Total de Ingresos</div>
            <div style="font-size: 18px; text-align: center;">$<?= number_format($datos['resumen']['total_ingresos'] ?? 0, 2) ?></div>
        </div>
    </div>
</div>

<?php if (!empty($datos['estados'])): ?>
<h2>Turnos por Estado</h2>
<table>
    <thead>
        <tr>
            <th>Estado</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_estados = array_sum(array_column($datos['estados'], 'cantidad'));
        foreach ($datos['estados'] as $estado): 
            $porcentaje = $total_estados > 0 ? round(($estado['cantidad'] / $total_estados) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($estado['estado']) ?></td>
            <td class="text-center"><?= $estado['cantidad'] ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong><?= $total_estados ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de turnos por estado</div>
<?php endif; ?>

<?php if (!empty($datos['estilistas'])): ?>
<h2>Turnos por Estilista</h2>
<table>
    <thead>
        <tr>
            <th>Estilista</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_estilistas = array_sum(array_column($datos['estilistas'], 'cantidad'));
        foreach ($datos['estilistas'] as $estilista): 
            $porcentaje = $total_estilistas > 0 ? round(($estilista['cantidad'] / $total_estilistas) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($estilista['estilista']) ?></td>
            <td class="text-center"><?= $estilista['cantidad'] ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong><?= $total_estilistas ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de turnos por estilista</div>
<?php endif; ?>

<?php if (!empty($datos['horarios'])): ?>
<h2>Turnos por Franja Horaria</h2>
<table>
    <thead>
        <tr>
            <th>Franja Horaria</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_horarios = array_sum(array_column($datos['horarios'], 'cantidad'));
        foreach ($datos['horarios'] as $horario): 
            $porcentaje = $total_horarios > 0 ? round(($horario['cantidad'] / $total_horarios) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($horario['franja']) ?></td>
            <td class="text-center"><?= $horario['cantidad'] ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong><?= $total_horarios ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de turnos por franja horaria</div>
<?php endif; ?>

<div class="footer">
    <p>Reporte generado automáticamente por el sistema de gestión de turnos.</p>
</div>

</body>
</html>

<?php
$html = ob_get_clean();

// Configurar DOMPDF
$dompdf = new Dompdf();
$options = $dompdf->getOptions();
$options->set([
    'isRemoteEnabled' => true,
    'isHtml5ParserEnabled' => true,
    'isPhpEnabled' => true
]);
$dompdf->setOptions($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Generar nombre del archivo
$nombreArchivo = 'reporte_turnos_' . date('Y-m-d_His') . '.pdf';

// Enviar PDF al navegador
$dompdf->stream($nombreArchivo, array("Attachment" => true));

function obtenerDatosReporte($conexion, $periodo, $fecha_inicio, $fecha_fin) {
    // Configurar filtro según período
    switch($periodo) {
        case 'Hoy':
            $filtro = "turnos.Fecha = CURDATE()";
            break;
        case 'Esta semana':
            $filtro = "turnos.Fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND CURDATE()";
            break;
        case 'Este mes':
            $filtro = "turnos.Fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND CURDATE()";
            break;
        case 'Personalizado':
            if ($fecha_inicio && $fecha_fin) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || 
                    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
                    return ['error' => 'Formato de fecha inválido'];
                }
                $filtro = "turnos.Fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            } else {
                $filtro = "turnos.Fecha = CURDATE()";
            }
            break;
        default:
            $filtro = "turnos.Fecha = CURDATE()";
    }
    
    $datos = [];
    
    // 1. CONSULTA DE RESUMEN - USANDO ALIAS CORRECTAMENTE
    $query_resumen = "SELECT 
                COUNT(DISTINCT turnos.IdTurno) as total_turnos,
                COALESCE(SUM(ts.precio), 0) as total_ingresos
              FROM turnos
              LEFT JOIN detalle_turno dt ON turnos.IdTurno = dt.idTurno
              LEFT JOIN tipo_servicio ts ON dt.idTipoServicio = ts.IdTipoServicio
              WHERE $filtro";
    
    $result = $conexion->query($query_resumen);
    if ($result) {
        $datos['resumen'] = $result->fetch_assoc();
    } else {
        $datos['resumen'] = ['total_turnos' => 0, 'total_ingresos' => 0];
        error_log("Error en consulta resumen: " . $conexion->error);
    }
    
    // 2. Turnos por estado
    $query_estados = "SELECT 
                e.Denominacion as estado,
                COUNT(turnos.IdTurno) as cantidad
              FROM turnos
              JOIN estado e ON turnos.IdEstado = e.IdEstado
              WHERE $filtro
              GROUP BY e.Denominacion";
    
    $result = $conexion->query($query_estados);
    $datos['estados'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['estados'][] = $row;
        }
    }
    
    // 3. Turnos por estilista
    $query_estilistas = "SELECT 
                CONCAT(es.Nombre, ' ', es.Apellido) as estilista,
                COUNT(turnos.IdTurno) as cantidad
              FROM turnos
              JOIN estilista es ON turnos.IdEstilista = es.IdEstilista
              WHERE $filtro
              GROUP BY es.IdEstilista
              ORDER BY cantidad DESC";
    
    $result = $conexion->query($query_estilistas);
    $datos['estilistas'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['estilistas'][] = $row;
        }
    }
    
    // 4. Turnos por franja horaria
    $query_horarios = "SELECT 
                CASE 
                  WHEN TIME(turnos.Horario) BETWEEN '09:00:00' AND '11:59:59' THEN 'Mañana (9-12)'
                  WHEN TIME(turnos.Horario) BETWEEN '12:00:00' AND '14:59:59' THEN 'Mediodía (12-15)'
                  WHEN TIME(turnos.Horario) BETWEEN '15:00:00' AND '17:59:59' THEN 'Tarde (15-18)'
                  WHEN TIME(turnos.Horario) BETWEEN '18:00:00' AND '20:59:59' THEN 'Noche (18-21)'
                  ELSE 'Otro horario'
                END as franja,
                COUNT(turnos.IdTurno) as cantidad
              FROM turnos
              WHERE $filtro
              GROUP BY franja
              ORDER BY 
                CASE franja
                  WHEN 'Mañana (9-12)' THEN 1
                  WHEN 'Mediodía (12-15)' THEN 2
                  WHEN 'Tarde (15-18)' THEN 3
                  WHEN 'Noche (18-21)' THEN 4
                  ELSE 5
                END";
    
    $result = $conexion->query($query_horarios);
    $datos['horarios'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['horarios'][] = $row;
        }
    }
    
    return $datos;
}