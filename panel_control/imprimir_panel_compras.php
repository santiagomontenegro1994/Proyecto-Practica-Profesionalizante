<?php
session_start();

// Verificar sesión y permisos
if (empty($_SESSION['Usuario_Nombre'])) {
    die(json_encode(['error' => 'Acceso no autorizado']));
}

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/pdf_errors.log');

require_once '../funciones/conexion.php';
require_once '../libreria/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Obtener parámetros del POST
$periodo = $_POST['periodo'] ?? 'hoy';
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;

// Mapear períodos a textos legibles
$periodoTextos = [
    'hoy' => 'Hoy',
    'semana' => 'Esta semana',
    'mes' => 'Este mes',
    'anio' => 'Este año',
    'personalizado' => 'Personalizado'
];

$periodo_titulo = $periodoTextos[$periodo] ?? 'Personalizado';

// Conexión a la base de datos con verificación
$MiConexion = ConexionBD();
if (!$MiConexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Obtener datos para el reporte
function obtenerDatosReporte($conexion, $periodo, $fecha_inicio, $fecha_fin) {
    // Configurar filtro según período
    switch($periodo) {
        case 'hoy':
            $filtro = "oc.fecha = CURDATE()";
            break;
        case 'semana':
            $filtro = "oc.fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND CURDATE()";
            break;
        case 'mes':
            $filtro = "oc.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND CURDATE()";
            break;
        case 'anio':
            $filtro = "oc.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-01-01') AND CURDATE()";
            break;
        case 'personalizado':
            if ($fecha_inicio && $fecha_fin) {
                $filtro = "oc.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            } else {
                $filtro = "oc.fecha = CURDATE()";
            }
            break;
        default:
            $filtro = "oc.fecha = CURDATE()";
    }
    
    $datos = [];
    
    // 1. CONSULTA DE RESUMEN
    $query_resumen = "SELECT 
                COUNT(DISTINCT oc.idOrdenCompra) as total_compras,
                COALESCE(SUM(doc.cantidad * doc.precio), 0) as total_gastos
              FROM orden_compra oc
              LEFT JOIN detalle_orden_compra doc ON oc.idOrdenCompra = doc.idOrdenCompra
              WHERE $filtro";
    
    $result = $conexion->query($query_resumen);
    $datos['resumen'] = $result ? $result->fetch_assoc() : ['total_compras' => 0, 'total_gastos' => 0];
    
    // 2. Artículos más comprados (top 10)
    $query_articulos = "SELECT 
                p.nombre as articulo,
                SUM(doc.cantidad) as cantidad
              FROM productos p
              JOIN detalle_orden_compra doc ON p.idProducto = doc.idArticulo
              JOIN orden_compra oc ON doc.idOrdenCompra = oc.idOrdenCompra
              WHERE $filtro
              GROUP BY p.idProducto
              ORDER BY cantidad DESC
              LIMIT 10";
    
    $result = $conexion->query($query_articulos);
    $datos['articulos'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['articulos'][] = $row;
        }
    }
    
    // 3. Compras por proveedor
    $query_proveedores = "SELECT 
                pr.razon_social as proveedor,
                SUM(doc.cantidad * doc.precio) as monto_total
              FROM proveedores pr
              JOIN orden_compra oc ON pr.idProveedor = oc.idProveedor
              JOIN detalle_orden_compra doc ON oc.idOrdenCompra = doc.idOrdenCompra
              WHERE $filtro
              GROUP BY pr.idProveedor
              ORDER BY monto_total DESC";
    
    $result = $conexion->query($query_proveedores);
    $datos['proveedores'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['proveedores'][] = $row;
        }
    }
    
    // 4. Evolución de compras
    $query_evolucion = "SELECT 
                oc.fecha,
                SUM(doc.cantidad) as cantidad_total
              FROM orden_compra oc
              JOIN detalle_orden_compra doc ON oc.idOrdenCompra = doc.idOrdenCompra
              WHERE $filtro
              GROUP BY oc.fecha
              ORDER BY oc.fecha";
    
    $result = $conexion->query($query_evolucion);
    $datos['evolucion'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['evolucion'][] = $row;
        }
    }
    
    // 5. Frecuencia de compras por proveedor
    $query_frecuencia = "SELECT 
                pr.razon_social as proveedor,
                COUNT(DISTINCT oc.idOrdenCompra) as frecuencia
              FROM proveedores pr
              JOIN orden_compra oc ON pr.idProveedor = oc.idProveedor
              WHERE $filtro
              GROUP BY pr.idProveedor
              ORDER BY frecuencia DESC";
    
    $result = $conexion->query($query_frecuencia);
    $datos['frecuencia'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['frecuencia'][] = $row;
        }
    }
    
    return $datos;
}

