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
            $filtro = "v.fecha = CURDATE()";
            break;
        case 'semana':
            $filtro = "v.fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) AND CURDATE()";
            break;
        case 'mes':
            $filtro = "v.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND CURDATE()";
            break;
        case 'anio':
            $filtro = "v.fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-01-01') AND CURDATE()";
            break;
        case 'personalizado':
            if ($fecha_inicio && $fecha_fin) {
                $filtro = "v.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            } else {
                $filtro = "v.fecha = CURDATE()";
            }
            break;
        default:
            $filtro = "v.fecha = CURDATE()";
    }
    
    $datos = [];
    
    // 1. CONSULTA DE RESUMEN
    $query_resumen = "SELECT 
                COUNT(DISTINCT v.idVenta) as total_ventas,
                COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) + 
                COALESCE(SUM(p.senia), 0) as total_ingresos
              FROM ventas v
              LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
              LEFT JOIN pedidos p ON v.idCliente = p.idCliente AND p.fecha = v.fecha
              WHERE $filtro";
    
    $result = $conexion->query($query_resumen);
    $datos['resumen'] = $result ? $result->fetch_assoc() : ['total_ventas' => 0, 'total_ingresos' => 0];
    
    // 2. Productos más vendidos (top 10)
    $query_productos = "SELECT 
                p.nombre as producto,
                COALESCE(SUM(dv.cantidad), 0) as cantidad
              FROM productos p
              LEFT JOIN detalle_venta dv ON p.idProducto = dv.idProducto
              LEFT JOIN ventas v ON dv.idVenta = v.idVenta AND $filtro
              GROUP BY p.idProducto
              HAVING cantidad > 0
              ORDER BY cantidad DESC
              LIMIT 10";
    
    $result = $conexion->query($query_productos);
    $datos['productos'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['productos'][] = $row;
        }
    }
    
    // 3. Clientes destacados (top 10)
    $query_clientes = "SELECT 
                CONCAT(c.nombre, ' ', c.apellido) as cliente,
                COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) + 
                COALESCE(SUM(p.senia), 0) as monto_gastado
              FROM clientes c
              LEFT JOIN ventas v ON c.idCliente = v.idCliente AND $filtro
              LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
              LEFT JOIN pedidos p ON c.idCliente = p.idCliente AND p.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
              GROUP BY c.idCliente
              HAVING monto_gastado > 0
              ORDER BY monto_gastado DESC
              LIMIT 10";
    
    $result = $conexion->query($query_clientes);
    $datos['clientes'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['clientes'][] = $row;
        }
    }
    
    // 4. Rendimiento por empleado
    $query_empleados = "SELECT 
                CONCAT(u.nombre, ' ', u.apellido) as empleado,
                COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) as monto_generado
              FROM usuarios u
              LEFT JOIN ventas v ON u.id = v.idUsuario AND $filtro
              LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
              GROUP BY u.id
              HAVING monto_generado > 0
              ORDER BY monto_generado DESC";
    
    $result = $conexion->query($query_empleados);
    $datos['empleados'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['empleados'][] = $row;
        }
    }
    
    // 5. Ventas por día
    $query_dias = "SELECT 
                v.fecha,
                COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) as monto_total
              FROM ventas v
              LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
              WHERE $filtro
              GROUP BY v.fecha
              ORDER BY v.fecha";
    
    $result = $conexion->query($query_dias);
    $datos['dias'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['dias'][] = $row;
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
    <title>Reporte de Ventas</title>
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
    <h1 class="main-title">Reporte de Ventas</h1>
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
            <div class="card-title">Total de Ventas</div>
            <div style="font-size: 18px; text-align: center;"><?= $datos['resumen']['total_ventas'] ?? '0' ?></div>
        </div>
    </div>
    <div class="flex-item">
        <div class="card">
            <div class="card-title">Total de Ingresos</div>
            <div style="font-size: 18px; text-align: center;">$<?= number_format($datos['resumen']['total_ingresos'] ?? 0, 2) ?></div>
        </div>
    </div>
</div>

<?php if (!empty($datos['productos'])): ?>
<h2>Productos Más Vendidos</h2>
<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_productos = array_sum(array_column($datos['productos'], 'cantidad'));
        foreach ($datos['productos'] as $producto): 
            $porcentaje = $total_productos > 0 ? round(($producto['cantidad'] / $total_productos) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($producto['producto']) ?></td>
            <td class="text-center"><?= $producto['cantidad'] ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong><?= $total_productos ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de productos vendidos</div>
<?php endif; ?>

<?php if (!empty($datos['clientes'])): ?>
<h2>Clientes Destacados</h2>
<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th class="text-center">Monto Gastado ($)</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_clientes = array_sum(array_column($datos['clientes'], 'monto_gastado'));
        foreach ($datos['clientes'] as $cliente): 
            $porcentaje = $total_clientes > 0 ? round(($cliente['monto_gastado'] / $total_clientes) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($cliente['cliente']) ?></td>
            <td class="text-center">$<?= number_format($cliente['monto_gastado'], 2) ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong>$<?= number_format($total_clientes, 2) ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de clientes destacados</div>
<?php endif; ?>

<?php if (!empty($datos['empleados'])): ?>
<h2>Rendimiento por Empleado</h2>
<table>
    <thead>
        <tr>
            <th>Empleado</th>
            <th class="text-center">Monto Generado ($)</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_empleados = array_sum(array_column($datos['empleados'], 'monto_generado'));
        foreach ($datos['empleados'] as $empleado): 
            $porcentaje = $total_empleados > 0 ? round(($empleado['monto_generado'] / $total_empleados) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($empleado['empleado']) ?></td>
            <td class="text-center">$<?= number_format($empleado['monto_generado'], 2) ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong>$<?= number_format($total_empleados, 2) ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de rendimiento por empleado</div>
<?php endif; ?>

<?php if (!empty($datos['dias'])): ?>
<h2>Ventas por Día</h2>
<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th class="text-center">Monto ($)</th>
            <th class="text-center">Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total_dias = array_sum(array_column($datos['dias'], 'monto_total'));
        foreach ($datos['dias'] as $dia): 
            $porcentaje = $total_dias > 0 ? round(($dia['monto_total'] / $total_dias) * 100, 2) : 0;
        ?>
        <tr>
            <td><?= date('d/m/Y', strtotime($dia['fecha'])) ?></td>
            <td class="text-center">$<?= number_format($dia['monto_total'], 2) ?></td>
            <td class="text-center"><?= $porcentaje ?>%</td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong>$<?= number_format($total_dias, 2) ?></strong></td>
            <td class="text-center">100%</td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-data">No hay datos de ventas por día</div>
<?php endif; ?>

<div class="footer">
    <p>Reporte generado automáticamente por el sistema de gestión de ventas.</p>
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
$nombreArchivo = 'reporte_ventas_' . date('Y-m-d_His') . '.pdf';

// Enviar PDF al navegador
$dompdf->stream($nombreArchivo, array("Attachment" => true));