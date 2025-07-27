<?php
// Configuración inicial
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/recommendations_ventas_errors.log');

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
require_once realpath(__DIR__ . '/..') . '/funciones/openai_helper_ventas.php';

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

    switch($periodo) {
        case 'hoy':
            $filtro = "v.fecha = '$hoy'";
            $inicio_anterior = $ayer;
            $fin_anterior = $ayer;
            $periodo_texto = 'Hoy';
            break;
        case 'semana':
            $filtro = "v.fecha BETWEEN '$inicio_semana' AND '$hoy'";
            $inicio_anterior = date('Y-m-d', strtotime('last monday -7 days'));
            $fin_anterior = date('Y-m-d', strtotime('last sunday -7 days'));
            $periodo_texto = 'Esta semana';
            break;
        case 'mes':
            $filtro = "v.fecha BETWEEN '$inicio_mes' AND '$hoy'";
            $inicio_anterior = date('Y-m-01', strtotime('-1 month'));
            $fin_anterior = date('Y-m-t', strtotime('-1 month'));
            $periodo_texto = 'Este mes';
            break;
        case 'anio':
            $filtro = "v.fecha BETWEEN '$inicio_anio' AND '$hoy'";
            $inicio_anterior = date('Y-01-01', strtotime('-1 year'));
            $fin_anterior = date('Y-12-31', strtotime('-1 year'));
            $periodo_texto = 'Este año';
            break;
        case 'personalizado':
            $filtro = "v.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
            $dias = (strtotime($fechaFin) - strtotime($fechaInicio)) / (60 * 60 * 24);
            $inicio_anterior = date('Y-m-d', strtotime($fechaInicio . " -" . ($dias + 1) . " days"));
            $fin_anterior = date('Y-m-d', strtotime($fechaInicio . " -1 day"));
            $periodo_texto = "Personalizado ($fechaInicio al $fechaFin)";
            break;
        default:
            $filtro = "v.fecha = '$hoy'";
            $periodo_texto = 'Hoy';
            break;
    }

    // Estructura para almacenar todos los datos
    $datos = [
        'ventasHoy' => [
            'total' => 0,
            'variacion' => 0,
            'periodo' => $periodo_texto
        ],
        'ingresosVentas' => [
            'total' => 0,
            'variacion' => 0,
            'periodo' => $periodo_texto
        ],
        'productosChart' => [
            'labels' => [],
            'series' => [],
            'periodo' => $periodo_texto
        ],
        'clientesChart' => [
            'labels' => [],
            'series' => [],
            'periodo' => $periodo_texto
        ],
        'empleadosChart' => [
            'labels' => [],
            'series' => [],
            'periodo' => $periodo_texto
        ],
        'diasChart' => [
            'labels' => [],
            'series' => [],
            'periodo' => $periodo_texto
        ]
    ];

    // 1. Consulta para contar ventas actuales
    $query = "SELECT COUNT(*) as total FROM ventas v WHERE $filtro";
    $result = $conexion->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $datos['ventasHoy']['total'] = (int)$row['total'];
    }

    // Consulta para contar ventas período anterior
    if (!empty($inicio_anterior) && !empty($fin_anterior)) {
        $query_anterior = "SELECT COUNT(*) as total FROM ventas v WHERE v.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior'";
        $result_anterior = $conexion->query($query_anterior);
        if ($result_anterior) {
            $row_anterior = $result_anterior->fetch_assoc();
            $total_anterior = (int)($row_anterior['total'] ?? 0);
            $variacion = $total_anterior > 0 ? round((($datos['ventasHoy']['total'] - $total_anterior) / $total_anterior) * 100, 2) : 
                     ($datos['ventasHoy']['total'] > 0 ? 100 : 0);
            $datos['ventasHoy']['variacion'] = (float)$variacion;
        }
    }

    // 2. Consulta para sumar ingresos actuales (ventas + pedidos)
    $query = "SELECT 
                COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) + 
                COALESCE(SUM(p.senia), 0) as total 
              FROM ventas v
              LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
              LEFT JOIN pedidos p ON v.idCliente = p.idCliente AND p.fecha = v.fecha
              WHERE $filtro";
    $result = $conexion->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $datos['ingresosVentas']['total'] = (float)$row['total'];
    }

    // Consulta para sumar ingresos período anterior
    if (!empty($inicio_anterior) && !empty($fin_anterior)) {
        $query_anterior = "SELECT 
                            COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) + 
                            COALESCE(SUM(p.senia), 0) as total 
                          FROM ventas v
                          LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
                          LEFT JOIN pedidos p ON v.idCliente = p.idCliente AND p.fecha = v.fecha
                          WHERE v.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior'";
        $result_anterior = $conexion->query($query_anterior);
        if ($result_anterior) {
            $row_anterior = $result_anterior->fetch_assoc();
            $total_anterior = (float)($row_anterior['total'] ?? 0);
            $variacion = $total_anterior > 0 ? round((($datos['ingresosVentas']['total'] - $total_anterior) / $total_anterior) * 100, 2) : 
                     ($datos['ingresosVentas']['total'] > 0 ? 100 : 0);
            $datos['ingresosVentas']['variacion'] = (float)$variacion;
        }
    }

    // 3. Consulta para productos más vendidos
    $query = "SELECT 
                p.nombre as producto,
                COALESCE(SUM(dv.cantidad), 0) + COALESCE(SUM(dp.cantidad), 0) as cantidad
              FROM productos p
              LEFT JOIN detalle_venta dv ON p.idProducto = dv.idProducto
              LEFT JOIN ventas v ON dv.idVenta = v.idVenta AND $filtro
              LEFT JOIN detalle_pedido dp ON p.idProducto = dp.idProducto
              LEFT JOIN pedidos pd ON dp.idPedido = pd.idPedido AND pd.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
              GROUP BY p.idProducto
              HAVING cantidad > 0
              ORDER BY cantidad DESC
              LIMIT 10";
    
    $result = $conexion->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['productosChart']['labels'][] = $row['producto'];
            $datos['productosChart']['series'][] = (int)$row['cantidad'];
        }
    }

    // 4. Consulta para clientes destacados
    $query = "SELECT 
                CONCAT(c.nombre, ' ', c.apellido) as cliente,
                COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) + 
                COALESCE(SUM(p.senia), 0) as monto_gastado
              FROM clientes c
              LEFT JOIN ventas v ON c.idCliente = v.idCliente AND $filtro
              LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
              LEFT JOIN pedidos p ON c.idCliente = p.idCliente AND p.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
              GROUP BY c.idCliente
              HAVING monto_gastado > 0
              ORDER BY monto_gastado DESC
              LIMIT 10";
    
    $result = $conexion->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['clientesChart']['labels'][] = $row['cliente'];
            $datos['clientesChart']['series'][] = (float)$row['monto_gastado'];
        }
    }

    // 5. Consulta para rendimiento por empleado
    $query = "SELECT 
                CONCAT(u.nombre, ' ', u.apellido) as empleado,
                COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) as monto_generado
              FROM usuarios u
              LEFT JOIN ventas v ON u.id = v.idUsuario AND $filtro
              LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
              GROUP BY u.id
              HAVING monto_generado > 0
              ORDER BY monto_generado DESC";
    
    $result = $conexion->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['empleadosChart']['labels'][] = $row['empleado'];
            $datos['empleadosChart']['series'][] = (float)$row['monto_generado'];
        }
    }

    // 6. Consulta para ventas por día
    $query = "SELECT 
                v.fecha,
                COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) as monto_total
              FROM ventas v
              LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
              WHERE $filtro
              GROUP BY v.fecha
              ORDER BY v.fecha";
    
    $result = $conexion->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $datos['diasChart']['labels'][] = date('d/m', strtotime($row['fecha']));
            $datos['diasChart']['series'][] = (float)$row['monto_total'];
        }
    }

    // Cerrar conexión
    $conexion->close();

    // Verificar si hay datos antes de llamar a OpenAI
    $hayDatos = false;
    if ($datos['ventasHoy']['total'] > 0 || $datos['ingresosVentas']['total'] > 0) {
        $hayDatos = true;
    } else {
        // Verificar en los charts si hay datos
        foreach (['productosChart', 'clientesChart', 'empleadosChart', 'diasChart'] as $chart) {
            if (!empty($datos[$chart]['series']) && max($datos[$chart]['series']) > 0) {
                $hayDatos = true;
                break;
            }
        }
    }

    if (!$hayDatos) {
        // Respuesta directa sin llamar a OpenAI
        $recomendaciones = "¡Hola! Soy Hachi, tu asistente virtual. Revisando los datos, no se registraron ventas en este período.\n\n";
        $recomendaciones .= "💡 Te sugiero:\n";
        $recomendaciones .= "1. Revisar si hay productos con poco stock que puedan estar afectando las ventas\n";
        $recomendaciones .= "2. Crear promociones especiales para incentivar las compras\n";
        $recomendaciones .= "3. Verificar que los precios sean competitivos\n";
        $recomendaciones .= "4. Capacitar al equipo en técnicas de venta adicionales";
    } else {
        // Llamar a OpenAI solo si hay datos
        $recomendaciones = obtenerRecomendacionesDeOpenAIVentas($datos, $periodo);
        
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
    error_log("Error en get_recomendaciones_ventas: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    
    // Respuesta de error con datos de diagnóstico
    jsonError("Error al generar recomendaciones: " . $e->getMessage(), 500);
}
?>