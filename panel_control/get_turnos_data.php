<?php
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
            $filtro = "Fecha = '$hoy'";
            $inicio_anterior = $ayer;
            $fin_anterior = $ayer;
            $periodo_texto = 'Hoy';
            break;
        case 'semana':
            $filtro = "Fecha BETWEEN '$inicio_semana' AND '$hoy'";
            $inicio_anterior = date('Y-m-d', strtotime('last monday -7 days'));
            $fin_anterior = date('Y-m-d', strtotime('last sunday -7 days'));
            $periodo_texto = 'Esta semana';
            break;
        case 'mes':
            $filtro = "Fecha BETWEEN '$inicio_mes' AND '$hoy'";
            $inicio_anterior = date('Y-m-01', strtotime('-1 month'));
            $fin_anterior = date('Y-m-t', strtotime('-1 month'));
            $periodo_texto = 'Este mes';
            break;
        case 'anio':
            $filtro = "Fecha BETWEEN '$inicio_anio' AND '$hoy'";
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
            
            $filtro = "Fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
            
            // Calcular período anterior equivalente
            $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
            $inicio_anterior = date('Y-m-d', strtotime($fecha_inicio . " -" . ($dias + 1) . " days"));
            $fin_anterior = date('Y-m-d', strtotime($fecha_inicio . " -1 day"));
            
            $periodo_texto = "Personalizado ($fecha_inicio al $fecha_fin)";
            break;
        default:
            $filtro = "Fecha = '$hoy'";
            $periodo_texto = 'Hoy';
            break;
    }

    // Inicializar respuesta
    $response = ['periodo' => $periodo_texto];

    switch($tipo) {
        case 'turnosHoy':
            // Consulta para contar turnos actuales
            $query = "SELECT COUNT(*) as total FROM turnos WHERE $filtro AND idActivo = 1";
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            $row = $result->fetch_assoc();
            $total_actual = (int)$row['total'];
            
            // Consulta para contar turnos período anterior
            $query_anterior = "SELECT COUNT(*) as total FROM turnos WHERE idActivo = 1 AND Fecha BETWEEN '$inicio_anterior' AND '$fin_anterior'";
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
            
        case 'ingresosTurnos':
            // Consulta para sumar ingresos actuales
            $query = "SELECT COALESCE(SUM(ts.precio), 0) as total 
                        FROM turnos t
                        JOIN detalle_turno dt ON t.IdTurno = dt.idTurno
                        JOIN tipo_servicio ts ON dt.idTipoServicio = ts.IdTipoServicio
                        WHERE $filtro 
                        AND t.IdEstado = 4
                        AND t.idActivo = 1
                        AND ts.idActivo = 1";
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            $row = $result->fetch_assoc();
            $total_actual = (float)$row['total'];
            
            // Consulta para sumar ingresos período anterior
            $query_anterior = "SELECT COALESCE(SUM(ts.precio), 0) as total 
                                FROM turnos t
                                JOIN detalle_turno dt ON t.IdTurno = dt.idTurno
                                JOIN tipo_servicio ts ON dt.idTipoServicio = ts.IdTipoServicio
                                WHERE t.Fecha BETWEEN '$inicio_anterior' AND '$fin_anterior'
                                AND t.IdEstado = 4
                                AND t.idActivo = 1";
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
            
        case 'estadoChart':
            // Obtener todos los estados posibles primero
            $estados_query = "SELECT IdEstado, Denominacion FROM estado ORDER BY IdEstado";
            $estados_result = $conexion->query($estados_query);
            
            if (!$estados_result) {
                throw new Exception("Error al obtener estados: " . $conexion->error, 500);
            }
            
            $estados = [];
            while ($estado = $estados_result->fetch_assoc()) {
                $estados[$estado['IdEstado']] = $estado['Denominacion'];
            }
            
            // Consulta para contar turnos por estado
            $query = "SELECT 
                        t.IdEstado,
                        COUNT(t.IdTurno) as cantidad
                      FROM turnos t
                      WHERE $filtro AND t.IdActivo = 1
                      GROUP BY t.IdEstado";
            
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            // Inicializar con ceros para todos los estados
            $datos = [];
            foreach ($estados as $id => $nombre) {
                $datos[$id] = [
                    'nombre' => $nombre,
                    'cantidad' => 0
                ];
            }
            
            // Llenar con datos reales
            while ($row = $result->fetch_assoc()) {
                if (isset($datos[$row['IdEstado']])) {
                    $datos[$row['IdEstado']]['cantidad'] = (int)$row['cantidad'];
                }
            }
            
            // Preparar respuesta
            $labels = [];
            $series = [];
            $colores = [
                1 => '#FF4560', // Pendiente
                2 => '#00E396', // En Proceso
                3 => '#FEB019', // Completado
                4 => '#008FFB', // Pagado
                5 => '#702b35ff', // Rechazado
                5 => '#775DD0'  // Vencido
            ];
            $colors = [];
            
            foreach ($datos as $id => $estado) {
                $labels[] = $estado['nombre'];
                $series[] = $estado['cantidad'];
                $colors[] = $colores[$id] ?? '#999999';
            }
            
            $response = [
                'labels' => $labels,
                'series' => $series,
                'colors' => $colors,
                'periodo' => $periodo_texto,
                'total' => array_sum($series)
            ];
            break;
            
        case 'estilistaChart':
            // Consulta para contar turnos por estilista
            $query = "SELECT 
                        e.id,
                        CONCAT(e.nombre, ' ', e.apellido) as estilista,
                        COUNT(t.IdTurno) as cantidad
                      FROM turnos t
                      JOIN usuarios e ON t.IdEstilista = e.id
                      WHERE $filtro AND t.IdActivo = 1
                      GROUP BY e.id, e.nombre, e.apellido
                      ORDER BY cantidad DESC";
            
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            $labels = [];
            $series = [];
            
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['estilista'];
                $series[] = (int)$row['cantidad'];
            }
            
            $response = [
                'labels' => $labels,
                'series' => $series,
                'periodo' => $periodo_texto
            ];
            break;
            
        case 'horarioChart':
            // Consulta para agrupar por franja horaria
            $query = "SELECT 
                        CASE 
                          WHEN TIME(Horario) BETWEEN '09:00:00' AND '11:59:59' THEN 'Mañana (9-12)'
                          WHEN TIME(Horario) BETWEEN '12:00:00' AND '14:59:59' THEN 'Mediodía (12-15)'
                          WHEN TIME(Horario) BETWEEN '15:00:00' AND '17:59:59' THEN 'Tarde (15-18)'
                          WHEN TIME(Horario) BETWEEN '18:00:00' AND '20:59:59' THEN 'Noche (18-21)'
                          ELSE 'Otro horario'
                        END as franja,
                        COUNT(IdTurno) as cantidad
                      FROM turnos
                      WHERE $filtro AND IdActivo = 1
                      GROUP BY franja
                      ORDER BY 
                        CASE franja
                          WHEN 'Mañana (9-12)' THEN 1
                          WHEN 'Mediodía (12-15)' THEN 2
                          WHEN 'Tarde (15-18)' THEN 3
                          WHEN 'Noche (18-21)' THEN 4
                          ELSE 5
                        END";
            
            $result = $conexion->query($query);
            if (!$result) {
                throw new Exception("Error en consulta: " . $conexion->error, 500);
            }
            
            $labels = [];
            $series = [];
            
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['franja'];
                $series[] = (int)$row['cantidad'];
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
    
    error_log("Error en get_turnos_data: " . $e->getMessage());
    
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'code' => $errorCode,
        'trace' => $e->getTraceAsString()
    ]);
}