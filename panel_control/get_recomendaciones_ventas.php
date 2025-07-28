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
            $filtro_ventas = "v.fecha = '$hoy' AND v.idEstado = 2";
            $filtro_pedidos = "p.fecha = '$hoy' AND p.idEstado = 3";
            $inicio_anterior = $ayer;
            $fin_anterior = $ayer;
            $periodo_texto = 'Hoy';
            $fechaInicio = $hoy;
            $fechaFin = $hoy;
            break;
        case 'semana':
            $filtro_ventas = "v.fecha BETWEEN '$inicio_semana' AND '$hoy' AND v.idEstado = 2";
            $filtro_pedidos = "p.fecha BETWEEN '$inicio_semana' AND '$hoy' AND p.idEstado = 3";
            $inicio_anterior = date('Y-m-d', strtotime('last monday -7 days'));
            $fin_anterior = date('Y-m-d', strtotime('last sunday -7 days'));
            $periodo_texto = 'Esta semana';
            $fechaInicio = $inicio_semana;
            $fechaFin = $hoy;
            break;
        case 'mes':
            $filtro_ventas = "v.fecha BETWEEN '$inicio_mes' AND '$hoy' AND v.idEstado = 2";
            $filtro_pedidos = "p.fecha BETWEEN '$inicio_mes' AND '$hoy' AND p.idEstado = 3";
            $inicio_anterior = date('Y-m-01', strtotime('-1 month'));
            $fin_anterior = date('Y-m-t', strtotime('-1 month'));
            $periodo_texto = 'Este mes';
            $fechaInicio = $inicio_mes;
            $fechaFin = $hoy;
            break;
        case 'anio':
            $filtro_ventas = "v.fecha BETWEEN '$inicio_anio' AND '$hoy' AND v.idEstado = 2";
            $filtro_pedidos = "p.fecha BETWEEN '$inicio_anio' AND '$hoy' AND p.idEstado = 3";
            $inicio_anterior = date('Y-01-01', strtotime('-1 year'));
            $fin_anterior = date('Y-12-31', strtotime('-1 year'));
            $periodo_texto = 'Este año';
            $fechaInicio = $inicio_anio;
            $fechaFin = $hoy;
            break;
        case 'personalizado':
            $filtro_ventas = "v.fecha BETWEEN '$fechaInicio' AND '$fechaFin' AND v.idEstado = 2";
            $filtro_pedidos = "p.fecha BETWEEN '$fechaInicio' AND '$fechaFin' AND p.idEstado = 3";
            $dias = (strtotime($fechaFin) - strtotime($fechaInicio)) / (60 * 60 * 24);
            $inicio_anterior = date('Y-m-d', strtotime($fechaInicio . " -" . ($dias + 1) . " days"));
            $fin_anterior = date('Y-m-d', strtotime($fechaInicio . " -1 day"));
            $periodo_texto = "Personalizado ($fechaInicio al $fechaFin)";
            break;
        default:
            $filtro_ventas = "v.fecha = '$hoy' AND v.idEstado = 2";
            $filtro_pedidos = "p.fecha = '$hoy' AND p.idEstado = 3";
            $periodo_texto = 'Hoy';
            $fechaInicio = $hoy;
            $fechaFin = $hoy;
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

    // 1. Consulta para contar ventas y pedidos actuales
    $query = "SELECT 
                (SELECT COUNT(*) FROM ventas v WHERE $filtro_ventas) +
                (SELECT COUNT(*) FROM pedidos p WHERE $filtro_pedidos) as total";
    $result = $conexion->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $datos['ventasHoy']['total'] = (int)$row['total'];
    }

    // Consulta para contar ventas y pedidos período anterior
    if (!empty($inicio_anterior) && !empty($fin_anterior)) {
        $query_anterior = "SELECT 
                            (SELECT COUNT(*) FROM ventas v WHERE v.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior' AND v.idEstado = 2) +
                            (SELECT COUNT(*) FROM pedidos p WHERE p.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior' AND p.idEstado = 3) as total";
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
                (SELECT COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) 
                 FROM ventas v 
                 JOIN detalle_venta dv ON v.idVenta = dv.idVenta 
                 WHERE $filtro_ventas) +
                (SELECT COALESCE(SUM(dp.precio_venta * dp.cantidad), 0) 
                 FROM pedidos p 
                 JOIN detalle_pedido dp ON p.idPedido = dp.idPedido 
                 WHERE $filtro_pedidos) as total";
    $result = $conexion->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $datos['ingresosVentas']['total'] = (float)$row['total'];
    }

    // Consulta para sumar ingresos período anterior
    if (!empty($inicio_anterior) && !empty($fin_anterior)) {
        $query_anterior = "SELECT 
                            (SELECT COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) 
                             FROM ventas v 
                             JOIN detalle_venta dv ON v.idVenta = dv.idVenta 
                             WHERE v.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior' AND v.idEstado = 2) +
                            (SELECT COALESCE(SUM(dp.precio_venta * dp.cantidad), 0) 
                             FROM pedidos p 
                             JOIN detalle_pedido dp ON p.idPedido = dp.idPedido 
                             WHERE p.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior' AND p.idEstado = 3) as total";
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
        COALESCE(v.cantidad, 0) + COALESCE(pd.cantidad, 0) as cantidad
    FROM productos p
    LEFT JOIN (
        SELECT dv.idProducto, SUM(dv.cantidad) as cantidad
        FROM detalle_venta dv
        JOIN ventas v ON dv.idVenta = v.idVenta
        WHERE $filtro_ventas
        GROUP BY dv.idProducto
    ) v ON p.idProducto = v.idProducto
    LEFT JOIN (
        SELECT dp.idProducto, SUM(dp.cantidad) as cantidad
        FROM detalle_pedido dp
        JOIN pedidos p ON dp.idPedido = p.idPedido
        WHERE $filtro_pedidos
        GROUP BY dp.idProducto
    ) pd ON p.idProducto = pd.idProducto
    WHERE COALESCE(v.cantidad, 0) + COALESCE(pd.cantidad, 0) > 0
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
        COALESCE(SUM(dp.precio_venta * dp.cantidad), 0) as monto_gastado
      FROM clientes c
      LEFT JOIN ventas v ON c.idCliente = v.idCliente AND $filtro_ventas
      LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
      LEFT JOIN pedidos p ON c.idCliente = p.idCliente AND $filtro_pedidos
      LEFT JOIN detalle_pedido dp ON p.idPedido = dp.idPedido
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
        COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) + 
        COALESCE(SUM(dp.precio_venta * dp.cantidad), 0) as monto_generado
      FROM usuarios u
      LEFT JOIN ventas v ON u.id = v.idUsuario AND $filtro_ventas
      LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
      LEFT JOIN pedidos p ON u.id = p.idUsuario AND $filtro_pedidos
      LEFT JOIN detalle_pedido dp ON p.idPedido = dp.idPedido
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
        fecha,
        SUM(monto) as monto_total
      FROM (
          SELECT 
              v.fecha,
              SUM(dv.precio_venta * dv.cantidad) as monto
          FROM ventas v
          JOIN detalle_venta dv ON v.idVenta = dv.idVenta
          WHERE $filtro_ventas
          GROUP BY v.fecha
          
          UNION ALL
          
          SELECT 
              p.fecha,
              SUM(dp.precio_venta * dp.cantidad) as monto
          FROM pedidos p
          JOIN detalle_pedido dp ON p.idPedido = dp.idPedido
          WHERE $filtro_pedidos
          GROUP BY p.fecha
      ) as combined
      GROUP BY fecha
      ORDER BY fecha";
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