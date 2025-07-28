<?php
// Configuración inicial
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/recommendations_compras_errors.log');

header('Content-Type: application/json; charset=utf-8');

function jsonError($message, $code = 500) {
    http_response_code($code);
    die(json_encode(['error' => true, 'message' => $message, 'code' => $code]));
}

// Validar método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Método no permitido', 405);
}

// Validar y sanitizar parámetros
$periodo = isset($_GET['periodo']) ? trim($_GET['periodo']) : 'hoy';
$allowedPeriods = ['hoy', 'semana', 'mes', 'anio', 'personalizado'];
if (!in_array($periodo, $allowedPeriods)) {
    jsonError('Período no válido. Valores permitidos: ' . implode(', ', $allowedPeriods), 400);
}

// Validar fechas para período personalizado
$fechaInicio = null;
$fechaFin = null;

if ($periodo === 'personalizado') {
    $fechaInicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : null;
    $fechaFin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : null;
    
    if (!$fechaInicio || !$fechaFin) {
        jsonError('Fechas personalizadas requeridas para este período', 400);
    }
    
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin)) {
        jsonError('Formato de fecha inválido. Use YYYY-MM-DD', 400);
    }
    
    if (strtotime($fechaInicio) > strtotime($fechaFin)) {
        jsonError('La fecha de inicio no puede ser mayor a la fecha fin', 400);
    }
}

// Incluir conexión y helper
require_once realpath(__DIR__ . '/..') . '/funciones/conexion.php';
require_once realpath(__DIR__ . '/..') . '/funciones/openai_helper_compras.php';