// Obtener datos
$datos = obtenerDatosReporte($MiConexion, $periodo, $fecha_inicio, $fecha_fin);

// Cerrar conexión
$MiConexion->close();

// Iniciar buffer de salida
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Compras</title>
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
    <h1 class="main-title">Reporte de Compras</h1>
    <p class="subtitle">Generado el: <?= date('d/m/Y H:i:s') ?></p>
</div>

<div class="info-box">
    <div class="info-item"><strong>Período:</strong> <?= htmlspecialchars($periodo_titulo) ?></div>
    <?php if (!empty($fecha_inicio) && !empty($fecha_fin)): ?>
    <div class="info-item"><strong>Rango:</strong> <?= htmlspecialchars("$fecha_inicio al $fecha_fin") ?></div>
    <?php endif; ?>
</div>

<div class="flex-container">
    <div class="flex-item">
        <div class="card">
            <div class="card-title">Total de Compras</div>
            <div style="font-size: 18px; text-align: center;"><?= $datos['resumen']['total_compras'] ?? '0' ?></div>
        </div>
    </div>
    <div class="flex-item">
        <div class="card">
            <div class="card-title">Total de Gastos</div>
            <div style="font-size: 18px; text-align: center;">$<?= number_format($datos['resumen']['total_gastos'] ?? 0, 2) ?></div>
        </div>
    </div>
</div>

<?php if (!empty($datos['articulos'])): ?>
<h2>Artículos Más Comprados</h2>
<table>
    <thead>
        <tr>
            <th>Artículo</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_articulos = array_sum(array_column($datos['articulos'], 'cantidad'));
        foreach ($datos['articulos'] as $articulo): 
            $porcentaje = $total_articulos > 0 ? round(($articulo['cantidad'] / $total_articulos) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($articulo['articulo']) ?></td>
            <td class="text-center"><?= $articulo['cantidad'] ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong><?= $total_articulos ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de artículos comprados</div>
<?php endif; ?>

<?php if (!empty($datos['proveedores'])): ?>
<h2>Compras por Proveedor</h2>
<table>
    <thead>
        <tr>
            <th>Proveedor</th>
            <th class="text-center">Monto ($)</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_proveedores = array_sum(array_column($datos['proveedores'], 'monto_total'));
        foreach ($datos['proveedores'] as $proveedor): 
            $porcentaje = $total_proveedores > 0 ? round(($proveedor['monto_total'] / $total_proveedores) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($proveedor['proveedor']) ?></td>
            <td class="text-center">$<?= number_format($proveedor['monto_total'], 2) ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong>$<?= number_format($total_proveedores, 2) ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de compras por proveedor</div>
<?php endif; ?>

<?php if (!empty($datos['evolucion'])): ?>
<h2>Evolución de Compras</h2>
<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_evolucion = array_sum(array_column($datos['evolucion'], 'cantidad_total'));
        foreach ($datos['evolucion'] as $dia): 
            $porcentaje = $total_evolucion > 0 ? round(($dia['cantidad_total'] / $total_evolucion) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= date('d/m/Y', strtotime($dia['fecha'])) ?></td>
            <td class="text-center"><?= $dia['cantidad_total'] ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong><?= $total_evolucion ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de evolución de compras</div>
<?php endif; ?>

<?php if (!empty($datos['frecuencia'])): ?>
<h2>Frecuencia de Compras por Proveedor</h2>
<table>
    <thead>
        <tr>
            <th>Proveedor</th>
            <th class="text-center">Número de Compras</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_frecuencia = array_sum(array_column($datos['frecuencia'], 'frecuencia'));
        foreach ($datos['frecuencia'] as $frec): 
            $porcentaje = $total_frecuencia > 0 ? round(($frec['frecuencia'] / $total_frecuencia) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($frec['proveedor']) ?></td>
            <td class="text-center"><?= $frec['frecuencia'] ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong><?= $total_frecuencia ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de frecuencia de compras</div>
<?php endif; ?>

<div class="footer">
    <p>Reporte generado automáticamente por el sistema de gestión de compras.</p>
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
$nombreArchivo = 'reporte_compras_' . date('Y-m-d_His') . '.pdf';

// Enviar PDF al navegador
$dompdf->stream($nombreArchivo, array("Attachment" => true));