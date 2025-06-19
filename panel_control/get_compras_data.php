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
            $filtro = "oc.fecha = '$hoy'";
            $inicio_anterior = $ayer;
            $fin_anterior = $ayer;
            $periodo_texto = 'Hoy';
            break;
        case 'semana':
            $filtro = "oc.fecha BETWEEN '$inicio_semana' AND '$hoy'";
            $inicio_anterior = date('Y-m-d', strtotime('last monday -7 days'));
            $fin_anterior = date('Y-m-d', strtotime('last sunday -7 days'));
            $periodo_texto = 'Esta semana';
            break;
        case 'mes':
            $filtro = "oc.fecha BETWEEN '$inicio_mes' AND '$hoy'";
            $inicio_anterior = date('Y-m-01', strtotime('-1 month'));
            $fin_anterior = date('Y-m-t', strtotime('-1 month'));
            $periodo_texto = 'Este mes';
            break;
        case 'anio':
            $filtro = "oc.fecha BETWEEN '$inicio_anio' AND '$hoy'";
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
            
            $filtro = "oc.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            
            // Calcular período anterior equivalente
            $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
            $inicio_anterior = date('Y-m-d', strtotime($fecha_inicio . " -" . ($dias + 1) . " days"));
            $fin_anterior = date('Y-m-d', strtotime($fecha_inicio . " -1 day"));
            
            $periodo_texto = "Personalizado ($fecha_inicio al $fecha_fin)";
            break;
        default:
            $filtro = "oc.fecha = '$hoy'";
            $periodo_texto = 'Hoy';
            break;
    }

    // Inicializar respuesta
    $response = ['periodo' => $periodo_texto];

    switch($tipo) {
        case 'comprasHoy':
            // Consulta para contar compras actuales
            $query = "SELECT COUNT(*) as total FROM orden_compra oc WHERE $filtro";
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            $row = $result->fetch_assoc();
            $total_actual = (int)$row['total'];
            
            // Consulta para contar compras período anterior
            $query_anterior = "SELECT COUNT(*) as total FROM orden_compra oc WHERE oc.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior'";
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
            
        case 'gastosCompras':
            // Consulta para sumar gastos actuales
            $query = "SELECT COALESCE(SUM(doc.cantidad * doc.precio), 0) as total 
                      FROM detalle_orden_compra doc
                      JOIN orden_compra oc ON doc.idOrdenCompra = oc.idOrdenCompra
                      WHERE $filtro";
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            $row = $result->fetch_assoc();
            $total_actual = (float)$row['total'];
            
            // Consulta para sumar gastos período anterior
            $query_anterior = "SELECT COALESCE(SUM(doc.cantidad * doc.precio), 0) as total 
                               FROM detalle_orden_compra doc
                               JOIN orden_compra oc ON doc.idOrdenCompra = oc.idOrdenCompra
                               WHERE oc.fecha BETWEEN '$inicio_anterior' AND '$fin_anterior'";
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
            
        case 'articulosChart':
            // Consulta para artículos más comprados (top 10)
            $query = "SELECT 
                        p.nombre as articulo,
                        SUM(doc.cantidad) as cantidad
                      FROM productos p
                      JOIN detalle_orden_compra doc ON p.idProducto = doc.idArticulo
                      JOIN orden_compra oc ON doc.idOrdenCompra = oc.idOrdenCompra
                      WHERE $filtro
                      GROUP BY p.idProducto
                      ORDER BY cantidad DESC
                      LIMIT 10";
            
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            $labels = [];
            $series = [];
            
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['articulo'];
                $series[] = (int)$row['cantidad'];
            }
            
            $response = [
                'labels' => $labels,
                'series' => $series,
                'periodo' => $periodo_texto
            ];
            break;
            
        case 'proveedoresChart':
            // Consulta para compras por proveedor (monto total)
            $query = "SELECT 
                        pr.razon_social as proveedor,
                        SUM(doc.cantidad * doc.precio) as monto_total
                      FROM proveedores pr
                      JOIN orden_compra oc ON pr.idProveedor = oc.idProveedor
                      JOIN detalle_orden_compra doc ON oc.idOrdenCompra = doc.idOrdenCompra
                      WHERE $filtro
                      GROUP BY pr.idProveedor
                      ORDER BY monto_total DESC";
            
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            $labels = [];
            $series = [];
            
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['proveedor'];
                $series[] = (float)$row['monto_total'];
            }
            
            $response = [
                'labels' => $labels,
                'series' => $series,
                'periodo' => $periodo_texto
            ];
            break;
            
        case 'evolucionChart':
            // Consulta para evolución de compras por día/mes
            $query = "SELECT 
                        oc.fecha,
                        SUM(doc.cantidad) as cantidad_total
                      FROM orden_compra oc
                      JOIN detalle_orden_compra doc ON oc.idOrdenCompra = doc.idOrdenCompra
                      WHERE $filtro
                      GROUP BY oc.fecha
                      ORDER BY oc.fecha";
            
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            $labels = [];
            $series = [];
            
            while ($row = $result->fetch_assoc()) {
                $labels[] = date('d/m', strtotime($row['fecha']));
                $series[] = (int)$row['cantidad_total'];
            }
            
            $response = [
                'labels' => $labels,
                'series' => $series,
                'periodo' => $periodo_texto
            ];
            break;
            
        case 'frecuenciaChart':
            // Consulta para frecuencia de compras por proveedor
            $query = "SELECT 
                        pr.razon_social as proveedor,
                        COUNT(DISTINCT oc.idOrdenCompra) as frecuencia
                      FROM proveedores pr
                      JOIN orden_compra oc ON pr.idProveedor = oc.idProveedor
                      WHERE $filtro
                      GROUP BY pr.idProveedor
                      ORDER BY frecuencia DESC";
            
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            $labels = [];
            $series = [];
            
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['proveedor'];
                $series[] = (int)$row['frecuencia'];
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
    
    error_log("Error en get_compras_data: " . $e->getMessage());
    
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'code' => $errorCode,
        'trace' => $e->getTraceAsString()
    ]);
}