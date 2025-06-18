<?php
session_start();

// Verificar sesión y permisos
if (empty($_SESSION['Usuario_Nombre'])) {
    die(json_encode(['error' => 'Acceso no autorizado']));
}

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Forzar tipo de contenido JSON
header('Content-Type: application/json');

// Incluir conexión a la base de datos
require_once __DIR__ . '/../funciones/conexion.php';

try {
    // Verificar método de solicitud
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("Método no permitido", 405);
    }

    // Obtener y validar parámetros
    $tipo = $_GET['tipo'] ?? null;
    $periodo = $_GET['periodo'] ?? 'hoy';
    $fecha_inicio = $_GET['fecha_inicio'] ?? null;
    $fecha_fin = $_GET['fecha_fin'] ?? null;

    if (!$tipo) {
        throw new Exception("Parámetro 'tipo' requerido", 400);
    }

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
            if (!$fecha_inicio || !$fecha_fin) {
                throw new Exception("Fechas personalizadas requeridas", 400);
            }
            
            // Validar formato de fechas
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || 
                !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
                throw new Exception("Formato de fecha inválido. Use YYYY-MM-DD", 400);
            }
            
            // Validar que fecha inicio <= fecha fin
            if (strtotime($fecha_inicio) > strtotime($fecha_fin)) {
                throw new Exception("La fecha de inicio no puede ser mayor a la fecha fin", 400);
            }
            
            $filtro = "v.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            
            // Calcular período anterior equivalente
            $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
            $inicio_anterior = date('Y-m-d', strtotime($fecha_inicio . " -" . ($dias + 1) . " days"));
            $fin_anterior = date('Y-m-d', strtotime($fecha_inicio . " -1 day"));
            
            $periodo_texto = "Personalizado ($fecha_inicio al $fecha_fin)";
            break;
        default:
            $filtro = "v.fecha = '$hoy'";
            $periodo_texto = 'Hoy';
            break;
    }

    // Inicializar respuesta
    $response = ['periodo' => $periodo_texto];

    switch($tipo) {
        case 'ventasHoy':
            // Consulta para contar ventas actuales
            $query = "SELECT COUNT(*) as total FROM ventas v WHERE $filtro";
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            $row = $result->fetch_assoc();
            $total_actual = (int)$row['total'];
            
            // Consulta para contar ventas período anterior
            $query_anterior = "SELECT COUNT(*) as total FROM ventas v WHERE v.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior'";
            $result_anterior = $conexion->query($query_anterior);
            if (!$result_anterior) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            $row_anterior = $result_anterior->fetch_assoc();
            $total_anterior = (int)($row_anterior['total'] ?? 0);
            
            // Calcular variación (protegido contra división por cero)
            $variacion = $total_anterior > 0 ? round((($total_actual - $total_anterior) / $total_anterior) * 100, 2) : ($total_actual > 0 ? 100 : 0);
            
            $response = [
                'total' => $total_actual,
                'variacion' => (float)$variacion,
                'periodo' => $periodo_texto
            ];
            break;
            
        case 'ingresosVentas':
            // Consulta para sumar ingresos actuales (ventas + pedidos)
            $query = "SELECT 
                        COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) + 
                        COALESCE(SUM(p.senia), 0) as total 
                      FROM ventas v
                      LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
                      LEFT JOIN pedidos p ON v.idCliente = p.idCliente AND p.fecha = v.fecha
                      WHERE $filtro";
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            $row = $result->fetch_assoc();
            $total_actual = (float)$row['total'];
            
            // Consulta para sumar ingresos período anterior
            $query_anterior = "SELECT 
                                COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) + 
                                COALESCE(SUM(p.senia), 0) as total 
                              FROM ventas v
                              LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
                              LEFT JOIN pedidos p ON v.idCliente = p.idCliente AND p.fecha = v.fecha
                              WHERE v.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior'";
            $result_anterior = $conexion->query($query_anterior);
            if (!$result_anterior) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            $row_anterior = $result_anterior->fetch_assoc();
            $total_anterior = (float)($row_anterior['total'] ?? 0);
            
            // Calcular variación (protegido contra división por cero)
            $variacion = $total_anterior > 0 ? round((($total_actual - $total_anterior) / $total_anterior) * 100, 2) : ($total_actual > 0 ? 100 : 0);
            
            $response = [
                'total' => $total_actual,
                'variacion' => (float)$variacion,
                'periodo' => $periodo_texto
            ];
            break;
            
        case 'productosChart':
            // Consulta para contar productos más vendidos (ventas + pedidos)
            $query = "SELECT 
                        p.nombre as producto,
                        COALESCE(SUM(dv.cantidad), 0) + COALESCE(SUM(dp.cantidad), 0) as cantidad
                      FROM productos p
                      LEFT JOIN detalle_venta dv ON p.idProducto = dv.idProducto
                      LEFT JOIN ventas v ON dv.idVenta = v.idVenta AND $filtro
                      LEFT JOIN detalle_pedido dp ON p.idProducto = dp.idProducto
                      LEFT JOIN pedidos pd ON dp.idPedido = pd.idPedido AND pd.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                      GROUP BY p.idProducto
                      HAVING cantidad > 0
                      ORDER BY cantidad DESC
                      LIMIT 10";
            
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            $labels = [];
            $series = [];
            
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['producto'];
                $series[] = (int)$row['cantidad'];
            }
            
            $response = [
                'labels' => $labels,
                'series' => $series,
                'periodo' => $periodo_texto
            ];
            break;
            
        case 'clientesChart':
            // Consulta para clientes destacados por monto gastado
            $query = "SELECT 
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
            
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            $labels = [];
            $series = [];
            
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['cliente'];
                $series[] = (float)$row['monto_gastado'];
            }
            
            $response = [
                'labels' => $labels,
                'series' => $series,
                'periodo' => $periodo_texto
            ];
            break;
            
        case 'empleadosChart':
            // Consulta para rendimiento por empleado (ventas generadas)
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
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            $labels = [];
            $series = [];
            
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['empleado'];
                $series[] = (float)$row['monto_generado'];
            }
            
            $response = [
                'labels' => $labels,
                'series' => $series,
                'periodo' => $periodo_texto
            ];
            break;
            
        case 'diasChart':
            // Consulta para ventas por día
            $query = "SELECT 
                        v.fecha,
                        COALESCE(SUM(dv.precio_venta * dv.cantidad), 0) as monto_total
                      FROM ventas v
                      LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
                      WHERE $filtro
                      GROUP BY v.fecha
                      ORDER BY v.fecha";
            
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            $labels = [];
            $series = [];
            
            while ($row = $result->fetch_assoc()) {
                $labels[] = date('d/m', strtotime($row['fecha']));
                $series[] = (float)$row['monto_total'];
            }
            
            $response = [
                'labels' => $labels,
                'series' => $series,
                'periodo' => $periodo_texto
            ];
            break;
            
        default:
            throw new Exception("Tipo de solicitud no válido", 400);
    }

    // Cerrar conexión
    $conexion->close();

    // Enviar respuesta
    echo json_encode($response);

} catch (Exception $e) {
    // Manejo de errores
    $errorCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    http_response_code($errorCode);
    
    error_log("Error en get_ventas_data: " . $e->getMessage());
    
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'code' => $errorCode,
        'trace' => $e->getTraceAsString()
    ]);
}