try {
    // Conectar a la base de datos
    $conexion = ConexionBD();
    if (!$conexion) {
        throw new Exception("Error de conexión a la base de datos", 500);
    }

    // Configurar fechas según período
    $hoy = date('Y-m-d');
    $ayer = date('Y-m-d', strtotime('-1 day'));
    $inicio_semana = date('Y-m-d', strtotime('last monday'));
    $inicio_mes = date('Y-m-01');
    $inicio_anio = date('Y-01-01');
    $inicio_anterior = '';
    $fin_anterior = '';
    $periodo_texto = 'Hoy';

    // MODIFICACIÓN PRINCIPAL: Agregar condición de estado 2 o 3 a todos los filtros
    switch($periodo) {
        case 'hoy':
            $filtro = "oc.fecha = '$hoy' AND oc.idEstado IN (2, 3)";
            $inicio_anterior = $ayer;
            $fin_anterior = $ayer;
            $periodo_texto = 'Hoy';
            break;
        case 'semana':
            $filtro = "oc.fecha BETWEEN '$inicio_semana' AND '$hoy' AND oc.idEstado IN (2, 3)";
            $inicio_anterior = date('Y-m-d', strtotime('last monday -7 days'));
            $fin_anterior = date('Y-m-d', strtotime('last sunday -7 days'));
            $periodo_texto = 'Esta semana';
            break;
        case 'mes':
            $filtro = "oc.fecha BETWEEN '$inicio_mes' AND '$hoy' AND oc.idEstado IN (2, 3)";
            $inicio_anterior = date('Y-m-01', strtotime('-1 month'));
            $fin_anterior = date('Y-m-t', strtotime('-1 month'));
            $periodo_texto = 'Este mes';
            break;
        case 'anio':
            $filtro = "oc.fecha BETWEEN '$inicio_anio' AND '$hoy' AND oc.idEstado IN (2, 3)";
            $inicio_anterior = date('Y-01-01', strtotime('-1 year'));
            $fin_anterior = date('Y-12-31', strtotime('-1 year'));
            $periodo_texto = 'Este año';
            break;
        case 'personalizado':
            $filtro = "oc.fecha BETWEEN '$fechaInicio' AND '$fechaFin' AND oc.idEstado IN (2, 3)";
            $dias = (strtotime($fechaFin) - strtotime($fechaInicio)) / (60 * 60 * 24);
            $inicio_anterior = date('Y-m-d', strtotime($fechaInicio . " -" . ($dias + 1) . " days"));
            $fin_anterior = date('Y-m-d', strtotime($fechaInicio . " -1 day"));
            $periodo_texto = "Personalizado ($fechaInicio al $fechaFin)";
            break;
        default:
            $filtro = "oc.fecha = '$hoy' AND oc.idEstado IN (2, 3)";
            $periodo_texto = 'Hoy';
            break;
    }

    // Estructura para almacenar todos los datos
    $datos = [
        'comprasHoy' => [
            'total' => 0,
            'variacion' => 0,
            'periodo' => $periodo_texto
        ],
        'gastosCompras' => [
            'total' => 0,
            'variacion' => 0,
            'periodo' => $periodo_texto
        ],
        'articulosChart' => [
            'labels' => [],
            'series' => [],
            'periodo' => $periodo_texto
        ],
        'proveedoresChart' => [
            'labels' => [],
            'series' => [],
            'periodo' => $periodo_texto
        ],
        'evolucionChart' => [
            'labels' => [],
            'series' => [],
            'periodo' => $periodo_texto
        ],
        'frecuenciaChart' => [
            'labels' => [],
            'series' => [],
            'periodo' => $periodo_texto
        ]
    ];

    // 1. Consulta para contar compras actuales
    $query = "SELECT COUNT(*) as total FROM orden_compra oc WHERE $filtro";
    $result = $conexion->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $datos['comprasHoy']['total'] = (int)$row['total'];
    }

    // Consulta para contar compras período anterior (también con filtro de estado)
    if (!empty($inicio_anterior) && !empty($fin_anterior)) {
        $query_anterior = "SELECT COUNT(*) as total FROM orden_compra oc WHERE oc.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior' AND oc.idEstado IN (2, 3)";
        $result_anterior = $conexion->query($query_anterior);
        if ($result_anterior) {
            $row_anterior = $result_anterior->fetch_assoc();
            $total_anterior = (int)($row_anterior['total'] ?? 0);
            $variacion = $total_anterior > 0 ? round((($datos['comprasHoy']['total'] - $total_anterior) / $total_anterior) * 100, 2) : 
                     ($datos['comprasHoy']['total'] > 0 ? 100 : 0);
            $datos['comprasHoy']['variacion'] = (float)$variacion;
        }
    }

    // 2. Consulta para sumar gastos actuales
    $query = "SELECT COALESCE(SUM(doc.cantidad * doc.precio), 0) as total 
              FROM detalle_orden_compra doc
              JOIN orden_compra oc ON doc.idOrdenCompra = oc.idOrdenCompra
              WHERE $filtro";
    $result = $conexion->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $datos['gastosCompras']['total'] = (float)$row['total'];
    }

    // Consulta para sumar gastos período anterior (también con filtro de estado)
    if (!empty($inicio_anterior) && !empty($fin_anterior)) {
        $query_anterior = "SELECT COALESCE(SUM(doc.cantidad * doc.precio), 0) as total 
                           FROM detalle_orden_compra doc
                           JOIN orden_compra oc ON doc.idOrdenCompra = oc.idOrdenCompra
                           WHERE oc.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior' AND oc.idEstado IN (2, 3)";
        $result_anterior = $conexion->query($query_anterior);
        if ($result_anterior) {
            $row_anterior = $result_anterior->fetch_assoc();
            $total_anterior = (float)($row_anterior['total'] ?? 0);
            $variacion = $total_anterior > 0 ? round((($datos['gastosCompras']['total'] - $total_anterior) / $total_anterior) * 100, 2) : 
                     ($datos['gastosCompras']['total'] > 0 ? 100 : 0);
            $datos['gastosCompras']['variacion'] = (float)$variacion;
        }
    }

    // 3. Consulta para artículos más comprados
    $query = "SELECT 
                p.nombre as articulo,
                SUM(doc.cantidad) as cantidad
              FROM productos p
              JOIN detalle_orden_compra doc ON p.idProducto = doc.idArticulo
              JOIN orden_compra oc ON doc.idOrdenCompra = oc.idOrdenCompra
              WHERE $filtro
              GROUP BY p.idProducto
              HAVING cantidad > 0
              ORDER BY cantidad DESC
              LIMIT 10";
    
    $result = $conexion->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['articulosChart']['labels'][] = $row['articulo'];
            $datos['articulosChart']['series'][] = (int)$row['cantidad'];
        }
    }

    // 4. Consulta para compras por proveedor
    $query = "SELECT 
                pr.razon_social as proveedor,
                SUM(doc.cantidad * doc.precio) as monto_total
              FROM proveedores pr
              JOIN orden_compra oc ON pr.idProveedor = oc.idProveedor
              JOIN detalle_orden_compra doc ON oc.idOrdenCompra = doc.idOrdenCompra
              WHERE $filtro
              GROUP BY pr.idProveedor
              HAVING monto_total > 0
              ORDER BY monto_total DESC";
    
    $result = $conexion->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['proveedoresChart']['labels'][] = $row['proveedor'];
            $datos['proveedoresChart']['series'][] = (float)$row['monto_total'];
        }
    }

    // 5. Consulta para evolución de compras
    $query = "SELECT 
                oc.fecha,
                SUM(doc.cantidad) as cantidad_total
              FROM orden_compra oc
              JOIN detalle_orden_compra doc ON oc.idOrdenCompra = doc.idOrdenCompra
              WHERE $filtro
              GROUP BY oc.fecha
              ORDER BY oc.fecha";
    
    $result = $conexion->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['evolucionChart']['labels'][] = date('d/m', strtotime($row['fecha']));
            $datos['evolucionChart']['series'][] = (int)$row['cantidad_total'];
        }
    }

    // 6. Consulta para frecuencia de compras por proveedor
    $query = "SELECT 
                pr.razon_social as proveedor,
                COUNT(DISTINCT oc.idOrdenCompra) as frecuencia
              FROM proveedores pr
              JOIN orden_compra oc ON pr.idProveedor = oc.idProveedor
              WHERE $filtro
              GROUP BY pr.idProveedor
              HAVING frecuencia > 0
              ORDER BY frecuencia DESC";
    
    $result = $conexion->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['frecuenciaChart']['labels'][] = $row['proveedor'];
            $datos['frecuenciaChart']['series'][] = (int)$row['frecuencia'];
        }
    }

    // Cerrar conexión
    $conexion->close();

    // Verificar si hay datos antes de llamar a OpenAI
    $hayDatos = false;
    if ($datos['comprasHoy']['total'] > 0 || $datos['gastosCompras']['total'] > 0) {
        $hayDatos = true;
    } else {
        // Verificar en los charts si hay datos
        foreach (['articulosChart', 'proveedoresChart', 'evolucionChart', 'frecuenciaChart'] as $chart) {
            if (!empty($datos[$chart]['series']) && max($datos[$chart]['series']) > 0) {
                $hayDatos = true;
                break;
            }
        }
    }

    if (!$hayDatos) {
        // Respuesta directa sin llamar a OpenAI
        $recomendaciones = "¡Hola! Soy Hachi, tu asistente virtual. Revisando los datos, no se registraron compras en este período.\n\n";
        $recomendaciones .= "💡 Te sugiero:\n";
        $recomendaciones .= "1. Revisar si hay productos con poco stock que deban ser repuestos\n";
        $recomendaciones .= "2. Evaluar relaciones con proveedores para mejores condiciones\n";
        $recomendaciones .= "3. Analizar si hay productos que no se están vendiendo y requieren ajustes\n";
        $recomendaciones .= "4. Considerar compras programadas para optimizar costos";
    } else {
        // Llamar a OpenAI solo si hay datos
        $recomendaciones = obtenerRecomendacionesDeOpenAICompras($datos, $periodo);
        
        if (empty($recomendaciones)) {
            throw new Exception("No se recibieron recomendaciones, pero el proceso completó sin errores.");
        }
    }

    // Registrar datos enviados para diagnóstico
    error_log("Datos enviados a OpenAI: " . json_encode($datos));

    echo json_encode([
        'success' => true,
        'recomendaciones' => $recomendaciones,
        'periodo' => $periodo,
        'timestamp' => date('Y-m-d H:i:s'),
        'datos' => $hayDatos ? $datos : 'Sin datos relevantes'
    ]);

} catch (Exception $e) {
    error_log("Error en get_recomendaciones_compras: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    
    // Respuesta de error con datos de diagnóstico
    jsonError("Error al generar recomendaciones: " . $e->getMessage(), 500);
}